"use client";

import { useRef } from "react";
import { Canvas, useFrame } from "@react-three/fiber";
import { Float, Stars, MeshDistortMaterial, Environment } from "@react-three/drei";
import type { Group } from "three";
import { useDark } from "./useDark";

export function Cap() {
  return (
    <group rotation={[0.35, 0, 0]}>
      {/* board */}
      <mesh rotation={[0, Math.PI / 4, 0]}>
        <boxGeometry args={[2.6, 0.14, 2.6]} />
        <meshStandardMaterial color="#1e1b4b" metalness={0.6} roughness={0.25} />
      </mesh>
      {/* base */}
      <mesh position={[0, -0.55, 0]}>
        <cylinderGeometry args={[0.85, 1, 0.9, 32]} />
        <meshStandardMaterial color="#312e81" metalness={0.5} roughness={0.3} />
      </mesh>
      {/* button */}
      <mesh position={[0, 0.12, 0]}>
        <sphereGeometry args={[0.1, 16, 16]} />
        <meshStandardMaterial color="#fbbf24" metalness={0.9} roughness={0.2} />
      </mesh>
      {/* tassel */}
      <mesh position={[1.05, -0.45, 1.05]}>
        <cylinderGeometry args={[0.025, 0.025, 1.1, 8]} />
        <meshStandardMaterial color="#fbbf24" metalness={0.9} roughness={0.2} />
      </mesh>
      <mesh position={[1.05, -1.05, 1.05]}>
        <sphereGeometry args={[0.13, 16, 16]} />
        <meshStandardMaterial color="#f59e0b" metalness={0.8} roughness={0.3} />
      </mesh>
    </group>
  );
}

const books = [
  { p: [-3.4, 1.4, -1], c: "#ef4444", r: [0.3, 0.5, 0.2] },
  { p: [3.5, 1.1, -1.5], c: "#22d3ee", r: [0.5, -0.4, 0.3] },
  { p: [-3, -1.6, 0.5], c: "#a855f7", r: [-0.2, 0.7, 0.4] },
  { p: [3.2, -1.5, 0], c: "#10b981", r: [0.4, 0.2, -0.5] },
  { p: [0.2, 2.6, -2], c: "#f59e0b", r: [0.6, 0.3, 0.1] },
] as const;

function Rig({ children }: { children: React.ReactNode }) {
  const g = useRef<Group>(null);
  useFrame((state, dt) => {
    if (!g.current) return;
    g.current.rotation.y += dt * 0.25;
    // gentle mouse parallax
    g.current.position.x += (state.pointer.x * 0.6 - g.current.position.x) * 0.05;
    g.current.position.y += (state.pointer.y * 0.4 - g.current.position.y) * 0.05;
  });
  return <group ref={g}>{children}</group>;
}

export default function Scene3D() {
  const dark = useDark();
  return (
    <Canvas camera={{ position: [0, 0, 8], fov: 50 }} dpr={[1, 1.75]}>
      <ambientLight intensity={0.6} />
      <pointLight position={[6, 6, 6]} intensity={80} color="#a5b4fc" />
      <pointLight position={[-6, -3, 4]} intensity={60} color="#f0abfc" />
      {dark && <Stars radius={60} depth={40} count={2500} factor={3} fade speed={1} />}
      <Environment preset={dark ? "city" : "apartment"} />

      <Float speed={2} rotationIntensity={0.4} floatIntensity={1.2}>
        <Rig>
          <Cap />
        </Rig>
      </Float>

      <Float speed={1.5} floatIntensity={2}>
        <mesh position={[0, 0, -3]} scale={2.2}>
          <icosahedronGeometry args={[1, 6]} />
          <MeshDistortMaterial color="#6366f1" distort={0.45} speed={2} transparent opacity={dark ? 0.28 : 0.45} wireframe />
        </mesh>
      </Float>

      {books.map((b, i) => (
        <Float key={i} speed={1.2 + i * 0.3} rotationIntensity={1.5} floatIntensity={2.5}>
          <mesh position={b.p as unknown as [number, number, number]} rotation={b.r as unknown as [number, number, number]}>
            <boxGeometry args={[0.9, 0.2, 0.65]} />
            <meshStandardMaterial color={b.c} metalness={0.3} roughness={0.35} />
          </mesh>
        </Float>
      ))}

      <Float speed={2.5} floatIntensity={3}>
        <mesh position={[-1.8, -2.4, 1]} rotation={[1.2, 0.3, 0]}>
          <torusGeometry args={[0.45, 0.12, 16, 48]} />
          <meshStandardMaterial color="#f472b6" metalness={0.6} roughness={0.2} />
        </mesh>
      </Float>
    </Canvas>
  );
}
