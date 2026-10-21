"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { LayoutDashboard, GraduationCap, UserCheck, Wallet, Menu } from "lucide-react";

/** Mobile-only bottom navigation (like a native app). */
export default function BottomNav({ slug }: { slug: string }) {
  const path = usePathname();
  const items = [
    { href: `/${slug}/dashboard`, label: "Home", Icon: LayoutDashboard },
    { href: `/${slug}/students`, label: "Students", Icon: GraduationCap },
    { href: `/${slug}/attendance`, label: "Attendance", Icon: UserCheck },
    { href: `/${slug}/payments`, label: "Payments", Icon: Wallet },
  ];
  return (
    <nav className="fixed inset-x-0 bottom-0 z-30 flex border-t border-[var(--border)] bg-[var(--surface)] pb-[env(safe-area-inset-bottom)] lg:hidden">
      {items.map(({ href, label, Icon }) => (
        <Link key={href} href={href}
          className={`flex flex-1 flex-col items-center gap-0.5 py-2 text-[11px] ${path === href ? "text-indigo-600 dark:text-indigo-300" : "muted"}`}>
          <Icon size={20} /> {label}
        </Link>
      ))}
      <button onClick={() => window.dispatchEvent(new Event("toggle-sidebar"))}
        className="muted flex flex-1 flex-col items-center gap-0.5 py-2 text-[11px]">
        <Menu size={20} /> More
      </button>
    </nav>
  );
}
