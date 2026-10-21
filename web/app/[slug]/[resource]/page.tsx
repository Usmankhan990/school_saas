import { notFound } from "next/navigation";
import { Trash2 } from "lucide-react";
import { getTenant } from "@/lib/auth";
import { byKey } from "@/lib/resources";
import { deleteRecord } from "@/app/actions";
import RecordForm from "@/components/RecordForm";
import PageHeader from "@/components/PageHeader";

type Row = Record<string, unknown> & { id: string };

export default async function ResourcePage({ params }: { params: Promise<{ slug: string; resource: string }> }) {
  const { slug, resource } = await params;
  const r = byKey(resource);
  if (!r) notFound();
  const { supabase } = await getTenant(slug);

  // lookup tables for foreign keys
  const fkFields = r.fields.filter((f) => f.type === "fk");
  const lookups: Record<string, { id: string; label: string }[]> = {};
  const mainQuery = supabase.from(r.table).select("*").order(r.order ?? "created_at", { ascending: r.order ? true : false }).limit(500);
  const [{ data }] = await Promise.all([mainQuery, ...fkFields.map(async (f) => {
    const { data } = await supabase.from(f.fk!.table).select(`id, ${f.fk!.label}`).limit(1000);
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
      <RecordForm slug={slug} resourceKey={r.key} fields={r.fields} lookups={lookups} />
      <div className="card overflow-x-auto p-0">
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
                  <form action={deleteRecord.bind(null, slug, r.key, row.id)}>
                    <button className="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600" aria-label="Delete">
                      <Trash2 size={16} />
                    </button>
                  </form>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
