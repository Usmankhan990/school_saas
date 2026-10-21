import Link from "next/link";
import {
  GraduationCap, Building2, Wallet, ClipboardCheck, FileText, Bell, Users, CalendarRange,
  ShieldCheck, BarChart3, ArrowRight, Sparkles,
} from "lucide-react";
import HeroScene from "@/components/HeroScene";
import TiltCard from "@/components/TiltCard";

const features = [
  { Icon: Building2, title: "Multi-school platform", text: "Ek hi platform par kai schools. Har school ka apna dashboard aur alag data." },
  { Icon: Users, title: "Students, teachers & staff", text: "Admissions, profiles, classes aur sections ka poora record ek jagah." },
  { Icon: ClipboardCheck, title: "Smart attendance", text: "Class-wise daily attendance, present / absent / late / leave ek click mein." },
  { Icon: Wallet, title: "Fees & finance", text: "Fee types, vouchers, payments, pending dues aur expenses ka hisaab." },
  { Icon: FileText, title: "Exams & results", text: "Exams, date sheets aur subject-wise marks ka aasaan management." },
  { Icon: CalendarRange, title: "Timetable & sessions", text: "Academic sessions, timetable, homework aur calendar events." },
  { Icon: Bell, title: "Notifications", text: "Poore school, teachers ya parents ko announcements bhejein." },
  { Icon: ShieldCheck, title: "Secure by design", text: "Supabase Auth aur Row Level Security — ek school ka data doosra nahi dekh sakta." },
];

const steps = [
  ["01", "Register", "Apne school ka naam aur admin account banayein."],
  ["02", "Setup", "Classes, sections, subjects aur fee types add karein."],
  ["03", "Manage", "Students, attendance, fees aur results roz chalayein."],
];

export default function Home() {
  return (
    <div className="bg-[#070816] text-white">
      {/* NAV */}
      <header className="fixed inset-x-0 top-0 z-30">
        <div className="glass mx-auto mt-4 flex max-w-6xl items-center justify-between rounded-2xl px-5 py-3">
          <Link href="/" className="flex items-center gap-2 font-bold">
            <span className="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-fuchsia-500">
              <GraduationCap size={18} />
            </span>
            EduVerse
          </Link>
          <nav className="hidden gap-8 text-sm text-slate-300 md:flex">
            <a href="#features" className="hover:text-white">Features</a>
            <a href="#how" className="hover:text-white">How it works</a>
            <a href="#cta" className="hover:text-white">Get started</a>
          </nav>
          <div className="flex items-center gap-2">
            <Link href="/login" className="rounded-xl px-4 py-2 text-sm text-slate-200 hover:bg-white/10">Login</Link>
            <Link href="/register" className="btn !py-2">Register school</Link>
          </div>
        </div>
      </header>

      {/* HERO */}
      <section className="relative flex min-h-screen items-center overflow-hidden">
        <div className="absolute inset-0">
          <div className="absolute left-1/4 top-1/4 h-[28rem] w-[28rem] rounded-full bg-indigo-600/25 blur-[120px]" />
          <div className="absolute bottom-0 right-1/4 h-[26rem] w-[26rem] rounded-full bg-fuchsia-600/20 blur-[120px]" />
          <HeroScene />
        </div>
        <div className="pointer-events-none relative mx-auto w-full max-w-6xl px-6 pt-28">
          <div className="pointer-events-auto max-w-2xl">
            <span className="glass mb-6 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs text-indigo-200">
              <Sparkles size={14} /> Complete school management, cloud par
            </span>
            <h1 className="text-5xl font-extrabold leading-[1.05] tracking-tight sm:text-7xl">
              Apna school <span className="text-gradient">smart</span> banayein
            </h1>
            <p className="mt-6 max-w-xl text-lg text-slate-300">
              Students, teachers, attendance, fees, exams aur results — sab kuch ek khoobsurat, tez aur
              secure dashboard mein. Ek platform, anginat schools.
            </p>
            <div className="mt-9 flex flex-wrap gap-3">
              <Link href="/register" className="btn !px-7 !py-3.5 text-base">
                Free start karein <ArrowRight size={18} />
              </Link>
              <Link href="/login" className="glass rounded-xl px-7 py-3.5 text-base font-semibold hover:bg-white/10">
                Login
              </Link>
            </div>
            <div className="mt-12 flex gap-10 text-sm">
              {[["20+", "Modules"], ["100%", "Cloud based"], ["RLS", "Data isolation"]].map(([a, b]) => (
                <div key={b}>
                  <p className="text-2xl font-bold">{a}</p>
                  <p className="text-slate-400">{b}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
        <div className="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-[#070816] to-transparent" />
      </section>

      {/* FEATURES */}
      <section id="features" className="mx-auto max-w-6xl px-6 py-24">
        <div className="mb-14 text-center">
          <p className="text-sm font-semibold uppercase tracking-widest text-indigo-300">Features</p>
          <h2 className="mt-3 text-4xl font-bold">Sab kuch ek hi jagah</h2>
          <p className="mx-auto mt-3 max-w-xl text-slate-400">School chalane ke liye jo kuch chahiye, wo sab ready hai.</p>
        </div>
        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          {features.map(({ Icon, title, text }, i) => (
            <TiltCard key={title} delay={(i % 4) * 0.08}>
              <span className="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 shadow-lg shadow-indigo-500/30">
                <Icon size={22} />
              </span>
              <h3 className="font-semibold">{title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-slate-400">{text}</p>
            </TiltCard>
          ))}
        </div>
      </section>

      {/* HOW */}
      <section id="how" className="mx-auto max-w-6xl px-6 py-20">
        <h2 className="mb-12 text-center text-4xl font-bold">3 aasaan steps</h2>
        <div className="grid gap-5 md:grid-cols-3">
          {steps.map(([n, t, d], i) => (
            <TiltCard key={n} delay={i * 0.1}>
              <p className="text-gradient text-5xl font-extrabold">{n}</p>
              <h3 className="mt-3 text-xl font-semibold">{t}</h3>
              <p className="mt-2 text-slate-400">{d}</p>
            </TiltCard>
          ))}
        </div>
      </section>

      {/* STATS BAND */}
      <section className="mx-auto max-w-6xl px-6 py-10">
        <div className="glass grid gap-8 rounded-3xl p-10 text-center sm:grid-cols-3">
          {[[BarChart3, "Live dashboard", "Students, fees aur attendance ka real-time overview"],
            [Wallet, "Fee tracking", "Payments khud pending dues adjust karti hain"],
            [ShieldCheck, "Safe data", "Har school ka data alag aur mehfooz"]].map(([I, t, d]) => {
            const Icon = I as typeof BarChart3;
            return (
              <div key={t as string}>
                <Icon className="mx-auto mb-3 text-indigo-300" size={28} />
                <p className="font-semibold">{t as string}</p>
                <p className="mt-1 text-sm text-slate-400">{d as string}</p>
              </div>
            );
          })}
        </div>
      </section>

      {/* CTA */}
      <section id="cta" className="px-6 py-24">
        <div className="relative mx-auto max-w-4xl overflow-hidden rounded-[2rem] bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 p-12 text-center shadow-2xl shadow-indigo-900/50">
          <h2 className="text-4xl font-bold">Aaj hi apna school online layein</h2>
          <p className="mx-auto mt-3 max-w-lg text-indigo-100">Register karein aur chand minute mein dashboard tayyar.</p>
          <Link href="/register" className="mt-8 inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 font-semibold text-indigo-700 hover:scale-105 transition">
            School register karein <ArrowRight size={18} />
          </Link>
        </div>
      </section>

      <footer className="border-t border-white/10 py-8 text-center text-sm text-slate-500">
        © {new Date().getFullYear()} EduVerse · School Management System
      </footer>
    </div>
  );
}
