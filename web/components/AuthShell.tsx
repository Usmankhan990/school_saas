import Link from "next/link";
import { GraduationCap } from "lucide-react";
import ThemeToggle from "./ThemeToggle";

export default function AuthShell({ title, subtitle, children }: { title: string; subtitle: string; children: React.ReactNode }) {
  return (
    <main className="relative flex min-h-screen items-center justify-center overflow-hidden bg-[var(--lp-bg)] px-4 text-[var(--lp-fg)]">
      <div className="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-indigo-500/30 blur-3xl" />
      <div className="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-fuchsia-500/25 blur-3xl" />
      <ThemeToggle className="absolute right-4 top-4" />
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
