import { redirect } from "next/navigation";
import { createClient } from "@/lib/supabase/server";

export type Profile = {
  id: string; school_id: string | null; name: string; email: string | null; role: string;
};
export type School = { id: string; name: string; slug: string; currency: string | null; status: string | null };

/** Resolve the signed-in user + the school for /[slug]; redirects when not allowed. */
export async function getTenant(slug: string) {
  const supabase = await createClient();
  const { data: { user } } = await supabase.auth.getUser();
  if (!user) redirect("/login");

  const { data: profile } = await supabase.from("profiles").select("*").eq("id", user.id).single<Profile>();
  if (!profile) redirect("/login");

  const { data: school } = await supabase.from("schools").select("*").eq("slug", slug).single<School>();
  if (!school) redirect(profile.role === "platform_admin" ? "/platform" : "/login");

  if (profile.role !== "platform_admin" && profile.school_id !== school.id) redirect("/login");
  return { supabase, user, profile, school };
}

export async function getPlatformAdmin() {
  const supabase = await createClient();
  const { data: { user } } = await supabase.auth.getUser();
  if (!user) redirect("/login");
  const { data: profile } = await supabase.from("profiles").select("*").eq("id", user.id).single<Profile>();
  if (!profile || profile.role !== "platform_admin") redirect("/login");
  return { supabase, profile };
}
