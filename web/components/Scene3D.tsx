"use client";

import { Canvas, useThree } from "@react-three/fiber";
import { Stars, Environment } from "@react-three/drei";
import { useDark } from "./useDark";
import SchoolScene from "./SchoolScene";

export type Align = "left" | "center" | "right";

function World({ align, dark }: { align: Align; dark: boolean }) {
  const { viewport } = useThree();
  // On wide screens push the scene to one side so it never sits behind the text.
  const wide = viewport.width > 9;
  const x = wide ? (align === "right" ? 2.6 : align === "left" ? -2.6 : 0) : 0;
  const scale = wide ? 1 : Math.max(0.55, viewport.width / 9);
  return (
    <>
      <ambientLight intensity={dark ? 0.7 : 1} />
      <directionalLight position={[4, 6, 5]} intensity={dark ? 2.2 : 2.6} />
      <pointLight position={[-6, -3, 4]} intensity={50} color="#f0abfc" />
      {dark && <Stars radius={60} depth={40} count={2500} factor={3} fade speed={1} />}
      <Environment preset={dark ? "city" : "apartment"} />
      <group position={[x, 0, 0]} scale={scale}>
        <SchoolScene />
      </group>
    </>
  );
}

export default function Scene3D({ align = "center" }: { align?: Align }) {
  const dark = useDark();
  return (
    <Canvas camera={{ position: [0, 0.3, 9], fov: 50 }} dpr={[1, 1.75]}>
      <World align={align} dark={dark} />
    </Canvas>
  );
}
