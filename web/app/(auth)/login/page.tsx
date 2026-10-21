import AuthShell from "@/components/AuthShell";
import AuthForm from "@/components/AuthForm";

export default function LoginPage() {
  return (
    <AuthShell title="Welcome back" subtitle="Sign in to your school or platform account.">
      <AuthForm mode="login" />
    </AuthShell>
  );
}
