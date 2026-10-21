import { ImageResponse } from "next/og";

export async function GET(_: Request, { params }: { params: Promise<{ size: string }> }) {
  const { size } = await params;
  const px = size === "192" ? 192 : 512;
  return new ImageResponse(
    (
      <div style={{
        width: "100%", height: "100%", display: "flex", alignItems: "center", justifyContent: "center",
        background: "linear-gradient(135deg,#4f46e5,#c026d3)", color: "white", fontSize: px * 0.5, fontWeight: 800,
      }}>
        E
      </div>
    ),
    { width: px, height: px },
  );
}
