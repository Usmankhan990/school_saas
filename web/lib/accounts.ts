import { createAdminClient } from "@/lib/supabase/admin";
import { createClient } from "@/lib/supabase/server";

export type Sb = Awaited<ReturnType<typeof createClient>>;

/** True when the signed-in user is an active admin of that school (or platform admin). */
export async function isSchoolAdmin(supabase: Sb, schoolId: string) {
  const { data: { user } } = await supabase.auth.getUser();
  if (!user) return false;
  const { data: me } = await supabase.from("profiles").select("role, school_id, status").eq("id", user.id).single();
  return !!me && me.status === "active" &&
    (me.role === "platform_admin" || (me.role === "school_admin" && me.school_id === schoolId));
}

/** Creates a Supabase auth user plus its profile row (service role). */
export async function createLogin(
  schoolId: string, name: string, email: string, password: string, role: string, status: string,
): Promise<{ error: string } | { userId: string }> {
  const admin = createAdminClient();
  const { data: created, error } = await admin.auth.admin.createUser({ email, password, email_confirm: true });
  if (error || !created.user) return { error: error?.message ?? "Could not create login." };
  const { error: pErr } = await admin.from("profiles").insert({
    id: created.user.id, school_id: schoolId, name, email, role, status,
  });
  if (pErr) {
    await admin.auth.admin.deleteUser(created.user.id);
    return { error: pErr.message };
  }
  return { userId: created.user.id };
}
