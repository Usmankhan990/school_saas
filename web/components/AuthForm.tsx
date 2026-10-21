"use client";

import { useActionState } from "react";
import Link from "next/link";
import { login, registerSchool, type FormState } from "@/app/actions";

export default function AuthForm({ mode }: { mode: "login" | "register" }) {
  const [state, action, pending] = useActionState<FormState, FormData>(
    mode === "login" ? login : registerSchool,
    undefined,
  );
  const isReg = mode === "register";

  return (
    <form action={action} className="space-y-4">
      {isReg && (
        <>
          <Field name="school_name" label="School name" placeholder="Green Valley School" />
          <Field name="slug" label="School URL (optional)" placeholder="green-valley" />
          <Field name="name" label="Your name" placeholder="Principal / Admin" />
        </>
      )}
      <Field name="email" type="email" label="Email" placeholder="you@school.com" />
      <Field name="password" type="password" label="Password" placeholder="••••••••" />
      {state?.error && (
        <p className="rounded-lg bg-red-500/15 px-3 py-2 text-sm text-red-600 dark:text-red-300">{state.error}</p>
      )}
      <button className="btn w-full" disabled={pending}>
        {pending ? "Please wait…" : isReg ? "Create my school" : "Sign in"}
      </button>
      <p className="text-center text-sm lp-muted">
        {isReg ? (
          <>Already registered? <Link href="/login" className="text-indigo-600 dark:text-indigo-300 hover:underline">Sign in</Link></>
        ) : (
          <>New school? <Link href="/register" className="text-indigo-600 dark:text-indigo-300 hover:underline">Register here</Link></>
        )}
      </p>
    </form>
  );
}

function Field({ name, label, type = "text", placeholder }: { name: string; label: string; type?: string; placeholder?: string }) {
  return (
    <label className="block">
      <span className="mb-1.5 block text-xs font-medium text-slate-600 dark:text-slate-300">{label}</span>
      <input
        name={name} type={type} placeholder={placeholder} required={name !== "slug"}
        className="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-slate-500"
      />
    </label>
  );
}
