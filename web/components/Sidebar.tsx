"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import * as Icons from "lucide-react";
import { resources } from "@/lib/resources";
import { logout } from "@/app/actions";

const groups = ["People", "Academics", "Exams", "Finance", "Communication"] as const;

export default function Sidebar({ slug, schoolName, userName }: { slug: string; schoolName: string; userName: string }) {
  const path = usePathname();
  const link = (href: string, label: string, Icon: React.ElementType) => {
    const active = path === href;
    return (
      <Link key={href} href={href}
        className={`flex items-center gap-3 rounded-xl px-3 py-2 text-sm transition ${
          active ? "bg-indigo-500/20 text-white" : "text-slate-400 hover:bg-white/5 hover:text-white"}`}>
        <Icon size={17} /> {label}
      </Link>
    );
  };

  return (
    <aside className="sticky top-0 hidden h-screen w-64 shrink-0 flex-col overflow-y-auto bg-[#0b0d24] p-4 lg:flex">
      <div className="mb-5 flex items-center gap-2 px-2 text-white">
        <span className="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-500">
          <Icons.GraduationCap size={20} />
        </span>
        <div className="min-w-0">
          <p className="truncate text-sm font-bold">{schoolName}</p>
          <p className="text-[11px] text-slate-500">EduVerse</p>
        </div>
      </div>
      <nav className="flex-1 space-y-4">
        <div>{link(`/${slug}/dashboard`, "Dashboard", Icons.LayoutDashboard)}</div>
        {groups.map((g) => (
          <div key={g}>
            <p className="mb-1 px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-600">{g}</p>
            {g === "People" && link(`/${slug}/attendance`, "Attendance", Icons.UserCheck)}
            {resources.filter((r) => r.group === g).map((r) => {
              const Icon = (Icons as unknown as Record<string, React.ElementType>)[r.icon] ?? Icons.Circle;
              return link(`/${slug}/${r.key}`, r.title, Icon);
            })}
          </div>
        ))}
      </nav>
      <form action={logout} className="mt-4 border-t border-white/10 pt-3">
        <p className="mb-2 truncate px-3 text-xs text-slate-500">{userName}</p>
        <button className="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-400 hover:bg-white/5 hover:text-white">
          <Icons.LogOut size={17} /> Logout
        </button>
      </form>
    </aside>
  );
}
