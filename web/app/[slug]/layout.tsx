import { getTenant } from "@/lib/auth";
import Sidebar from "@/components/Sidebar";

export default async function TenantLayout({
  children, params,
}: { children: React.ReactNode; params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const { school, profile } = await getTenant(slug);
  return (
    <div className="flex min-h-screen">
      <Sidebar slug={slug} schoolName={school.name} userName={profile.name} />
      <main className="min-w-0 flex-1 p-4 sm:p-8">{children}</main>
    </div>
  );
}
