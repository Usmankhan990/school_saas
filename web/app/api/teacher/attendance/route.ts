import { NextResponse } from "next/server";
import { createAdminClient } from "@/lib/supabase/admin";
import { createClient } from "@/lib/supabase/server";
import { teacherCheck } from "@/lib/teacher";

// Used by the mobile (Flutter) app. Auth: "Authorization: Bearer <supabase access token>".
async function userIdFrom(request: Request): Promise<string | null> {
  const header = request.headers.get("authorization");
  if (header?.startsWith("Bearer ")) {
    const { data } = await createAdminClient().auth.getUser(header.slice(7));
    return data.user?.id ?? null;
  }
  const { data } = await (await createClient()).auth.getUser();
  return data.user?.id ?? null;
}

export async function POST(request: Request) {
  const userId = await userIdFrom(request);
  if (!userId) return NextResponse.json({ ok: false, error: "Not signed in." }, { status: 401 });
  const body = await request.json().catch(() => ({}));
  if (body.action !== "check_in" && body.action !== "check_out")
    return NextResponse.json({ ok: false, error: "action must be check_in or check_out" }, { status: 400 });
  const res = await teacherCheck(userId, body.action);
  return NextResponse.json(res, { status: res.ok ? 200 : 400 });
}
