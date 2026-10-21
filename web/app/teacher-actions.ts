"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import { createClient } from "@/lib/supabase/server";
import { createAdminClient } from "@/lib/supabase/admin";
import { createLogin, isSchoolAdmin } from "@/lib/accounts";
import { teacherCheck } from "@/lib/teacher";
import { generatePayslips } from "@/lib/payslip";

export type FormState = { error?: string } | undefined;
export type CheckState = { ok?: boolean; message?: string; error?: string } | undefined;

const slugify = (s: string) =>
  s.toLowerCase().trim().replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "");

/** Public: a teacher requests an account; the school admin must approve it. */
export async function teacherSignUp(_: FormState, formData: FormData): Promise<FormState> {
  const code = slugify(String(formData.get("school_code") || ""));
  const name = String(formData.get("name") || "").trim();
  const email = String(formData.get("email") || "").trim();
  const phone = String(formData.get("phone") || "").trim();
  const password = String(formData.get("password") || "");
  if (!code || !name || !email) return { error: "All fields are required." };
  if (password.length < 6) return { error: "Password must be at least 6 characters." };

  const admin = createAdminClient();
  const { data: school } = await admin.from("schools").select("id, status").eq("slug", code).maybeSingle();
  if (!school || school.status !== "active") return { error: "School code not found. Ask your school for its code." };

  const created = await createLogin(school.id, name, email, password, "teacher", "pending");
  if ("error" in created) return created;
  const { error } = await admin.from("teachers").insert({
    school_id: school.id, name, email, phone: phone || null, status: "pending", user_id: created.userId,
  });
  if (error) {
    await admin.auth.admin.deleteUser(created.userId);
    return { error: error.message };
  }
  redirect("/login?signup=pending");
}

export async function approveTeacher(slug: string, teacherId: string, approve: boolean) {
  const supabase = await createClient();
  const { data: school } = await supabase.from("schools").select("id").eq("slug", slug).single();
  if (!school || !(await isSchoolAdmin(supabase, school.id))) return;
  const admin = createAdminClient();
  const { data: t } = await admin.from("teachers").select("id, user_id").eq("id", teacherId).eq("school_id", school.id).single();
  if (!t) return;
  if (approve) {
    await admin.from("teachers").update({ status: "active" }).eq("id", t.id);
    if (t.user_id) await admin.from("profiles").update({ status: "active" }).eq("id", t.user_id);
  } else {
    await admin.from("teachers").delete().eq("id", t.id);
    if (t.user_id) await admin.auth.admin.deleteUser(t.user_id);
  }
  revalidatePath(`/${slug}/teachers`);
}

/** Teacher taps Check in / Check out. */
export async function teacherCheckAction(
  slug: string, action: "check_in" | "check_out", _: CheckState,
): Promise<CheckState> {
  const supabase = await createClient();
  const { data: { user } } = await supabase.auth.getUser();
  if (!user) return { error: "Not signed in." };
  const res = await teacherCheck(user.id, action);
  revalidatePath(`/${slug}/teacher`);
  return res.ok ? { ok: true, message: res.message } : { error: res.error };
}

export async function setTeacherAttendance(slug: string, formData: FormData) {
  const supabase = await createClient();
  const { data: school } = await supabase.from("schools").select("id").eq("slug", slug).single();
  if (!school || !(await isSchoolAdmin(supabase, school.id))) return;
  const teacherId = String(formData.get("teacher_id"));
  const date = String(formData.get("date"));
  const status = String(formData.get("status"));
  if (!["present", "late", "absent", "leave"].includes(status)) return;
  await createAdminClient().from("teacher_attendance").upsert(
    { school_id: school.id, teacher_id: teacherId, date, status }, { onConflict: "teacher_id,date" });
  revalidatePath(`/${slug}/teacher-attendance`);
}

export async function generatePayslipsAction(slug: string, _: CheckState, formData: FormData): Promise<CheckState> {
  const supabase = await createClient();
  const { data: school } = await supabase.from("schools").select("id").eq("slug", slug).single();
  if (!school || !(await isSchoolAdmin(supabase, school.id))) return { error: "Not allowed." };
  const month = String(formData.get("month") || "");
  if (!/^\d{4}-\d{2}$/.test(month)) return { error: "Choose a month." };
  const n = await generatePayslips(createAdminClient(), school.id, month);
  revalidatePath(`/${slug}/payslips`);
  return n
    ? { ok: true, message: `Generated ${n} payslip(s).` }
    : { error: "No active teachers, or that month has not started yet." };
}

export async function setPayslipPaid(slug: string, id: string, paid: boolean) {
  const supabase = await createClient();
  const { data: school } = await supabase.from("schools").select("id").eq("slug", slug).single();
  if (!school || !(await isSchoolAdmin(supabase, school.id))) return;
  await createAdminClient().from("payslips")
    .update({ status: paid ? "paid" : "unpaid", paid_on: paid ? new Date().toISOString().slice(0, 10) : null })
    .eq("id", id).eq("school_id", school.id);
  revalidatePath(`/${slug}/payslips`);
}
