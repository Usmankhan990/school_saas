import AuthShell from "@/components/AuthShell";
import AuthForm from "@/components/AuthForm";

export default function RegisterPage() {
  return (
    <AuthShell title="Register your school" subtitle="Get your own dashboard in under a minute.">
      <AuthForm mode="register" />
    </AuthShell>
  );
}
