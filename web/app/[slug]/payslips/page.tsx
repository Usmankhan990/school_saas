import Link from "next/link";
import { getTenant } from "@/lib/auth";
import { localNow, monthLabel, previousMonth } from "@/lib/time";
import { setPayslipPaid } from "@/app/teacher-actions";
import GeneratePayslips from "@/components/GeneratePayslips";
import PageHeader from "@/components/PageHeader";

type Slip = {
  id: string; month: string; basic_salary: number; absent_days: number; deduction: number;
  net_salary: number; status: string; teachers: { name: string } | null;
};

export default async function PayslipsPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const { supabase, school, profile } = await getTenant(slug);
  const isTeacher = profile.role === "teacher";
  const cur = school.currency ?? "PKR";
  const tz = (school as { timezone?: string }).timezone ?? "Asia/Karachi";

  const { data } = await supabase.from("payslips").select("*, teachers(name)")
    .eq("school_id", school.id).order("month", { ascending: false }).limit(300);
  const slips = (data ?? []) as Slip[];

  return (
    <div className="space-y-6">
      <PageHeader title={isTeacher ? "My Payslips" : "Payslips"}
        subtitle={isTeacher ? "Your monthly salary slips" : "Generated automatically from teacher attendance"} />
      {!isTeacher && <GeneratePayslips slug={slug} defaultMonth={previousMonth(localNow(tz).date)} />}

      <div className="card overflow-x-auto p-0">
        <table className="w-full text-left text-sm">
          <thead className="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-white/5 dark:text-slate-400">
            <tr>
              {!isTeacher && <th className="px-4 py-3">Teacher</th>}
              <th className="px-4 py-3">Month</th><th className="px-4 py-3">Basic</th>
              <th className="px-4 py-3">Absent</th><th className="px-4 py-3">Deduction</th>
              <th className="px-4 py-3">Net</th><th className="px-4 py-3">Status</th><th />
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100 dark:divide-white/10">
            {slips.length === 0 && (
              <tr><td colSpan={8} className="px-4 py-10 text-center text-slate-400">No payslips yet.</td></tr>
            )}
            {slips.map((p) => (
              <tr key={p.id}>
                {!isTeacher && <td className="px-4 py-3 font-medium">{p.teachers?.name ?? "—"}</td>}
                <td className="px-4 py-3">{monthLabel(p.month)}</td>
                <td className="px-4 py-3">{cur} {Number(p.basic_salary).toLocaleString()}</td>
                <td className="px-4 py-3">{p.absent_days}</td>
                <td className="px-4 py-3">{cur} {Number(p.deduction).toLocaleString()}</td>
                <td className="px-4 py-3 font-semibold">{cur} {Number(p.net_salary).toLocaleString()}</td>
                <td className="px-4 py-3">
                  <span className={`rounded-full px-2.5 py-1 text-xs font-medium ${p.status === "paid" ? "bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300" : "bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300"}`}>{p.status}</span>
                </td>
                <td className="whitespace-nowrap px-4 py-3 text-right">
                  <Link href={`/${slug}/payslips/${p.id}`} className="text-indigo-600 hover:underline">View</Link>
                  {!isTeacher && (
                    <form action={setPayslipPaid.bind(null, slug, p.id, p.status !== "paid")} className="ml-3 inline">
                      <button className="text-xs text-slate-500 hover:text-slate-900 dark:hover:text-white">
                        {p.status === "paid" ? "Mark unpaid" : "Mark paid"}
                      </button>
                    </form>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
