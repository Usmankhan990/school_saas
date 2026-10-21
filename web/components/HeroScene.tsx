"use client";

import dynamic from "next/dynamic";

// Three.js needs the browser, so load it client-side only.
const Scene3D = dynamic(() => import("./Scene3D"), { ssr: false });

export default function HeroScene() {
  return <Scene3D />;
}
