import type { SupabaseClient } from "@supabase/supabase-js";
import { localNow } from "@/lib/time";

/**
 * Builds (or refreshes) payslips for a month, from teacher attendance.
 *  - Working days = every day of the month except Sundays.
 *  - Present / late days are paid; approved "leave" days are paid.
 *  - Every other elapsed working day (no attendance, or marked absent) is deducted
 *    at salary / working days.
 * Payslips already marked paid are left untouched.
 */
export async function generatePayslips(
  admin: SupabaseClient, schoolId: string, month: string, onlyTeacherId?: string,
): Promise<number> {
  const { data: school } = await admin.from("schools").select("timezone").eq("id", schoolId).single();
  const today = localNow(school?.timezone ?? "Asia/Karachi").date;

  const [y, m] = month.split("-").map(Number);
  const daysInMonth = new Date(Date.UTC(y, m, 0)).getUTCDate();
  let workingDays = 0;
  let elapsedWorkingDays = 0;
  for (let d = 1; d <= daysInMonth; d++) {
    if (new Date(Date.UTC(y, m - 1, d)).getUTCDay() === 0) continue; // Sunday
    workingDays++;
    const iso = `${month}-${String(d).padStart(2, "0")}`;
    if (iso <= today) elapsedWorkingDays++;
  }
  if (elapsedWorkingDays === 0) return 0; // future month

  let tq = admin.from("teachers").select("id, salary, joining_date, created_at").eq("school_id", schoolId).eq("status", "active");
  if (onlyTeacherId) tq = tq.eq("id", onlyTeacherId);
  const { data: teachers } = await tq;
  if (!teachers?.length) return 0;

  const { data: att } = await admin.from("teacher_attendance").select("teacher_id, status, date")
    .eq("school_id", schoolId).gte("date", `${month}-01`).lte("date", `${month}-${String(daysInMonth).padStart(2, "0")}`);
  const { data: existing } = await admin.from("payslips").select("teacher_id, status, bonus")
    .eq("school_id", schoolId).eq("month", month);
  const existingBy = new Map((existing ?? []).map((e) => [e.teacher_id, e]));

  let count = 0;
  for (const t of teachers) {
    // Nothing to pay for months before the teacher joined.
    const joined = String(t.joining_date ?? t.created_at ?? "").slice(0, 10);
    if (joined && joined > `${month}-${String(daysInMonth).padStart(2, "0")}`) continue;
    const prev = existingBy.get(t.id);
    if (prev?.status === "paid") continue;

    const mine = (att ?? []).filter((a) => a.teacher_id === t.id && new Date(a.date + "T00:00:00Z").getUTCDay() !== 0);
    const present = mine.filter((a) => a.status === "present").length;
    const late = mine.filter((a) => a.status === "late").length;
    const leave = mine.filter((a) => a.status === "leave").length;
    const absent = Math.max(0, elapsedWorkingDays - present - late - leave);

    const salary = Number(t.salary ?? 0);
    const perDay = workingDays ? salary / workingDays : 0;
    const deduction = Math.round(absent * perDay * 100) / 100;
    const bonus = Number(prev?.bonus ?? 0);
    const net = Math.round((salary - deduction + bonus) * 100) / 100;

    const { error } = await admin.from("payslips").upsert({
      school_id: schoolId, teacher_id: t.id, month, basic_salary: salary,
      working_days: workingDays, present_days: present, late_days: late, leave_days: leave,
      absent_days: absent, deduction, bonus, net_salary: net, generated_at: new Date().toISOString(),
    }, { onConflict: "teacher_id,month" });
    if (!error) count++;
  }
  return count;
}
