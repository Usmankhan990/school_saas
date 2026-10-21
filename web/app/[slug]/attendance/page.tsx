import Link from "next/link";
import { getTenant } from "@/lib/auth";
import { saveAttendance } from "@/app/actions";

type Student = { id: string; name: string; roll_no: string | null; admission_no: string };

export default async function AttendancePage({
  params, searchParams,
}: {
  params: Promise<{ slug: string }>;
  searchParams: Promise<{ class?: string; date?: string }>;
}) {
  const { slug } = await params;
  const { class: classId, date: dateQ } = await searchParams;
  const date = dateQ ?? new Date().toISOString().slice(0, 10);
  const { supabase } = await getTenant(slug);

  const { data: classes } = await supabase.from("classes").select("id, name").order("numeric_name");
  let students: Student[] = [];
  const existing: Record<string, string> = {};
  if (classId) {
    const { data } = await supabase.from("students").select("id, name, roll_no, admission_no")
      .eq("class_id", classId).eq("status", "active").order("name");
    students = (data ?? []) as Student[];
    const { data: att } = await supabase.from("attendance").select("student_id, status").eq("date", date);
    (att ?? []).forEach((a) => { existing[a.student_id] = a.status; });
  }

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Attendance</h1>
      <form className="card flex flex-wrap items-end gap-4">
        <label>
          <span className="mb-1 block text-xs font-medium text-slate-600">Class</span>
          <select name="class" defaultValue={classId ?? ""} className="input min-w-48">
            <option value="">Select class</option>
            {(classes ?? []).map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
          </select>
        </label>
        <label>
          <span className="mb-1 block text-xs font-medium text-slate-600">Date</span>
          <input type="date" name="date" defaultValue={date} className="input" />
        </label>
        <button className="btn">Load</button>
      </form>

      {classId && (
        <form action={saveAttendance.bind(null, slug)} className="card p-0">
          <input type="hidden" name="date" value={date} />
          <table className="w-full text-left text-sm">
            <thead className="bg-slate-50 text-xs uppercase text-slate-500">
              <tr><th className="px-4 py-3">Roll</th><th className="px-4 py-3">Student</th><th className="px-4 py-3">Status</th></tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {students.length === 0 && (
                <tr><td colSpan={3} className="px-4 py-10 text-center text-slate-400">
                  No active students in this class. <Link href={`/${slug}/students`} className="text-indigo-600">Add students</Link>
                </td></tr>
              )}
              {students.map((s) => (
                <tr key={s.id}>
                  <td className="px-4 py-3">{s.roll_no ?? s.admission_no}</td>
                  <td className="px-4 py-3 font-medium">{s.name}</td>
                  <td className="px-4 py-3">
                    <div className="flex gap-4">
                      {["present", "absent", "late", "leave"].map((st) => (
                        <label key={st} className="flex items-center gap-1.5 capitalize">
                          <input type="radio" name={`s_${s.id}`} value={st} defaultChecked={(existing[s.id] ?? "present") === st} />
                          {st}
                        </label>
                      ))}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
          {students.length > 0 && <div className="p-4"><button className="btn">Save attendance</button></div>}
        </form>
      )}
    </div>
  );
}
