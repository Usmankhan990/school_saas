import type { Metadata, Viewport } from "next";
import PwaRegister from "@/components/PwaRegister";
import { Inter } from "next/font/google";
import "./globals.css";

const inter = Inter({ variable: "--font-inter", subsets: ["latin"] });

export const viewport: Viewport = {
  themeColor: "#4f46e5",
  viewportFit: "cover",
};

export const metadata: Metadata = {
  appleWebApp: { capable: true, title: "EduVerse", statusBarStyle: "black-translucent" },
  title: "EduVerse – School Management System",
  description: "Complete multi-school management platform: students, fees, exams, attendance and more.",
};

// Applies the saved/system theme before first paint to avoid a flash.
const themeScript = `try{var t=localStorage.getItem('theme');if(t==='dark'||(!t&&matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.classList.add('dark')}catch(e){}`;

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={inter.variable} suppressHydrationWarning>
      <head>
        <script dangerouslySetInnerHTML={{ __html: themeScript }} />
      </head>
      <body className="min-h-screen antialiased">
        {children}
        <PwaRegister />
      </body>
    </html>
  );
}
