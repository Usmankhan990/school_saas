import { getTenant } from "@/lib/auth";
import Sidebar from "@/components/Sidebar";
import BottomNav from "@/components/BottomNav";

export default async function TenantLayout({
  children, params,
}: { children: React.ReactNode; params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const { school, profile } = await getTenant(slug);
  return (
    <div className="flex min-h-screen">
      <Sidebar slug={slug} schoolName={school.name} userName={profile.name} />
      <main className="min-w-0 flex-1 p-4 pb-24 pt-20 sm:p-8 sm:pb-24 sm:pt-20 lg:pb-8 lg:pt-8">{children}</main>
      <BottomNav slug={slug} />
    </div>
  );
}
