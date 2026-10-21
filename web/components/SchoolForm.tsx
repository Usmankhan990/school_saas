"use client";

import { useActionState, useRef, useEffect } from "react";
import { createSchoolByPlatform, type FormState } from "@/app/actions";

export default function SchoolForm() {
  const [state, action, pending] = useActionState<FormState, FormData>(createSchoolByPlatform, undefined);
  const ref = useRef<HTMLFormElement>(null);
  useEffect(() => { if (!pending && !state?.error) ref.current?.reset(); }, [pending, state]);
  const f = (name: string, label: string, type = "text") => (
    <label>
      <span className="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">{label}</span>
      <input name={name} type={type} className="input" required={name !== "slug"} />
    </label>
  );
  return (
    <form ref={ref} action={action} className="card grid gap-4 sm:grid-cols-3">
      {f("school_name", "School name")}
      {f("slug", "URL slug (optional)")}
      {f("name", "Admin name")}
      {f("email", "Admin email", "email")}
      {f("password", "Admin password", "password")}
      <div className="flex items-end gap-3">
        <button className="btn" disabled={pending}>{pending ? "Creating…" : "Add school"}</button>
        {state?.error && <span className="text-sm text-red-600">{state.error}</span>}
      </div>
    </form>
  );
}
