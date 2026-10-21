import Link from "next/link";
import { ArrowLeft, GraduationCap, ShieldCheck, Wallet, ClipboardCheck } from "lucide-react";
import ThemeToggle from "./ThemeToggle";
import HeroScene from "./HeroScene";

export default function AuthShell({ title, subtitle, children }: { title: string; subtitle: string; children: React.ReactNode }) {
  return (
    <main className="relative flex min-h-screen items-center justify-center overflow-hidden bg-[var(--lp-bg)] px-4 py-10 text-[var(--lp-fg)] lg:justify-end lg:pr-[8%]">
      {/* 3D scene fills the page, sitting on the left on desktop */}
      <div className="absolute inset-0">
        <div className="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-indigo-500/30 blur-3xl" />
        <div className="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-fuchsia-500/25 blur-3xl" />
        <HeroScene align="left" />
      </div>

      <div className="absolute left-4 right-4 top-4 flex items-center justify-between">
        <Link href="/" className="glass inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm hover:brightness-110">
          <ArrowLeft size={16} /> Home
        </Link>
        <ThemeToggle />
      </div>

      {/* desktop tagline under the 3D scene */}
      <div className="pointer-events-none absolute bottom-10 left-[6%] hidden max-w-sm lg:block">
        <p className="text-2xl font-bold">One platform for your whole school</p>
        <ul className="lp-muted mt-4 space-y-2 text-sm">
          <li className="flex items-center gap-2"><ClipboardCheck size={16} className="text-indigo-400" /> Attendance, exams and results</li>
          <li className="flex items-center gap-2"><Wallet size={16} className="text-fuchsia-400" /> Fees, payments and expenses</li>
          <li className="flex items-center gap-2"><ShieldCheck size={16} className="text-cyan-400" /> Secure, separate data for every school</li>
        </ul>
      </div>

      <div className="glass relative w-full max-w-md rounded-3xl p-8 shadow-2xl">
        <Link href="/" className="mb-6 flex items-center gap-2">
          <span className="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 text-white">
            <GraduationCap size={20} />
          </span>
          <span className="text-lg font-bold">EduVerse</span>
        </Link>
        <h1 className="text-2xl font-bold">{title}</h1>
        <p className="lp-muted mb-6 mt-1 text-sm">{subtitle}</p>
        {children}
      </div>
    </main>
  );
}
