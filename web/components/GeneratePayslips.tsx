"use client";

import { useActionState } from "react";
import { generatePayslipsAction, type CheckState } from "@/app/teacher-actions";

export default function GeneratePayslips({ slug, defaultMonth }: { slug: string; defaultMonth: string }) {
  const [state, action, pending] = useActionState<CheckState, FormData>(
    generatePayslipsAction.bind(null, slug) as (s: CheckState, f: FormData) => Promise<CheckState>, undefined);
  return (
    <form action={action} className="card flex flex-wrap items-end gap-4">
      <label>
        <span className="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Month</span>
        <input type="month" name="month" defaultValue={defaultMonth} className="input" required />
      </label>
      <button className="btn" disabled={pending}>{pending ? "Generating…" : "Generate payslips"}</button>
      <p className="muted max-w-md text-xs">
        Built from teacher attendance. Sundays are off; each unmarked or absent working day is deducted at salary ÷ working days.
        Payslips are also created automatically on the 1st of every month.
      </p>
      {state?.message && <span className="text-sm text-emerald-600">{state.message}</span>}
      {state?.error && <span className="text-sm text-red-600">{state.error}</span>}
    </form>
  );
}
