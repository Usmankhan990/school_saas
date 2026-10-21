"use client";

import { useActionState } from "react";
import Link from "next/link";
import { login, registerSchool, type FormState } from "@/app/actions";
import { teacherSignUp } from "@/app/teacher-actions";

type Mode = "login" | "register" | "teacher";

export default function AuthForm({ mode }: { mode: Mode }) {
  const [state, action, pending] = useActionState<FormState, FormData>(
    mode === "login" ? login : mode === "register" ? registerSchool : teacherSignUp,
    undefined,
  );

  return (
    <form action={action} className="space-y-4">
      {mode === "register" && (
        <>
          <Field name="school_name" label="School name" placeholder="Green Valley School" />
          <Field name="slug" label="School URL (optional)" placeholder="green-valley" />
          <Field name="name" label="Your name" placeholder="Principal / Admin" />
        </>
      )}
      {mode === "teacher" && (
        <>
          <Field name="school_code" label="School code" placeholder="green-valley (ask your school)" />
          <Field name="name" label="Your full name" placeholder="Teacher name" />
          <Field name="phone" label="Phone (optional)" placeholder="03xx xxxxxxx" optional />
        </>
      )}
      <Field name="email" type="email" label="Email" placeholder="you@school.com" />
      <Field name="password" type="password" label="Password" placeholder="••••••••" />
      {state?.error && (
        <p className="rounded-lg bg-red-500/15 px-3 py-2 text-sm text-red-600 dark:text-red-300">{state.error}</p>
      )}
      <button className="btn w-full" disabled={pending}>
        {pending ? "Please wait…" : mode === "register" ? "Create my school" : mode === "teacher" ? "Request teacher account" : "Sign in"}
      </button>

      <div className="lp-muted space-y-1 text-center text-sm">
        {mode === "login" ? (
          <>
            <p>New school? <Link href="/register" className="text-indigo-600 hover:underline dark:text-indigo-300">Register here</Link></p>
            <p>Teacher? <Link href="/register/teacher" className="text-indigo-600 hover:underline dark:text-indigo-300">Create your account</Link></p>
          </>
        ) : (
          <p>Already have an account? <Link href="/login" className="text-indigo-600 hover:underline dark:text-indigo-300">Sign in</Link></p>
        )}
      </div>
    </form>
  );
}

function Field({ name, label, type = "text", placeholder, optional }: {
  name: string; label: string; type?: string; placeholder?: string; optional?: boolean;
}) {
  return (
    <label className="block">
      <span className="mb-1.5 block text-xs font-medium text-slate-600 dark:text-slate-300">{label}</span>
      <input
        name={name} type={type} placeholder={placeholder} required={name !== "slug" && !optional}
        className="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-slate-500"
      />
    </label>
  );
}
