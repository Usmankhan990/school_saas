import Link from "next/link";
import { GraduationCap } from "lucide-react";

export default function AuthShell({ title, subtitle, children }: { title: string; subtitle: string; children: React.ReactNode }) {
  return (
    <main className="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#070816] px-4">
      <div className="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-indigo-600/30 blur-3xl" />
      <div className="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-fuchsia-600/25 blur-3xl" />
      <div className="glass relative w-full max-w-md rounded-3xl p-8 shadow-2xl">
        <Link href="/" className="mb-6 flex items-center gap-2 text-white">
          <span className="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-500">
            <GraduationCap size={20} />
          </span>
          <span className="text-lg font-bold">EduVerse</span>
        </Link>
        <h1 className="text-2xl font-bold text-white">{title}</h1>
        <p className="mb-6 mt-1 text-sm text-slate-400">{subtitle}</p>
        {children}
      </div>
    </main>
  );
}
