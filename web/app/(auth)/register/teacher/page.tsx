import AuthShell from "@/components/AuthShell";
import AuthForm from "@/components/AuthForm";

export default function TeacherSignUpPage() {
  return (
    <AuthShell title="Teacher sign up" subtitle="Request an account. Your school admin approves it, then you can sign in on your phone.">
      <AuthForm mode="teacher" />
    </AuthShell>
  );
}
