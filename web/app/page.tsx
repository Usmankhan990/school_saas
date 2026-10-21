import Link from "next/link";
import {
  GraduationCap, Building2, Wallet, ClipboardCheck, FileText, Bell, Users, CalendarRange,
  ShieldCheck, BarChart3, ArrowRight, Sparkles,
} from "lucide-react";
import HeroScene from "@/components/HeroScene";
import TiltCard from "@/components/TiltCard";
import ThemeToggle from "@/components/ThemeToggle";

const features = [
  { Icon: Building2, title: "Multi-school platform", text: "Run many schools from one platform. Every school gets its own dashboard and fully separate data." },
  { Icon: Users, title: "Students, teachers & staff", text: "Admissions, profiles, classes and sections — the complete record in one place." },
  { Icon: ClipboardCheck, title: "Smart attendance", text: "Class-wise daily attendance with present, absent, late and leave in one click." },
  { Icon: Wallet, title: "Fees & finance", text: "Fee types, student fees, payments, pending dues and expenses, all tracked." },
  { Icon: FileText, title: "Exams & results", text: "Manage exams, date sheets and subject-wise marks with ease." },
  { Icon: CalendarRange, title: "Timetable & sessions", text: "Academic sessions, timetables, homework and calendar events." },
  { Icon: Bell, title: "Notifications", text: "Send announcements to the whole school, teachers, students or parents." },
  { Icon: ShieldCheck, title: "Secure by design", text: "Supabase Auth with Row Level Security — one school can never see another school's data." },
];

const steps = [
  ["01", "Register", "Create your school and your admin account."],
  ["02", "Set up", "Add classes, sections, subjects and fee types."],
  ["03", "Manage", "Run students, attendance, fees and results every day."],
];

export default function Home() {
  return (
    <div className="bg-[var(--lp-bg)] text-[var(--lp-fg)]">
      {/* NAV */}
      <header className="fixed inset-x-0 top-0 z-30 px-3">
        <div className="glass mx-auto mt-4 flex max-w-6xl items-center justify-between rounded-2xl px-4 py-3 sm:px-5">
          <Link href="/" className="flex items-center gap-2 font-bold">
            <span className="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-fuchsia-500 text-white">
              <GraduationCap size={18} />
            </span>
            EduVerse
          </Link>
          <nav className="lp-muted hidden gap-8 text-sm md:flex">
            <a href="#features" className="hover:text-[var(--lp-fg)]">Features</a>
            <a href="#how" className="hover:text-[var(--lp-fg)]">How it works</a>
            <a href="#cta" className="hover:text-[var(--lp-fg)]">Get started</a>
          </nav>
          <div className="flex items-center gap-2">
            <ThemeToggle />
            <Link href="/login" className="rounded-xl px-3 py-2 text-sm hover:bg-black/5 dark:hover:bg-white/10">Login</Link>
            <Link href="/register" className="btn !py-2">Register school</Link>
          </div>
        </div>
      </header>

      {/* HERO */}
      <section className="relative flex min-h-screen items-center overflow-hidden">
        <div className="absolute inset-0">
          <div className="absolute left-1/4 top-1/4 h-[28rem] w-[28rem] rounded-full bg-indigo-500/25 blur-[120px]" />
          <div className="absolute bottom-0 right-1/4 h-[26rem] w-[26rem] rounded-full bg-fuchsia-500/20 blur-[120px]" />
          <HeroScene align="right" />
        </div>
        <div className="pointer-events-none relative mx-auto w-full max-w-6xl px-6 pt-28">
          <div className="pointer-events-auto max-w-2xl">
            <span className="glass mb-6 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs text-indigo-600 dark:text-indigo-200">
              <Sparkles size={14} /> Complete school management in the cloud
            </span>
            <h1 className="text-5xl font-extrabold leading-[1.05] tracking-tight sm:text-7xl">
              Make your school <span className="text-gradient">smarter</span>
            </h1>
            <p className="lp-muted mt-6 max-w-xl text-lg">
              Students, teachers, attendance, fees, exams and results — everything in one beautiful, fast and
              secure dashboard. One platform, unlimited schools.
            </p>
            <div className="mt-9 flex flex-wrap gap-3">
              <Link href="/register" className="btn !px-7 !py-3.5 text-base">
                Get started free <ArrowRight size={18} />
              </Link>
              <Link href="/login" className="glass rounded-xl px-7 py-3.5 text-base font-semibold hover:brightness-110">
                Login
              </Link>
            </div>
            <div className="mt-12 flex gap-10 text-sm">
              {[["20+", "Modules"], ["100%", "Cloud based"], ["RLS", "Data isolation"]].map(([a, b]) => (
                <div key={b}>
                  <p className="text-2xl font-bold">{a}</p>
                  <p className="lp-muted">{b}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
        <div className="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-[var(--lp-bg)] to-transparent" />
      </section>

      {/* FEATURES */}
      <section id="features" className="mx-auto max-w-6xl px-6 py-24">
        <div className="mb-14 text-center">
          <p className="text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-300">Features</p>
          <h2 className="mt-3 text-4xl font-bold">Everything in one place</h2>
          <p className="lp-muted mx-auto mt-3 max-w-xl">Everything you need to run a school is ready to use.</p>
        </div>
        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          {features.map(({ Icon, title, text }, i) => (
            <TiltCard key={title} delay={(i % 4) * 0.08}>
              <span className="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30">
                <Icon size={22} />
              </span>
              <h3 className="font-semibold">{title}</h3>
              <p className="lp-muted mt-2 text-sm leading-relaxed">{text}</p>
            </TiltCard>
          ))}
        </div>
      </section>

      {/* HOW */}
      <section id="how" className="mx-auto max-w-6xl px-6 py-20">
        <h2 className="mb-12 text-center text-4xl font-bold">3 simple steps</h2>
        <div className="grid gap-5 md:grid-cols-3">
          {steps.map(([n, t, d], i) => (
            <TiltCard key={n} delay={i * 0.1}>
              <p className="text-gradient text-5xl font-extrabold">{n}</p>
              <h3 className="mt-3 text-xl font-semibold">{t}</h3>
              <p className="lp-muted mt-2">{d}</p>
            </TiltCard>
          ))}
        </div>
      </section>

      {/* STATS BAND */}
      <section className="mx-auto max-w-6xl px-6 py-10">
        <div className="glass grid gap-8 rounded-3xl p-10 text-center sm:grid-cols-3">
          {[[BarChart3, "Live dashboard", "Real-time overview of students, fees and attendance"],
            [Wallet, "Fee tracking", "Payments automatically settle pending dues"],
            [ShieldCheck, "Safe data", "Every school's data is isolated and protected"]].map(([I, t, d]) => {
            const Icon = I as typeof BarChart3;
            return (
              <div key={t as string}>
                <Icon className="mx-auto mb-3 text-indigo-500 dark:text-indigo-300" size={28} />
                <p className="font-semibold">{t as string}</p>
                <p className="lp-muted mt-1 text-sm">{d as string}</p>
              </div>
            );
          })}
        </div>
      </section>

      {/* CTA */}
      <section id="cta" className="px-6 py-24">
        <div className="relative mx-auto max-w-4xl overflow-hidden rounded-[2rem] bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 p-12 text-center text-white shadow-2xl shadow-indigo-900/30">
          <h2 className="text-4xl font-bold">Take your school online today</h2>
          <p className="mx-auto mt-3 max-w-lg text-indigo-100">Register and have your dashboard ready in minutes.</p>
          <Link href="/register" className="mt-8 inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 font-semibold text-indigo-700 transition hover:scale-105">
            Register your school <ArrowRight size={18} />
          </Link>
        </div>
      </section>

      <footer className="lp-muted border-t border-[var(--glass-border)] py-8 text-center text-sm">
        © {new Date().getFullYear()} EduVerse · School Management System
      </footer>
    </div>
  );
}
