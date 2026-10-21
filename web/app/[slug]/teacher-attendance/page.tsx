import { redirect } from "next/navigation";
import { getTenant } from "@/lib/auth";
import { formatTime, localNow } from "@/lib/time";
import { setTeacherAttendance } from "@/app/teacher-actions";
import PageHeader from "@/components/PageHeader";

export default async function TeacherAttendancePage({
  params, searchParams,
}: { params: Promise<{ slug: string }>; searchParams: Promise<{ date?: string }> }) {
  const { slug } = await params;
  const { date: dateQ } = await searchParams;
  const { supabase, school, profile } = await getTenant(slug);
  if (profile.role === "teacher") redirect(`/${slug}/teacher`);

  const tz = (school as { timezone?: string }).timezone ?? "Asia/Karachi";
  const date = dateQ ?? localNow(tz).date;

  const [{ data: teachers }, { data: rows }] = await Promise.all([
    supabase.from("teachers").select("id, name, phone").eq("school_id", school.id).eq("status", "active").order("name"),
    supabase.from("teacher_attendance").select("*").eq("school_id", school.id).eq("date", date),
  ]);
  const byTeacher = new Map((rows ?? []).map((r) => [r.teacher_id, r]));
  const badge: Record<string, string> = {
    present: "bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300",
    late: "bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300",
    absent: "bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300",
    leave: "bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-300",
  };

  return (
    <div className="space-y-6">
      <PageHeader title="Teacher Attendance" subtitle="Teachers check in and out from their phones" />
      <form className="card flex flex-wrap items-end gap-4">
        <label>
          <span className="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Date</span>
          <input type="date" name="date" defaultValue={date} className="input" />
        </label>
        <button className="btn">Show</button>
        <p className="muted text-xs">Late after {String((school as { late_after?: string }).late_after ?? "08:30").slice(0, 5)} ({tz}). A day with no record counts as absent in payslips.</p>
      </form>

      <div className="card overflow-x-auto p-0">
        <table className="w-full text-left text-sm">
          <thead className="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-white/5 dark:text-slate-400">
            <tr><th className="px-4 py-3">Teacher</th><th className="px-4 py-3">Check in</th><th className="px-4 py-3">Check out</th><th className="px-4 py-3">Status</th><th className="px-4 py-3">Set manually</th></tr>
          </thead>
          <tbody className="divide-y divide-slate-100 dark:divide-white/10">
            {(teachers ?? []).length === 0 && (
              <tr><td colSpan={5} className="px-4 py-10 text-center text-slate-400">No active teachers yet.</td></tr>
            )}
            {(teachers ?? []).map((t) => {
              const r = byTeacher.get(t.id);
              return (
                <tr key={t.id}>
                  <td className="px-4 py-3 font-medium">{t.name}</td>
                  <td className="px-4 py-3">{formatTime(r?.check_in ?? null, tz)}</td>
                  <td className="px-4 py-3">{formatTime(r?.check_out ?? null, tz)}</td>
                  <td className="px-4 py-3">
                    {r ? <span className={`rounded-full px-2.5 py-1 text-xs font-medium capitalize ${badge[r.status]}`}>{r.status}</span> : <span className="muted">Not marked</span>}
                  </td>
                  <td className="px-4 py-3">
                    <form action={setTeacherAttendance.bind(null, slug)} className="flex gap-2">
                      <input type="hidden" name="teacher_id" value={t.id} />
                      <input type="hidden" name="date" value={date} />
                      <select name="status" defaultValue={r?.status ?? "present"} className="input !w-auto !py-1.5">
                        {["present", "late", "absent", "leave"].map((s) => <option key={s}>{s}</option>)}
                      </select>
                      <button className="rounded-lg border border-[var(--border)] px-3 text-sm hover:bg-black/5 dark:hover:bg-white/10">Save</button>
                    </form>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
