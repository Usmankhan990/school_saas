import Link from "next/link";
import { getPlatformAdmin } from "@/lib/auth";
import { logout, toggleSchool } from "@/app/actions";
import SchoolForm from "@/components/SchoolForm";

export default async function Platform() {
  const { supabase, profile } = await getPlatformAdmin();
  const { data: schools } = await supabase.from("schools").select("*").order("created_at", { ascending: false });

  return (
    <main className="mx-auto max-w-5xl space-y-6 p-4 sm:p-8">
      <header className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Platform Admin</h1>
          <p className="text-sm text-slate-500">{profile.name} · {schools?.length ?? 0} schools</p>
        </div>
        <form action={logout}><button className="rounded-xl border border-slate-200 px-4 py-2 text-sm hover:bg-white">Logout</button></form>
      </header>

      <SchoolForm />

      <div className="card overflow-x-auto p-0">
        <table className="w-full text-left text-sm">
          <thead className="bg-slate-50 text-xs uppercase text-slate-500">
            <tr><th className="px-4 py-3">School</th><th className="px-4 py-3">URL</th><th className="px-4 py-3">Status</th><th /></tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {(schools ?? []).map((s) => (
              <tr key={s.id}>
                <td className="px-4 py-3 font-medium">{s.name}</td>
                <td className="px-4 py-3"><Link className="text-indigo-600 hover:underline" href={`/${s.slug}/dashboard`}>/{s.slug}</Link></td>
                <td className="px-4 py-3">
                  <span className={`rounded-full px-2.5 py-1 text-xs font-medium ${s.status === "active" ? "bg-emerald-100 text-emerald-700" : "bg-slate-100 text-slate-600"}`}>{s.status}</span>
                </td>
                <td className="px-4 py-3 text-right">
                  <form action={toggleSchool.bind(null, s.id, s.status)}>
                    <button className="text-xs text-slate-500 hover:text-slate-900">{s.status === "active" ? "Deactivate" : "Activate"}</button>
                  </form>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </main>
  );
}
