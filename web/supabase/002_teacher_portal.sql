-- Teacher portal: self attendance, payslips, role-based access.
-- Safe to run more than once. Run in Supabase SQL Editor after schema.sql.

alter table schools add column if not exists timezone text default 'Asia/Karachi';
alter table schools add column if not exists late_after time default '08:30';

alter table teachers add column if not exists user_id uuid references profiles(id) on delete set null;
create unique index if not exists teachers_user_id_key on teachers(user_id) where user_id is not null;

create table if not exists teacher_attendance (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  teacher_id uuid not null references teachers(id) on delete cascade,
  date date not null,
  check_in timestamptz,
  check_out timestamptz,
  status text not null default 'present' check (status in ('present','late','absent','leave')),
  created_at timestamptz default now(),
  unique (teacher_id, date)
);

create table if not exists payslips (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  teacher_id uuid not null references teachers(id) on delete cascade,
  month text not null,                    -- 'YYYY-MM'
  basic_salary numeric(12,2) not null default 0,
  working_days int not null default 0,
  present_days int not null default 0,
  late_days int not null default 0,
  leave_days int not null default 0,
  absent_days int not null default 0,
  deduction numeric(12,2) not null default 0,
  bonus numeric(12,2) not null default 0,
  net_salary numeric(12,2) not null default 0,
  status text not null default 'unpaid' check (status in ('unpaid','paid')),
  paid_on date,
  generated_at timestamptz default now(),
  unique (teacher_id, month)
);

-- ───────────── Role-aware helpers ─────────────
-- Only ACTIVE profiles resolve to a school/role (pending sign-ups get nothing).
create or replace function public.my_school_id() returns uuid
language sql stable security definer set search_path = public as $$
  select school_id from profiles where id = auth.uid() and status = 'active'
$$;

create or replace function public.my_role() returns text
language sql stable security definer set search_path = public as $$
  select role from profiles where id = auth.uid() and status = 'active'
$$;

create or replace function public.my_teacher_id() returns uuid
language sql stable security definer set search_path = public as $$
  select t.id from teachers t join profiles p on p.id = t.user_id
  where t.user_id = auth.uid() and p.status = 'active'
$$;

create or replace function public.is_platform_admin() returns boolean
language sql stable security definer set search_path = public as $$
  select coalesce((select role = 'platform_admin' from profiles where id = auth.uid() and status = 'active'), false)
$$;

-- ───────────── Policies ─────────────
-- Admin-level access (school_admin / platform_admin) on every tenant table.
do $$
declare t text;
begin
  foreach t in array array[
    'academic_sessions','classes','sections','subjects','students','teachers','staff',
    'exams','date_sheets','exam_marks','attendance','fee_types','student_fees','payments',
    'expenses','notifications','homeworks','timetables','leaves','calendar_events',
    'teacher_attendance','payslips'
  ] loop
    execute format('alter table %I enable row level security', t);
    execute format('drop policy if exists tenant_all on %I', t);
    execute format(
      'create policy tenant_all on %I for all
         using (is_platform_admin() or (my_role() = ''school_admin'' and school_id = my_school_id()))
         with check (is_platform_admin() or (my_role() = ''school_admin'' and school_id = my_school_id()))', t);
  end loop;
end $$;

-- Teachers: read the academic basics of their school.
do $$
declare t text;
begin
  foreach t in array array[
    'academic_sessions','classes','sections','subjects','students','exams','date_sheets',
    'timetables','notifications','calendar_events','homeworks','exam_marks','attendance'
  ] loop
    execute format('drop policy if exists teacher_read on %I', t);
    execute format(
      'create policy teacher_read on %I for select
         using (my_role() = ''teacher'' and school_id = my_school_id())', t);
  end loop;
end $$;

-- Teachers: write student attendance, homework and marks.
do $$
declare t text;
begin
  foreach t in array array['attendance','homeworks','exam_marks'] loop
    execute format('drop policy if exists teacher_write on %I', t);
    execute format(
      'create policy teacher_write on %I for all
         using (my_role() = ''teacher'' and school_id = my_school_id())
         with check (my_role() = ''teacher'' and school_id = my_school_id())', t);
  end loop;
end $$;

-- Teachers: their own record, own attendance (read) and own payslips.
drop policy if exists teacher_self on teachers;
create policy teacher_self on teachers for select using (user_id = auth.uid() and my_role() = 'teacher');

drop policy if exists teacher_att_read on teacher_attendance;
create policy teacher_att_read on teacher_attendance for select
  using (teacher_id = my_teacher_id());

-- Check-in / check-out are written by the server (service role) with server time, so teachers
-- cannot edit these rows directly.
drop policy if exists teacher_att_insert on teacher_attendance;
drop policy if exists teacher_att_update on teacher_attendance;

drop policy if exists teacher_payslip_read on payslips;
create policy teacher_payslip_read on payslips for select using (teacher_id = my_teacher_id());
