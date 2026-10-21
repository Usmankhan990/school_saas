# EduVerse – School Management System (Next.js + Supabase + Vercel)

Multi-school SaaS: har school ka apna dashboard (`/{slug}/dashboard`), data Supabase Row Level Security se alag.
Online classes is version mein nahi hain.

## Setup

1. **Supabase project** banayein → *SQL Editor* mein [supabase/schema.sql](supabase/schema.sql) poora run karein.
   Phir [supabase/002_teacher_portal.sql](supabase/002_teacher_portal.sql) run karein (teacher attendance, payslips, role-based access).
2. *Authentication → Providers → Email*: chahein to "Confirm email" band kar dein (app khud users confirmed banata hai).
3. `.env.local.example` ko `.env.local` copy karke keys bharein (Project Settings → API):
   - `NEXT_PUBLIC_SUPABASE_URL`, `NEXT_PUBLIC_SUPABASE_ANON_KEY`
   - `SUPABASE_SERVICE_ROLE_KEY` (secret, sirf server par)
   - `CRON_SECRET` (koi bhi lambi random string; monthly payslip job ko protect karta hai)
4. **Platform admin** banayein: Supabase *Authentication → Users → Add user* (email + password), phir SQL Editor mein:
   ```sql
   insert into profiles (id, name, email, role)
   select id, 'Platform Admin', email, 'platform_admin' from auth.users where email = 'YOUR@EMAIL';
   ```
5. Chalayein:
   ```bash
   npm install
   npm run dev
   ```

## Vercel par deploy

1. Repo GitHub par push karein, Vercel mein import karein. **Root Directory = `web`** set karein.
2. Environment Variables mein upar wali 3 keys add karein, Deploy.

## Use

- `/register` – naya school + admin account
- `/login` – school admin / platform admin
- `/platform` – platform admin: schools add / activate / deactivate
- `/{slug}/dashboard` – school dashboard; sidebar se Students, Teachers, Staff, Classes, Sections, Subjects, Sessions, Timetable, Homework, Exams, Date Sheets, Results, Attendance, Fees, Payments, Expenses, Notifications, Leaves, Events

## Teacher portal

- **Login banana (admin):** Teachers page par email + *Login password* bharein. Ya teacher khud `/register/teacher` par sign up kare (school code = school ka URL slug); admin Teachers page par Approve karta hai.
- **Teacher:** `/login` se `/{slug}/teacher` par jata hai. **Check in / Check out** (server ka time; `late_after` ke baad late, default 08:30, timezone default Asia/Karachi), student attendance, homework, payslips.
- **Payslips:** har mahine ki 1 tareekh ko Vercel cron (`vercel.json`, `/api/cron/payslips`) khud banata hai, ya Payslips page par Generate. Sunday off; har absent / unmarked working day ki katoti = salary ÷ working days. Payslip page se Print / PDF.

Naya module chahiye to sirf [lib/resources.ts](lib/resources.ts) mein entry add karein (aur table schema.sql mein).
