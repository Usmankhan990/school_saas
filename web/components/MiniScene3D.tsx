"use client";

import { Canvas } from "@react-three/fiber";
import { Float, Environment } from "@react-three/drei";
import { Cap } from "./Cap";
import { useDark } from "./useDark";

const books = [
  { p: [-2.4, 0.9, 0], c: "#ef4444", r: [0.3, 0.5, 0.2] },
  { p: [2.5, 0.6, -0.5], c: "#22d3ee", r: [0.5, -0.4, 0.3] },
  { p: [-1.9, -1.1, 0.5], c: "#a855f7", r: [-0.2, 0.7, 0.4] },
  { p: [2.1, -1, 0.3], c: "#10b981", r: [0.4, 0.2, -0.5] },
] as const;

/** Compact 3D scene used in dashboard page headers. */
export default function MiniScene3D() {
  const dark = useDark();
  return (
    <Canvas camera={{ position: [0, 0, 7], fov: 45 }} dpr={[1, 1.5]}>
      <ambientLight intensity={dark ? 0.6 : 0.9} />
      <pointLight position={[5, 5, 5]} intensity={70} color="#a5b4fc" />
      <pointLight position={[-5, -3, 4]} intensity={50} color="#f0abfc" />
      <Environment preset={dark ? "city" : "apartment"} />
      <Float speed={2} rotationIntensity={0.6} floatIntensity={1}>
        <group scale={0.8}>
          <Cap />
        </group>
      </Float>
      {books.map((b, i) => (
        <Float key={i} speed={1.3 + i * 0.3} rotationIntensity={1.5} floatIntensity={2}>
          <mesh position={b.p as unknown as [number, number, number]} rotation={b.r as unknown as [number, number, number]} scale={0.8}>
            <boxGeometry args={[0.9, 0.2, 0.65]} />
            <meshStandardMaterial color={b.c} metalness={0.3} roughness={0.35} />
          </mesh>
        </Float>
      ))}
    </Canvas>
  );
}
