"use client";

import { Moon, Sun } from "lucide-react";

export default function ThemeToggle({ className = "" }: { className?: string }) {
  const toggle = () => {
    const dark = document.documentElement.classList.toggle("dark");
    try { localStorage.setItem("theme", dark ? "dark" : "light"); } catch {}
  };
  return (
    <button
      onClick={toggle} aria-label="Toggle light / dark theme" title="Toggle theme"
      className={`glass grid h-9 w-9 place-items-center rounded-xl transition hover:scale-105 ${className}`}
    >
      <Sun size={17} className="hidden dark:block" />
      <Moon size={17} className="dark:hidden" />
    </button>
  );
}
