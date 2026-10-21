"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import * as Icons from "lucide-react";
import { resources, teacherReadKeys } from "@/lib/resources";
import { logout } from "@/app/actions";
import ThemeToggle from "./ThemeToggle";

const groups = ["People", "Academics", "Exams", "Finance", "Communication"] as const;

export default function Sidebar({ slug, schoolName, userName, role }: { slug: string; schoolName: string; userName: string; role: string }) {
  const path = usePathname();
  const [open, setOpen] = useState(false);
  useEffect(() => {
    const t = () => setOpen((o) => !o);
    window.addEventListener("toggle-sidebar", t);
    return () => window.removeEventListener("toggle-sidebar", t);
  }, []);

  const link = (href: string, label: string, Icon: React.ElementType) => {
    const active = path === href;
    return (
      <Link key={href} href={href} onClick={() => setOpen(false)}
        className={`flex items-center gap-3 rounded-xl px-3 py-2 text-sm transition ${
          active
            ? "bg-indigo-500/15 font-medium text-indigo-700 dark:bg-indigo-500/20 dark:text-white"
            : "text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white"}`}>
        <Icon size={17} /> {label}
      </Link>
    );
  };

  const brand = (
    <div className="flex items-center gap-2 px-2">
      <span className="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 text-white">
        <Icons.GraduationCap size={20} />
      </span>
      <div className="min-w-0">
        <p className="truncate text-sm font-bold">{schoolName}</p>
        <p className="muted text-[11px]">EduVerse</p>
      </div>
    </div>
  );

  return (
    <>
      {/* mobile top bar */}
      <div className="fixed inset-x-0 top-0 z-30 flex items-center justify-between print:hidden border-b border-[var(--border)] bg-[var(--surface)] px-4 py-2.5 lg:hidden">
        {brand}
        <div className="flex items-center gap-2">
          <ThemeToggle />
          <button onClick={() => setOpen((o) => !o)} aria-label="Menu" className="glass grid h-9 w-9 place-items-center rounded-xl">
            {open ? <Icons.X size={18} /> : <Icons.Menu size={18} />}
          </button>
        </div>
      </div>

      <aside
        className={`print:hidden fixed inset-y-0 left-0 z-20 flex w-64 shrink-0 flex-col overflow-y-auto border-r border-[var(--border)] bg-[var(--surface)] p-4 pb-24 pt-16 transition-transform lg:pb-4 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:pt-4 ${
          open ? "translate-x-0" : "-translate-x-full"}`}
      >
        <div className="mb-5 hidden lg:block">{brand}</div>
        <nav className="flex-1 space-y-4">
          {role === "teacher" ? (
            <div className="space-y-0.5">
              {link(`/${slug}/teacher`, "My Day", Icons.Sun)}
              {link(`/${slug}/attendance`, "Student Attendance", Icons.UserCheck)}
              {link(`/${slug}/payslips`, "My Payslips", Icons.Banknote)}
              {resources.filter((r) => teacherReadKeys.includes(r.key) && r.key !== "academic-sessions").map((r) => {
                const Icon = (Icons as unknown as Record<string, React.ElementType>)[r.icon] ?? Icons.Circle;
                return link(`/${slug}/${r.key}`, r.title, Icon);
              })}
            </div>
          ) : (
            <>
              <div>{link(`/${slug}/dashboard`, "Dashboard", Icons.LayoutDashboard)}</div>
              {groups.map((g) => (
                <div key={g}>
                  <p className="muted mb-1 px-3 text-[10px] font-semibold uppercase tracking-wider">{g}</p>
                  {g === "People" && link(`/${slug}/attendance`, "Student Attendance", Icons.UserCheck)}
                  {g === "People" && link(`/${slug}/teacher-attendance`, "Teacher Attendance", Icons.Fingerprint)}
                  {g === "Finance" && link(`/${slug}/payslips`, "Payslips", Icons.Banknote)}
                  {resources.filter((r) => r.group === g).map((r) => {
                    const Icon = (Icons as unknown as Record<string, React.ElementType>)[r.icon] ?? Icons.Circle;
                    return link(`/${slug}/${r.key}`, r.title, Icon);
                  })}
                </div>
              ))}
            </>
          )}
        </nav>
        <div className="mt-4 border-t border-[var(--border)] pt-3">
          <div className="mb-2 flex items-center justify-between px-3">
            <p className="muted truncate text-xs">{userName}</p>
            <span className="hidden lg:block"><ThemeToggle /></span>
          </div>
          <form action={logout}>
            <button className="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white">
              <Icons.LogOut size={17} /> Logout
            </button>
          </form>
        </div>
      </aside>
      {open && <div className="fixed inset-0 z-10 bg-black/40 lg:hidden" onClick={() => setOpen(false)} />}
    </>
  );
}
