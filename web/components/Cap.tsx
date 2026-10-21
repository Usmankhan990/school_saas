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
