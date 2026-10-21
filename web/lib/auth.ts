import { cache } from "react";
import { redirect } from "next/navigation";
import { createClient } from "@/lib/supabase/server";

export type Profile = {
  id: string; school_id: string | null; name: string; email: string | null; role: string; status: string;
};
export type School = { id: string; name: string; slug: string; currency: string | null; status: string | null };

/**
 * Resolve the signed-in user + the school for /[slug]; redirects when not allowed.
 * Wrapped in cache() so the layout and the page share one lookup per request.
 * getClaims() verifies the JWT locally, avoiding a round trip to Supabase Auth.
 */
export const getTenant = cache(async (slug: string) => {
  const supabase = await createClient();
  const { data } = await supabase.auth.getClaims();
  const userId = data?.claims?.sub;
  if (!userId) redirect("/login");

  const [{ data: profile }, { data: school }] = await Promise.all([
    supabase.from("profiles").select("*").eq("id", userId).single<Profile>(),
    supabase.from("schools").select("*").eq("slug", slug).single<School>(),
  ]);
  if (!profile) redirect("/login");
  if (profile.status !== "active") redirect("/login");
  if (!school) redirect(profile.role === "platform_admin" ? "/platform" : "/login");
  if (profile.role !== "platform_admin" && profile.school_id !== school.id) redirect("/login");
  return { supabase, userId, profile, school };
});

export async function getPlatformAdmin() {
  const supabase = await createClient();
  const { data } = await supabase.auth.getClaims();
  const userId = data?.claims?.sub;
  if (!userId) redirect("/login");
  const { data: profile } = await supabase.from("profiles").select("*").eq("id", userId).single<Profile>();
  if (!profile || profile.role !== "platform_admin") redirect("/login");
  return { supabase, profile };
}
