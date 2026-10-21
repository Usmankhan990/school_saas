"use client";

import { useActionState, useRef, useEffect } from "react";
import { createRecord, type FormState } from "@/app/actions";
import type { Field } from "@/lib/resources";

export default function RecordForm({
  slug, resourceKey, fields, lookups,
}: {
  slug: string; resourceKey: string; fields: Field[];
  lookups: Record<string, { id: string; label: string }[]>;
}) {
  const [state, action, pending] = useActionState<FormState, FormData>(
    createRecord.bind(null, slug, resourceKey), undefined);
  const ref = useRef<HTMLFormElement>(null);
  useEffect(() => { if (!pending && !state?.error) ref.current?.reset(); }, [pending, state]);

  return (
    <form ref={ref} action={action} className="card grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      {fields.map((f) => (
        <label key={f.name} className={f.type === "textarea" ? "sm:col-span-2 lg:col-span-3" : ""}>
          <span className="mb-1 block text-xs font-medium text-slate-600">{f.label}{f.required && " *"}</span>
          {f.type === "textarea" ? (
            <textarea name={f.name} rows={2} className="input" />
          ) : f.type === "select" ? (
            <select name={f.name} className="input" defaultValue="">
              <option value="">—</option>
              {f.options!.map((o) => <option key={o}>{o}</option>)}
            </select>
          ) : f.type === "fk" ? (
            <select name={f.name} className="input" defaultValue="">
              <option value="">—</option>
              {(lookups[f.name] ?? []).map((o) => <option key={o.id} value={o.id}>{o.label}</option>)}
            </select>
          ) : (
            <input name={f.name} type={f.type ?? "text"} step={f.type === "number" ? "any" : undefined}
              required={f.required} className="input" />
          )}
        </label>
      ))}
      <div className="flex items-end gap-3 sm:col-span-2 lg:col-span-3">
        <button className="btn" disabled={pending}>{pending ? "Saving…" : "Add record"}</button>
        {state?.error && <span className="text-sm text-red-600">{state.error}</span>}
      </div>
    </form>
  );
}
