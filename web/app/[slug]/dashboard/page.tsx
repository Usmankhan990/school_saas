import { getTenant } from "@/lib/auth";
import { GraduationCap, UserRound, Wallet, AlertCircle, UserCheck, TrendingDown } from "lucide-react";
import PageHeader from "@/components/PageHeader";
import TiltCard from "@/components/TiltCard";

const money = (n: number, c = "PKR") => `${c} ${Math.round(n).toLocaleString()}`;

export default async function Dashboard({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const { supabase, school, profile } = await getTenant(slug);
  const today = new Date().toISOString().slice(0, 10);
  const monthStart = today.slice(0, 8) + "01";

  const count = (t: string) => supabase.from(t).select("*", { count: "exact", head: true });
  const [students, teachers, staff, present, absent, pays, unpaid, expenses, notes] = await Promise.all([
    count("students"), count("teachers"), count("staff"),
    supabase.from("attendance").select("*", { count: "exact", head: true }).eq("date", today).eq("status", "present"),
    supabase.from("attendance").select("*", { count: "exact", head: true }).eq("date", today).eq("status", "absent"),
    supabase.from("payments").select("amount").gte("paid_on", monthStart),
    supabase.from("student_fees").select("amount").neq("status", "paid"),
    supabase.from("expenses").select("amount").gte("spent_on", monthStart),
    supabase.from("notifications").select("title, created_at").order("created_at", { ascending: false }).limit(5),
  ]);
  const sum = (rows: { amount: number }[] | null) => (rows ?? []).reduce((a, r) => a + Number(r.amount), 0);
  const cur = school.currency ?? "PKR";

  const cards = [
    { label: "Students", value: students.count ?? 0, Icon: GraduationCap, tint: "from-indigo-500 to-blue-500" },
    { label: "Teachers", value: teachers.count ?? 0, Icon: UserRound, tint: "from-fuchsia-500 to-pink-500" },
    { label: "Present today", value: present.count ?? 0, Icon: UserCheck, tint: "from-emerald-500 to-teal-500" },
    { label: "Absent today", value: absent.count ?? 0, Icon: AlertCircle, tint: "from-orange-500 to-red-500" },
    { label: "Collected this month", value: money(sum(pays.data), cur), Icon: Wallet, tint: "from-cyan-500 to-sky-500" },
    { label: "Fees pending", value: money(sum(unpaid.data), cur), Icon: AlertCircle, tint: "from-amber-500 to-orange-500" },
    { label: "Expenses this month", value: money(sum(expenses.data), cur), Icon: TrendingDown, tint: "from-rose-500 to-red-500" },
    { label: "Staff", value: staff.count ?? 0, Icon: UserRound, tint: "from-violet-500 to-purple-500" },
  ];

  return (
    <div className="space-y-6">
      <PageHeader title={`Welcome, ${profile.name}`} subtitle={`${school.name} overview`} />
      <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {cards.map(({ label, value, Icon, tint }, i) => (
          <TiltCard key={label} delay={(i % 4) * 0.06} className="p-5" tilt={10}>
            <div className="flex items-center gap-4">
              <span className={`grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br ${tint} text-white shadow-lg`}>
                <Icon size={22} />
              </span>
              <div className="min-w-0">
                <p className="truncate text-xl font-bold">{value}</p>
                <p className="muted text-xs">{label}</p>
              </div>
            </div>
          </TiltCard>
        ))}
      </div>
      <section className="card">
        <h2 className="mb-3 font-semibold">Latest notifications</h2>
        {(notes.data ?? []).length === 0 && <p className="muted text-sm">Nothing yet.</p>}
        <ul className="divide-y divide-slate-100 dark:divide-white/10">
          {(notes.data ?? []).map((n, i) => (
            <li key={i} className="flex justify-between py-2 text-sm">
              <span>{n.title}</span>
              <span className="muted">{new Date(n.created_at).toLocaleDateString()}</span>
            </li>
          ))}
        </ul>
      </section>
    </div>
  );
}
