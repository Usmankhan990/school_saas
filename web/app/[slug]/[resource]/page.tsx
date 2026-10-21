import { notFound } from "next/navigation";
import { Trash2 } from "lucide-react";
import { getTenant } from "@/lib/auth";
import { byKey, teacherReadKeys, teacherWriteKeys } from "@/lib/resources";
import { deleteRecord } from "@/app/actions";
import { approveTeacher } from "@/app/teacher-actions";
import RecordForm from "@/components/RecordForm";
import PageHeader from "@/components/PageHeader";

type Row = Record<string, unknown> & { id: string };

export default async function ResourcePage({ params }: { params: Promise<{ slug: string; resource: string }> }) {
  const { slug, resource } = await params;
  const r = byKey(resource);
  if (!r) notFound();
  const { supabase, school, profile } = await getTenant(slug);
  const isTeacher = profile.role === "teacher";
  if (isTeacher && !teacherReadKeys.includes(r.key)) notFound();
  const canWrite = !isTeacher || teacherWriteKeys.includes(r.key);
  let pending: { id: string; name: string; email: string | null; phone: string | null }[] = [];
  if (!isTeacher && r.key === "teachers") {
    const { data: p } = await supabase.from("teachers").select("id, name, email, phone").eq("school_id", school.id).eq("status", "pending");
    pending = p ?? [];
  }

  // lookup tables for foreign keys
  const fkFields = r.fields.filter((f) => f.type === "fk");
  const lookups: Record<string, { id: string; label: string }[]> = {};
  const mainQuery = supabase.from(r.table).select("*").eq("school_id", school.id).order(r.order ?? "created_at", { ascending: r.order ? true : false }).limit(500);
  const [{ data }] = await Promise.all([mainQuery, ...fkFields.map(async (f) => {
    const { data } = await supabase.from(f.fk!.table).select(`id, ${f.fk!.label}`).eq("school_id", school.id).limit(1000);
    lookups[f.name] = ((data ?? []) as unknown as Row[]).map((d) => ({ id: d.id, label: String(d[f.fk!.label]) }));
  })]);

  const rows = (data ?? []) as Row[];
  const label = (col: string, v: unknown) => {
    const f = r.fields.find((x) => x.name === col);
    if (f?.type === "fk") return lookups[col]?.find((o) => o.id === v)?.label ?? "—";
    if (v === null || v === undefined || v === "") return "—";
    if (typeof v === "boolean") return v ? "Yes" : "No";
    if (col === "created_at") return new Date(String(v)).toLocaleDateString();
    return String(v);
  };
  const head = (col: string) => r.fields.find((f) => f.name === col)?.label ?? col.replace(/_/g, " ");

  return (
    <div className="space-y-6">
      <PageHeader title={r.title} subtitle={`Manage ${r.title.toLowerCase()} for your school`} />
      {pending.length > 0 && (
        <div className="card border-amber-400/50 bg-amber-50/60 dark:bg-amber-500/10">
          <h2 className="mb-3 font-semibold">Pending teacher sign-ups ({pending.length})</h2>
          <div className="space-y-2">
            {pending.map((p) => (
              <div key={p.id} className="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-3">
                <div className="text-sm">
                  <p className="font-medium">{p.name}</p>
                  <p className="muted">{p.email}{p.phone ? ` · ${p.phone}` : ""}</p>
                </div>
                <div className="flex gap-2">
                  <form action={approveTeacher.bind(null, slug, p.id, true)}><button className="btn !py-1.5">Approve</button></form>
                  <form action={approveTeacher.bind(null, slug, p.id, false)}><button className="glass rounded-xl px-4 py-1.5 text-sm hover:brightness-110">Reject</button></form>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
      {canWrite && <RecordForm slug={slug} resourceKey={r.key} fields={r.fields} lookups={lookups} />}
      {/* mobile: card list */}
      <div className="space-y-3 sm:hidden">
        {rows.length === 0 && <p className="card muted text-center text-sm">No records yet.</p>}
        {rows.map((row) => (
          <div key={row.id} className="card relative space-y-1.5 pr-12">
            {r.columns.map((c, i) => (
              <div key={c} className={i === 0 ? "text-base font-semibold" : "flex justify-between gap-3 text-sm"}>
                {i === 0 ? label(c, row[c]) : (<><span className="muted">{head(c)}</span><span className="text-right">{label(c, row[c])}</span></>)}
              </div>
            ))}
            {canWrite && <form action={deleteRecord.bind(null, slug, r.key, row.id)} className="absolute right-2 top-2">
              <button className="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600" aria-label="Delete"><Trash2 size={16} /></button>
            </form>}
          </div>
        ))}
      </div>
      <div className="card hidden overflow-x-auto p-0 sm:block">
        <table className="w-full text-left text-sm">
          <thead className="bg-slate-50 text-xs uppercase muted dark:bg-white/5 dark:text-slate-400">
            <tr>
              {r.columns.map((c) => <th key={c} className="px-4 py-3 font-semibold">{head(c)}</th>)}
              <th className="w-12" />
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100 dark:divide-white/10">
            {rows.length === 0 && (
              <tr><td colSpan={r.columns.length + 1} className="px-4 py-10 text-center text-slate-400">No records yet.</td></tr>
            )}
            {rows.map((row) => (
              <tr key={row.id} className="hover:bg-slate-50/70 dark:hover:bg-white/5">
                {r.columns.map((c) => <td key={c} className="px-4 py-3">{label(c, row[c])}</td>)}
                <td className="px-2">
                  {canWrite && <form action={deleteRecord.bind(null, slug, r.key, row.id)}>
                    <button className="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600" aria-label="Delete">
                      <Trash2 size={16} />
                    </button>
                  </form>}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
