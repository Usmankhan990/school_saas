"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import { createClient } from "@/lib/supabase/server";
import { createAdminClient } from "@/lib/supabase/admin";
import { byKey } from "@/lib/resources";

const slugify = (s: string) =>
  s.toLowerCase().trim().replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "");

export type FormState = { error?: string } | undefined;

export async function login(_: FormState, formData: FormData): Promise<FormState> {
  const supabase = await createClient();
  const { data, error } = await supabase.auth.signInWithPassword({
    email: String(formData.get("email")),
    password: String(formData.get("password")),
  });
  if (error || !data.user) return { error: "Invalid email or password." };

  const { data: profile } = await supabase
    .from("profiles").select("role, school_id").eq("id", data.user.id).single();
  if (!profile) return { error: "No profile found for this account." };
  if (profile.role === "platform_admin") redirect("/platform");

  const { data: school } = await supabase.from("schools").select("slug, status").eq("id", profile.school_id).single();
  if (!school || school.status !== "active") {
    await supabase.auth.signOut();
    return { error: "This school is inactive." };
  }
  redirect(`/${school.slug}/dashboard`);
}

export async function logout() {
  const supabase = await createClient();
  await supabase.auth.signOut();
  redirect("/login");
}

/** Creates a school and its first admin user. Returns the slug, or an error. */
async function provisionSchool(formData: FormData): Promise<{ error: string } | { slug: string; email: string; password: string }> {
  const schoolName = String(formData.get("school_name") || "").trim();
  const name = String(formData.get("name") || "").trim() || "Admin";
  const email = String(formData.get("email") || "").trim();
  const password = String(formData.get("password") || "");
  const slug = slugify(String(formData.get("slug") || schoolName));
  if (!schoolName || !email || !slug) return { error: "All fields are required." };
  if (password.length < 6) return { error: "Password must be at least 6 characters." };

  const admin = createAdminClient();
  const { data: school, error: sErr } = await admin
    .from("schools").insert({ name: schoolName, slug }).select().single();
  if (sErr) return { error: sErr.code === "23505" ? "That school URL is already taken." : sErr.message };

  const { data: created, error: uErr } = await admin.auth.admin.createUser({
    email, password, email_confirm: true,
  });
  if (uErr || !created.user) {
    await admin.from("schools").delete().eq("id", school.id);
    return { error: uErr?.message ?? "Could not create user." };
  }
  await admin.from("profiles").insert({
    id: created.user.id, school_id: school.id, name, email, role: "school_admin",
  });
  return { slug, email, password };
}

/** Public sign-up. */
export async function registerSchool(_: FormState, formData: FormData): Promise<FormState> {
  const res = await provisionSchool(formData);
  if ("error" in res) return res;
  const supabase = await createClient();
  await supabase.auth.signInWithPassword({ email: res.email, password: res.password });
  redirect(`/${res.slug}/dashboard`);
}

/** Platform admin adds a school. */
export async function createSchoolByPlatform(_: FormState, formData: FormData): Promise<FormState> {
  const supabase = await createClient();
  const { data: { user } } = await supabase.auth.getUser();
  const { data: me } = await supabase.from("profiles").select("role").eq("id", user?.id ?? "").single();
  if (me?.role !== "platform_admin") return { error: "Not allowed." };

  const res = await provisionSchool(formData);
  if ("error" in res) return res;
  revalidatePath("/platform");
  return undefined;
}

export async function toggleSchool(id: string, status: string) {
  const supabase = await createClient();
  await supabase.from("schools").update({ status: status === "active" ? "inactive" : "active" }).eq("id", id);
  revalidatePath("/platform");
}

function clean(resourceKey: string, formData: FormData) {
  const r = byKey(resourceKey);
  if (!r) throw new Error("Unknown resource");
  const row: Record<string, unknown> = {};
  for (const f of r.fields) {
    const v = formData.get(f.name);
    if (v === null || v === "") continue;
    row[f.name] = f.type === "number" ? Number(v) : f.name === "is_current" ? v === "true" : v;
  }
  return { r, row };
}

export async function createRecord(slug: string, resourceKey: string, _: FormState, formData: FormData): Promise<FormState> {
  const supabase = await createClient();
  const { data: school } = await supabase.from("schools").select("id").eq("slug", slug).single();
  if (!school) return { error: "School not found." };
  const { r, row } = clean(resourceKey, formData);

  if (r.table === "academic_sessions" && row.is_current)
    await supabase.from("academic_sessions").update({ is_current: false }).eq("school_id", school.id);

  const { error } = await supabase.from(r.table).insert({ ...row, school_id: school.id });
  if (error) return { error: error.message };

  if (r.table === "payments" && row.student_id)
    await settleFees(supabase, school.id, String(row.student_id), Number(row.amount));
  revalidatePath(`/${slug}/${resourceKey}`);
  revalidatePath(`/${slug}/dashboard`);
  return undefined;
}

/** Apply a payment to the student's oldest unpaid fees. */
async function settleFees(
  supabase: Awaited<ReturnType<typeof createClient>>,
  schoolId: string, studentId: string, amount: number,
) {
  const { data: fees } = await supabase.from("student_fees").select("id, amount, status")
    .eq("school_id", schoolId).eq("student_id", studentId).neq("status", "paid").order("due_date");
  let left = amount;
  for (const f of fees ?? []) {
    if (left <= 0) break;
    if (left >= Number(f.amount)) {
      await supabase.from("student_fees").update({ status: "paid" }).eq("id", f.id);
      left -= Number(f.amount);
    } else {
      await supabase.from("student_fees").update({ status: "partial" }).eq("id", f.id);
      left = 0;
    }
  }
}

export async function deleteRecord(slug: string, resourceKey: string, id: string) {
  const supabase = await createClient();
  const r = byKey(resourceKey);
  if (!r) return;
  await supabase.from(r.table).delete().eq("id", id);
  revalidatePath(`/${slug}/${resourceKey}`);
  revalidatePath(`/${slug}/dashboard`);
}

export async function saveAttendance(slug: string, formData: FormData) {
  const supabase = await createClient();
  const { data: school } = await supabase.from("schools").select("id").eq("slug", slug).single();
  if (!school) return;
  const date = String(formData.get("date"));
  const rows = [...formData.entries()]
    .filter(([k]) => k.startsWith("s_"))
    .map(([k, v]) => ({ school_id: school.id, student_id: k.slice(2), date, status: String(v) }));
  if (rows.length) await supabase.from("attendance").upsert(rows, { onConflict: "student_id,date" });
  revalidatePath(`/${slug}/attendance`);
  revalidatePath(`/${slug}/dashboard`);
}
