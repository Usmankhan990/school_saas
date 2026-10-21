import type { MetadataRoute } from "next";

export default function manifest(): MetadataRoute.Manifest {
  return {
    name: "EduVerse – School Management",
    short_name: "EduVerse",
    description: "Manage students, attendance, fees, exams and more.",
    start_url: "/login",
    display: "standalone",
    background_color: "#080a1c",
    theme_color: "#4f46e5",
    icons: [
      { src: "/pwa-icon/192", sizes: "192x192", type: "image/png" },
      { src: "/pwa-icon/512", sizes: "512x512", type: "image/png" },
      { src: "/pwa-icon/512", sizes: "512x512", type: "image/png", purpose: "maskable" },
    ],
  };
}
