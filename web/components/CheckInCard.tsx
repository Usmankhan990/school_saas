"use client";

import { useActionState, useEffect, useState } from "react";
import { LogIn, LogOut, CheckCircle2 } from "lucide-react";
import { teacherCheckAction, type CheckState } from "@/app/teacher-actions";

function Clock({ timeZone }: { timeZone: string }) {
  const [now, setNow] = useState<Date | null>(null);
  useEffect(() => {
    const tick = () => setNow(new Date());
    tick();
    const id = setInterval(tick, 1000);
    return () => clearInterval(id);
  }, []);
  if (!now) return <span className="opacity-0">00:00:00</span>;
  return <span>{now.toLocaleTimeString("en-GB", { timeZone })}</span>;
}

export default function CheckInCard({
  slug, timeZone, checkedIn, checkedOut, checkInTime, checkOutTime, status, blockedReason,
}: {
  slug: string; timeZone: string; checkedIn: boolean; checkedOut: boolean;
  checkInTime: string; checkOutTime: string; status: string | null; blockedReason: string | null;
}) {
  const [inState, inAction, inPending] = useActionState<CheckState, FormData>(
    teacherCheckAction.bind(null, slug, "check_in") as (s: CheckState, f: FormData) => Promise<CheckState>, undefined);
  const [outState, outAction, outPending] = useActionState<CheckState, FormData>(
    teacherCheckAction.bind(null, slug, "check_out") as (s: CheckState, f: FormData) => Promise<CheckState>, undefined);
  const state = inState ?? outState;

  return (
    <div className="card relative overflow-hidden text-center">
      <div className="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-500/20 blur-3xl" />
      <p className="muted text-sm">{new Date().toLocaleDateString("en-US", { weekday: "long", day: "numeric", month: "long", timeZone })}</p>
      <p className="my-2 text-5xl font-extrabold tabular-nums"><Clock timeZone={timeZone} /></p>

      {blockedReason ? (
        <p className="rounded-xl bg-amber-500/15 px-4 py-3 text-sm text-amber-700 dark:text-amber-300">{blockedReason}</p>
      ) : checkedOut ? (
        <div className="mx-auto flex max-w-xs items-center justify-center gap-2 rounded-2xl bg-emerald-500/15 px-4 py-4 font-semibold text-emerald-700 dark:text-emerald-300">
          <CheckCircle2 /> Done for today
        </div>
      ) : checkedIn ? (
        <form action={outAction}>
          <button disabled={outPending}
            className="mx-auto flex w-full max-w-xs items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-rose-500 to-orange-500 px-6 py-5 text-lg font-bold text-white shadow-lg shadow-rose-500/30 transition active:scale-95 disabled:opacity-60">
            <LogOut /> {outPending ? "Saving…" : "Check out"}
          </button>
        </form>
      ) : (
        <form action={inAction}>
          <button disabled={inPending}
            className="mx-auto flex w-full max-w-xs items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-5 text-lg font-bold text-white shadow-lg shadow-emerald-500/30 transition active:scale-95 disabled:opacity-60">
            <LogIn /> {inPending ? "Saving…" : "Check in"}
          </button>
        </form>
      )}

      {state?.message && <p className="mt-3 text-sm text-emerald-600 dark:text-emerald-300">{state.message}</p>}
      {state?.error && <p className="mt-3 text-sm text-red-600">{state.error}</p>}

      <div className="mt-5 grid grid-cols-3 gap-2 text-sm">
        <div className="rounded-xl bg-black/5 py-2 dark:bg-white/5"><p className="muted text-xs">Check in</p><p className="font-semibold">{checkInTime}</p></div>
        <div className="rounded-xl bg-black/5 py-2 dark:bg-white/5"><p className="muted text-xs">Check out</p><p className="font-semibold">{checkOutTime}</p></div>
        <div className="rounded-xl bg-black/5 py-2 dark:bg-white/5"><p className="muted text-xs">Status</p><p className="font-semibold capitalize">{status ?? "—"}</p></div>
      </div>
    </div>
  );
}
