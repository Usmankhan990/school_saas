"use client";

import { useRef } from "react";
import { motion } from "framer-motion";

/** 3D tilt-on-hover card with a moving glare. Works in light and dark themes. */
export default function TiltCard({
  children, delay = 0, className = "p-6", tilt = 14,
}: { children: React.ReactNode; delay?: number; className?: string; tilt?: number }) {
  const ref = useRef<HTMLDivElement>(null);

  const move = (e: React.MouseEvent) => {
    const el = ref.current;
    if (!el) return;
    const r = el.getBoundingClientRect();
    const x = (e.clientX - r.left) / r.width - 0.5;
    const y = (e.clientY - r.top) / r.height - 0.5;
    el.style.transform = `perspective(700px) rotateY(${x * tilt}deg) rotateX(${-y * tilt}deg) translateZ(8px)`;
    el.style.setProperty("--gx", `${(x + 0.5) * 100}%`);
    el.style.setProperty("--gy", `${(y + 0.5) * 100}%`);
  };
  const leave = () => {
    if (ref.current) ref.current.style.transform = "perspective(700px) rotateY(0) rotateX(0)";
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 30 }} whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-40px" }} transition={{ duration: 0.55, delay }}
      className="h-full"
    >
      <div
        ref={ref} onMouseMove={move} onMouseLeave={leave}
        className={`glass relative h-full overflow-hidden rounded-3xl shadow-sm transition-transform duration-200 ease-out ${className}`}
        style={{ background: "radial-gradient(circle at var(--gx,50%) var(--gy,0%), rgba(129,140,248,.2), var(--glass-bg) 60%)" }}
      >
        {children}
      </div>
    </motion.div>
  );
}
