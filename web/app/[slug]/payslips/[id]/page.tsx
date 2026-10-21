import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import { getTenant } from "@/lib/auth";
import { monthLabel } from "@/lib/time";
import PrintButton from "@/components/PrintButton";

export default async function PayslipPage({ params }: { params: Promise<{ slug: string; id: string }> }) {
  const { slug, id } = await params;
  const { supabase, school } = await getTenant(slug);
  const { data: p } = await supabase.from("payslips").select("*, teachers(name, phone, email)")
    .eq("id", id).eq("school_id", school.id).maybeSingle();
  if (!p) notFound();

  const cur = school.currency ?? "PKR";
  const money = (n: number) => `${cur} ${Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  const t = p.teachers as { name: string; phone: string | null; email: string | null } | null;
  const perDay = p.working_days ? Number(p.basic_salary) / p.working_days : 0;

  return (
    <div className="mx-auto max-w-2xl space-y-4">
      <div className="flex items-center justify-between print:hidden">
        <Link href={`/${slug}/payslips`} className="inline-flex items-center gap-1 text-sm text-indigo-600"><ArrowLeft size={14} /> Back</Link>
        <PrintButton />
      </div>

      <article className="rounded-2xl border border-slate-200 bg-white p-8 text-slate-900 shadow-sm print:rounded-none print:border-0 print:shadow-none">
        <header className="flex items-start justify-between border-b border-slate-200 pb-5">
          <div>
            <h1 className="text-2xl font-extrabold">{school.name}</h1>
            <p className="text-sm text-slate-500">Salary slip</p>
          </div>
          <div className="text-right">
            <p className="text-lg font-bold">{monthLabel(p.month)}</p>
            <span className={`rounded-full px-3 py-1 text-xs font-semibold ${p.status === "paid" ? "bg-emerald-100 text-emerald-700" : "bg-amber-100 text-amber-700"}`}>
              {p.status === "paid" ? `Paid${p.paid_on ? " on " + p.paid_on : ""}` : "Unpaid"}
            </span>
          </div>
        </header>

        <section className="grid grid-cols-2 gap-4 py-5 text-sm">
          <div><p className="text-slate-500">Employee</p><p className="font-semibold">{t?.name ?? "—"}</p></div>
          <div><p className="text-slate-500">Contact</p><p className="break-all font-semibold">{t?.phone ?? t?.email ?? "—"}</p></div>
          <div><p className="text-slate-500">Working days</p><p className="font-semibold">{p.working_days}</p></div>
          <div><p className="text-slate-500">Per-day rate</p><p className="font-semibold">{money(perDay)}</p></div>
        </section>

        <table className="w-full text-sm">
          <thead>
            <tr className="border-y border-slate-200 bg-slate-50 text-left text-xs uppercase text-slate-500">
              <th className="px-3 py-2">Attendance</th><th className="px-3 py-2 text-right">Days</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            <tr><td className="px-3 py-2">Present</td><td className="px-3 py-2 text-right">{p.present_days}</td></tr>
            <tr><td className="px-3 py-2">Late (counted as present)</td><td className="px-3 py-2 text-right">{p.late_days}</td></tr>
            <tr><td className="px-3 py-2">Leave (paid)</td><td className="px-3 py-2 text-right">{p.leave_days}</td></tr>
            <tr><td className="px-3 py-2">Absent / not marked</td><td className="px-3 py-2 text-right">{p.absent_days}</td></tr>
          </tbody>
        </table>

        <table className="mt-5 w-full text-sm">
          <tbody className="divide-y divide-slate-100">
            <tr><td className="px-3 py-2">Basic salary</td><td className="px-3 py-2 text-right">{money(p.basic_salary)}</td></tr>
            <tr><td className="px-3 py-2">Bonus</td><td className="px-3 py-2 text-right">{money(p.bonus)}</td></tr>
            <tr><td className="px-3 py-2 text-rose-600">Deduction ({p.absent_days} day{p.absent_days === 1 ? "" : "s"})</td><td className="px-3 py-2 text-right text-rose-600">− {money(p.deduction)}</td></tr>
            <tr className="border-t-2 border-slate-300 text-base font-bold"><td className="px-3 py-3">Net salary</td><td className="px-3 py-3 text-right">{money(p.net_salary)}</td></tr>
          </tbody>
        </table>

        <footer className="mt-10 flex justify-between text-xs text-slate-400">
          <span>Generated {new Date(p.generated_at).toLocaleDateString()} · computer generated slip</span>
          <span>Authorised signature: ______________</span>
        </footer>
      </article>
    </div>
  );
}
