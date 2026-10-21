"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import { Float } from "@react-three/drei";
import type { Group } from "three";
import { Cap } from "./Cap";

type V3 = [number, number, number];

const Box = ({ p, s, c, r, emissive }: { p: V3; s: V3; c: string; r?: V3; emissive?: string }) => (
  <mesh position={p} rotation={r}>
    <boxGeometry args={s} />
    <meshStandardMaterial color={c} roughness={0.55} metalness={0.05} emissive={emissive ?? "#000"} emissiveIntensity={emissive ? 0.5 : 0} />
  </mesh>
);

function Tree({ p, s = 1 }: { p: V3; s?: number }) {
  return (
    <group position={p} scale={s}>
      <mesh position={[0, 0.2, 0]}>
        <cylinderGeometry args={[0.07, 0.09, 0.4, 8]} />
        <meshStandardMaterial color="#7c4a21" roughness={0.8} />
      </mesh>
      <mesh position={[0, 0.65, 0]}>
        <coneGeometry args={[0.33, 0.6, 10]} />
        <meshStandardMaterial color="#16a34a" roughness={0.7} />
      </mesh>
      <mesh position={[0, 0.98, 0]}>
        <coneGeometry args={[0.25, 0.5, 10]} />
        <meshStandardMaterial color="#22c55e" roughness={0.7} />
      </mesh>
    </group>
  );
}

function Bus({ p }: { p: V3 }) {
  return (
    <group position={p}>
      <Box p={[0, 0.2, 0]} s={[1.2, 0.36, 0.44]} c="#facc15" />
      <Box p={[0.1, 0.4, 0]} s={[0.9, 0.12, 0.44]} c="#eab308" />
      {[-0.4, -0.15, 0.1, 0.35].map((x) => (
        <Box key={x} p={[x, 0.27, 0.225]} s={[0.17, 0.14, 0.02]} c="#bae6fd" />
      ))}
      <Box p={[0.62, 0.18, 0]} s={[0.1, 0.2, 0.4]} c="#facc15" />
      {[-0.4, 0.4].map((x) => (
        <group key={x}>
          {[0.23, -0.23].map((z) => (
            <mesh key={z} position={[x, 0.04, z]} rotation={[Math.PI / 2, 0, 0]}>
              <cylinderGeometry args={[0.1, 0.1, 0.07, 16]} />
              <meshStandardMaterial color="#1f2937" roughness={0.6} />
            </mesh>
          ))}
        </group>
      ))}
    </group>
  );
}

function Building() {
  const windows: V3[] = [];
  for (const x of [-1.35, -0.95, 0.95, 1.35]) windows.push([x, 0.5, 0.605]);
  return (
    <group>
      {/* main body + roof */}
      <Box p={[0, 0.55, 0]} s={[3.3, 1.1, 1.2]} c="#fde7b0" />
      <Box p={[0, 1.13, 0]} s={[3.45, 0.14, 1.35]} c="#b91c1c" />
      {/* central tower */}
      <Box p={[0, 1.65, 0]} s={[1.05, 1.0, 1.0]} c="#fcd9a0" />
      <mesh position={[0, 2.5, 0]} rotation={[0, Math.PI / 4, 0]}>
        <coneGeometry args={[0.95, 0.75, 4]} />
        <meshStandardMaterial color="#b91c1c" roughness={0.5} />
      </mesh>
      {/* flag */}
      <mesh position={[0, 3.1, 0]}>
        <cylinderGeometry args={[0.02, 0.02, 0.6, 8]} />
        <meshStandardMaterial color="#e5e7eb" metalness={0.5} />
      </mesh>
      <Box p={[0.17, 3.25, 0]} s={[0.32, 0.18, 0.02]} c="#22c55e" />
      {/* clock */}
      <mesh position={[0, 1.8, 0.51]} rotation={[Math.PI / 2, 0, 0]}>
        <cylinderGeometry args={[0.28, 0.28, 0.05, 32]} />
        <meshStandardMaterial color="#ffffff" roughness={0.3} />
      </mesh>
      <Box p={[0, 1.86, 0.54]} s={[0.025, 0.2, 0.02]} c="#111827" />
      <Box p={[0.06, 1.8, 0.54]} s={[0.14, 0.025, 0.02]} c="#111827" />
      {/* tower windows */}
      <Box p={[-0.3, 1.35, 0.505]} s={[0.2, 0.22, 0.03]} c="#7dd3fc" emissive="#38bdf8" />
      <Box p={[0.3, 1.35, 0.505]} s={[0.2, 0.22, 0.03]} c="#7dd3fc" emissive="#38bdf8" />
      {/* door + steps */}
      <Box p={[0, 0.34, 0.61]} s={[0.5, 0.68, 0.05]} c="#7c4a21" />
      <Box p={[0, 0.03, 0.78]} s={[0.95, 0.07, 0.3]} c="#d1d5db" />
      <Box p={[0, 0.1, 0.72]} s={[0.8, 0.07, 0.2]} c="#e5e7eb" />
      {/* wing windows (two rows) */}
      {windows.map((w, i) => (
        <group key={i}>
          <Box p={w} s={[0.3, 0.32, 0.03]} c="#7dd3fc" emissive="#38bdf8" />
          <Box p={[w[0], 0.52, 0.625]} s={[0.34, 0.02, 0.03]} c="#ffffff" />
        </group>
      ))}
    </group>
  );
}

function Globe({ p }: { p: V3 }) {
  return (
    <group position={p} rotation={[0.4, 0, 0.3]}>
      <mesh>
        <sphereGeometry args={[0.38, 32, 32]} />
        <meshStandardMaterial color="#38bdf8" roughness={0.4} />
      </mesh>
      <mesh position={[0.1, 0.1, 0.3]}>
        <sphereGeometry args={[0.16, 16, 16]} />
        <meshStandardMaterial color="#4ade80" roughness={0.6} />
      </mesh>
      <mesh rotation={[Math.PI / 2.4, 0, 0]}>
        <torusGeometry args={[0.52, 0.02, 12, 48]} />
        <meshStandardMaterial color="#fbbf24" metalness={0.8} roughness={0.3} />
      </mesh>
    </group>
  );
}

function Books({ p }: { p: V3 }) {
  return (
    <group position={p}>
      <Box p={[0, 0, 0]} s={[0.9, 0.18, 0.65]} c="#ef4444" />
      <Box p={[0.04, 0.18, 0.02]} s={[0.85, 0.16, 0.6]} c="#3b82f6" r={[0, 0.2, 0]} />
      <Box p={[-0.03, 0.34, 0]} s={[0.8, 0.15, 0.58]} c="#a855f7" r={[0, -0.15, 0]} />
    </group>
  );
}

function Pencil({ p }: { p: V3 }) {
  return (
    <group position={p} rotation={[0, 0, -0.9]}>
      <mesh>
        <cylinderGeometry args={[0.08, 0.08, 1.2, 6]} />
        <meshStandardMaterial color="#facc15" roughness={0.5} />
      </mesh>
      <mesh position={[0, -0.72, 0]} rotation={[Math.PI, 0, 0]}>
        <coneGeometry args={[0.08, 0.25, 6]} />
        <meshStandardMaterial color="#fde7b0" roughness={0.6} />
      </mesh>
      <mesh position={[0, -0.84, 0]} rotation={[Math.PI, 0, 0]}>
        <coneGeometry args={[0.03, 0.08, 6]} />
        <meshStandardMaterial color="#111827" />
      </mesh>
      <mesh position={[0, 0.65, 0]}>
        <cylinderGeometry args={[0.085, 0.085, 0.14, 12]} />
        <meshStandardMaterial color="#f472b6" roughness={0.5} />
      </mesh>
    </group>
  );
}

function Apple({ p }: { p: V3 }) {
  return (
    <group position={p}>
      <mesh>
        <sphereGeometry args={[0.28, 24, 24]} />
        <meshStandardMaterial color="#ef4444" roughness={0.35} />
      </mesh>
      <mesh position={[0, 0.3, 0]}>
        <cylinderGeometry args={[0.015, 0.015, 0.14, 6]} />
        <meshStandardMaterial color="#7c4a21" />
      </mesh>
      <mesh position={[0.1, 0.32, 0]} rotation={[0, 0, -0.6]} scale={[1, 0.4, 0.6]}>
        <sphereGeometry args={[0.1, 12, 12]} />
        <meshStandardMaterial color="#22c55e" />
      </mesh>
    </group>
  );
}

/** Gently swings left/right so the building front stays visible. */
function Swing({ children }: { children: React.ReactNode }) {
  const g = useRef<Group>(null);
  useFrame((state) => {
    if (!g.current) return;
    const t = state.clock.elapsedTime;
    g.current.rotation.y = Math.sin(t * 0.45) * 0.55 - 0.15 + state.pointer.x * 0.25;
    g.current.rotation.x = 0.12 + state.pointer.y * 0.08;
  });
  return <group ref={g}>{children}</group>;
}

/** Low-poly school campus: building, bus, trees, plus floating school items. */
export default function SchoolScene() {
  return (
    <group>
      <Swing>
        <group position={[0, -1.7, 0]} scale={0.95}>
          {/* ground */}
          <mesh position={[0, -0.12, 0.3]}>
            <cylinderGeometry args={[3.3, 3.45, 0.24, 48]} />
            <meshStandardMaterial color="#4ade80" roughness={0.9} />
          </mesh>
          <mesh position={[0, -0.3, 0.3]}>
            <cylinderGeometry args={[3.45, 3.0, 0.2, 48]} />
            <meshStandardMaterial color="#92400e" roughness={0.9} />
          </mesh>
          {/* path */}
          <Box p={[0, 0.005, 1.7]} s={[0.7, 0.02, 2.2]} c="#e5e7eb" />
          <Building />
          <Tree p={[-2.4, 0, 0.6]} s={1.1} />
          <Tree p={[2.5, 0, 0.3]} s={1.2} />
          <Tree p={[-2.0, 0, 1.7]} s={0.8} />
          <Tree p={[2.1, 0, 1.9]} s={0.9} />
          <Bus p={[1.5, 0, 2.1]} />
        </group>
      </Swing>

      {/* graduation cap hovering above the school */}
      <Float speed={2} floatIntensity={1.2} rotationIntensity={0.3}>
        <group position={[0, 2.35, 0]} scale={0.42}>
          <Cap />
        </group>
      </Float>

      {/* floating school items */}
      <Float speed={1.6} floatIntensity={2} rotationIntensity={0.8}>
        <Books p={[1.9, 2.35, 0.4]} />
      </Float>
      <Float speed={1.9} floatIntensity={2.4} rotationIntensity={0.6}>
        <Pencil p={[3.2, 0.9, 0.2]} />
      </Float>
      <Float speed={1.4} floatIntensity={2} rotationIntensity={0.5}>
        <Globe p={[3.0, -0.8, 1.2]} />
      </Float>
      <Float speed={2.1} floatIntensity={2.6} rotationIntensity={0.9}>
        <Apple p={[-0.95, -1.2, 2.7]} />
      </Float>
    </group>
  );
}
