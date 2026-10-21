import { createAdminClient } from "@/lib/supabase/admin";
import { localNow } from "@/lib/time";

export type CheckResult = { ok: true; message: string } | { ok: false; error: string };

/**
 * Teacher self attendance. Runs with the service role so the time always comes
 * from the server (teachers cannot back-date or edit their own rows).
 */
export async function teacherCheck(userId: string, action: "check_in" | "check_out"): Promise<CheckResult> {
  const admin = createAdminClient();

  const { data: profile } = await admin.from("profiles").select("status, role").eq("id", userId).single();
  if (!profile || profile.status !== "active" || profile.role !== "teacher")
    return { ok: false, error: "This account is not an active teacher account." };

  const { data: teacher } = await admin.from("teachers").select("id, school_id, status").eq("user_id", userId).single();
  if (!teacher || teacher.status !== "active") return { ok: false, error: "Teacher record is not active." };

  const { data: school } = await admin.from("schools").select("timezone, late_after").eq("id", teacher.school_id).single();
  const { date, time } = localNow(school?.timezone ?? "Asia/Karachi");
  const lateAfter = String(school?.late_after ?? "08:30").slice(0, 5);

  const { data: existing } = await admin.from("teacher_attendance").select("*")
    .eq("teacher_id", teacher.id).eq("date", date).maybeSingle();

  if (action === "check_in") {
    if (existing?.check_in) return { ok: false, error: "You have already checked in today." };
    if (existing && (existing.status === "leave" || existing.status === "absent"))
      return { ok: false, error: `Today is marked as ${existing.status} by the school admin.` };
    const status = time > lateAfter ? "late" : "present";
    const { error } = await admin.from("teacher_attendance").upsert(
      { school_id: teacher.school_id, teacher_id: teacher.id, date, check_in: new Date().toISOString(), status },
      { onConflict: "teacher_id,date" },
    );
    if (error) return { ok: false, error: error.message };
    return { ok: true, message: status === "late" ? `Checked in at ${time} (late).` : `Checked in at ${time}.` };
  }

  if (!existing?.check_in) return { ok: false, error: "Check in first." };
  if (existing.check_out) return { ok: false, error: "You have already checked out today." };
  const { error } = await admin.from("teacher_attendance")
    .update({ check_out: new Date().toISOString() }).eq("id", existing.id);
  if (error) return { ok: false, error: error.message };
  return { ok: true, message: `Checked out at ${time}.` };
}
