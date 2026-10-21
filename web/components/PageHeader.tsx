"use client";

import dynamic from "next/dynamic";

const MiniScene3D = dynamic(() => import("./MiniScene3D"), { ssr: false });

/** Page title with a small interactive 3D scene on the right. */
export default function PageHeader({ title, subtitle }: { title: string; subtitle?: string }) {
  return (
    <div className="relative overflow-hidden rounded-3xl border border-[var(--border)] bg-gradient-to-br from-indigo-500/15 via-violet-500/10 to-fuchsia-500/15">
      <div className="absolute -right-10 -top-16 h-56 w-56 rounded-full bg-indigo-500/20 blur-3xl" />
      <div className="relative flex items-center justify-between gap-4 px-6 py-3 sm:px-8">
        <div className="min-w-0 py-4">
          <h1 className="text-2xl font-bold sm:text-3xl">{title}</h1>
          {subtitle && <p className="muted mt-1 text-sm">{subtitle}</p>}
        </div>
        <div className="h-32 w-56 shrink-0 sm:h-40 sm:w-80">
          <MiniScene3D />
        </div>
      </div>
    </div>
  );
}
