import Link from "next/link";
import { redirect } from "next/navigation";
import { Banknote, UserCheck, NotebookPen } from "lucide-react";
import { getTenant } from "@/lib/auth";
import { formatTime, localNow, monthLabel } from "@/lib/time";
import CheckInCard from "@/components/CheckInCard";
import PageHeader from "@/components/PageHeader";

export default async function TeacherHome({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const { supabase, school, profile, userId } = await getTenant(slug);
  if (profile.role !== "teacher") redirect(`/${slug}/dashboard`);

  const tz = (school as { timezone?: string }).timezone ?? "Asia/Karachi";
  const { date } = localNow(tz);
  const month = date.slice(0, 7);

  const { data: teacher } = await supabase.from("teachers").select("id, name, salary").eq("user_id", userId).maybeSingle();
  if (!teacher) {
    return <div className="card">No teacher record is linked to your account. Please contact the school admin.</div>;
  }

  const [{ data: monthRows }, { data: slips }] = await Promise.all([
    supabase.from("teacher_attendance").select("date, status, check_in, check_out")
      .eq("teacher_id", teacher.id).gte("date", `${month}-01`).order("date", { ascending: false }),
    supabase.from("payslips").select("id, month, net_salary, status").eq("teacher_id", teacher.id)
      .order("month", { ascending: false }).limit(3),
  ]);
  const today = (monthRows ?? []).find((r) => r.date === date);
  const count = (s: string) => (monthRows ?? []).filter((r) => r.status === s).length;
  const blocked = today && !today.check_in && (today.status === "leave" || today.status === "absent")
    ? `Today is marked as ${today.status} by the school admin.` : null;

  return (
    <div className="space-y-6">
      <PageHeader title={`Hello, ${profile.name}`} subtitle={`${school.name} · your day at a glance`} />

      <div className="mx-auto max-w-xl">
        <CheckInCard
          slug={slug} timeZone={tz}
          checkedIn={!!today?.check_in} checkedOut={!!today?.check_out}
          checkInTime={formatTime(today?.check_in ?? null, tz)} checkOutTime={formatTime(today?.check_out ?? null, tz)}
          status={today?.status ?? null} blockedReason={blocked}
        />
      </div>

      <div className="grid gap-4 sm:grid-cols-3">
        {[["Present", count("present"), "text-emerald-600"], ["Late", count("late"), "text-amber-600"], ["Leave / Absent", count("leave") + count("absent"), "text-rose-600"]].map(([l, v, c]) => (
          <div key={l as string} className="card text-center">
            <p className={`text-3xl font-extrabold ${c}`}>{v as number}</p>
            <p className="muted text-sm">{l as string} · {monthLabel(month)}</p>
          </div>
        ))}
      </div>

      <div className="grid gap-4 md:grid-cols-2">
        <section className="card">
          <div className="mb-3 flex items-center justify-between">
            <h2 className="font-semibold">Recent attendance</h2>
            <Link href={`/${slug}/attendance`} className="inline-flex items-center gap-1 text-sm text-indigo-600"><UserCheck size={14} /> Mark students</Link>
          </div>
          {(monthRows ?? []).length === 0 && <p className="muted text-sm">No records this month yet.</p>}
          <ul className="divide-y divide-slate-100 dark:divide-white/10">
            {(monthRows ?? []).slice(0, 7).map((r) => (
              <li key={r.date} className="flex items-center justify-between py-2 text-sm">
                <span>{new Date(r.date + "T00:00:00").toLocaleDateString("en-GB", { weekday: "short", day: "numeric", month: "short" })}</span>
                <span className="muted">{formatTime(r.check_in, tz)} – {formatTime(r.check_out, tz)}</span>
                <span className="capitalize">{r.status}</span>
              </li>
            ))}
          </ul>
        </section>

        <section className="card">
          <div className="mb-3 flex items-center justify-between">
            <h2 className="font-semibold">Payslips</h2>
            <Link href={`/${slug}/payslips`} className="inline-flex items-center gap-1 text-sm text-indigo-600"><Banknote size={14} /> All payslips</Link>
          </div>
          {(slips ?? []).length === 0 && <p className="muted text-sm">Your first payslip appears on the 1st of next month.</p>}
          <ul className="divide-y divide-slate-100 dark:divide-white/10">
            {(slips ?? []).map((p) => (
              <li key={p.id}>
                <Link href={`/${slug}/payslips/${p.id}`} className="flex items-center justify-between py-2 text-sm hover:text-indigo-600">
                  <span>{monthLabel(p.month)}</span>
                  <span className="font-semibold">{school.currency ?? "PKR"} {Number(p.net_salary).toLocaleString()}</span>
                  <span className={p.status === "paid" ? "text-emerald-600" : "text-amber-600"}>{p.status}</span>
                </Link>
              </li>
            ))}
          </ul>
          <Link href={`/${slug}/homework`} className="mt-4 inline-flex items-center gap-1 text-sm text-indigo-600"><NotebookPen size={14} /> Homework</Link>
        </section>
      </div>
    </div>
  );
}
