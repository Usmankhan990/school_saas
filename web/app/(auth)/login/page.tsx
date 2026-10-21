import AuthShell from "@/components/AuthShell";
import AuthForm from "@/components/AuthForm";

export default async function LoginPage({ searchParams }: { searchParams: Promise<{ signup?: string }> }) {
  const { signup } = await searchParams;
  return (
    <AuthShell title="Welcome back" subtitle="Sign in to your school, teacher or platform account.">
      {signup === "pending" && (
        <p className="mb-4 rounded-xl bg-emerald-500/15 px-3 py-2 text-sm text-emerald-700 dark:text-emerald-300">
          Account requested. Your school admin must approve it before you can sign in.
        </p>
      )}
      <AuthForm mode="login" />
    </AuthShell>
  );
}
