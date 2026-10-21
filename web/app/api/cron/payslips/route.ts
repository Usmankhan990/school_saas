import { NextResponse } from "next/server";
import { createAdminClient } from "@/lib/supabase/admin";
import { generatePayslips } from "@/lib/payslip";
import { localNow, previousMonth } from "@/lib/time";

// Runs on the 1st of every month (see vercel.json) and builds last month's payslips for every school.
export async function GET(request: Request) {
  const secret = process.env.CRON_SECRET;
  if (!secret || request.headers.get("authorization") !== `Bearer ${secret}`)
    return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const admin = createAdminClient();
  const { data: schools } = await admin.from("schools").select("id, timezone").eq("status", "active");
  let total = 0;
  for (const s of schools ?? []) {
    const month = previousMonth(localNow(s.timezone ?? "Asia/Karachi").date);
    total += await generatePayslips(admin, s.id, month);
  }
  return NextResponse.json({ ok: true, schools: schools?.length ?? 0, payslips: total });
}
