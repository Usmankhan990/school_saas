-- School Management SaaS - Supabase (Postgres) schema
-- Run this whole file in Supabase Dashboard > SQL Editor.

create extension if not exists "pgcrypto";

-- ───────────── Core ─────────────
create table if not exists schools (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  slug text not null unique,
  address text,
  phone text,
  principal_name text,
  currency text default 'PKR',
  logo_url text,
  status text default 'active',
  created_at timestamptz default now()
);

-- one row per auth user; school_id null => platform admin
create table if not exists profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  school_id uuid references schools(id) on delete cascade,
  name text not null,
  email text,
  role text not null default 'student'
    check (role in ('platform_admin','school_admin','teacher','staff','student','parent')),
  status text default 'active',
  created_at timestamptz default now()
);

create table if not exists academic_sessions (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  name text not null,
  start_date date not null,
  end_date date not null,
  is_current boolean default false,
  created_at timestamptz default now()
);

create table if not exists classes (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  name text not null,
  numeric_name int,
  created_at timestamptz default now()
);

create table if not exists sections (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  class_id uuid not null references classes(id) on delete cascade,
  name text not null,
  created_at timestamptz default now()
);

create table if not exists subjects (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  class_id uuid references classes(id) on delete set null,
  name text not null,
  code text,
  created_at timestamptz default now()
);

-- ───────────── People ─────────────
create table if not exists students (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  admission_no text not null,
  roll_no text,
  name text not null,
  father_name text,
  gender text,
  dob date,
  phone text,
  address text,
  class_id uuid references classes(id) on delete set null,
  section_id uuid references sections(id) on delete set null,
  admission_date date default current_date,
  status text default 'active',
  created_at timestamptz default now(),
  unique (school_id, admission_no)
);

create table if not exists teachers (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  name text not null,
  phone text,
  email text,
  qualification text,
  salary numeric(12,2) default 0,
  joining_date date,
  status text default 'active',
  created_at timestamptz default now()
);

create table if not exists staff (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  name text not null,
  phone text,
  designation text,
  salary numeric(12,2) default 0,
  status text default 'active',
  created_at timestamptz default now()
);

-- ───────────── Exams & results ─────────────
create table if not exists exams (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  name text not null,
  start_date date,
  end_date date,
  created_at timestamptz default now()
);

create table if not exists date_sheets (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  exam_id uuid not null references exams(id) on delete cascade,
  class_id uuid references classes(id) on delete cascade,
  subject_id uuid references subjects(id) on delete cascade,
  exam_date date not null,
  start_time time,
  end_time time,
  created_at timestamptz default now()
);

create table if not exists exam_marks (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  exam_id uuid not null references exams(id) on delete cascade,
  student_id uuid not null references students(id) on delete cascade,
  subject_id uuid not null references subjects(id) on delete cascade,
  total_marks numeric(6,2) default 100,
  obtained_marks numeric(6,2) default 0,
  created_at timestamptz default now(),
  unique (exam_id, student_id, subject_id)
);

-- ───────────── Attendance ─────────────
create table if not exists attendance (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  student_id uuid not null references students(id) on delete cascade,
  date date not null default current_date,
  status text not null default 'present' check (status in ('present','absent','late','leave')),
  created_at timestamptz default now(),
  unique (student_id, date)
);

-- ───────────── Fees & finance ─────────────
create table if not exists fee_types (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  name text not null,
  amount numeric(12,2) not null default 0,
  frequency text default 'monthly',
  created_at timestamptz default now()
);

create table if not exists student_fees (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  student_id uuid not null references students(id) on delete cascade,
  fee_type_id uuid references fee_types(id) on delete set null,
  title text not null,
  amount numeric(12,2) not null,
  due_date date,
  status text default 'unpaid' check (status in ('unpaid','partial','paid')),
  created_at timestamptz default now()
);

create table if not exists payments (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  student_fee_id uuid references student_fees(id) on delete set null,
  student_id uuid not null references students(id) on delete cascade,
  amount numeric(12,2) not null,
  method text default 'cash',
  paid_on date default current_date,
  note text,
  created_at timestamptz default now()
);

create table if not exists expenses (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  title text not null,
  amount numeric(12,2) not null,
  spent_on date default current_date,
  category text,
  created_at timestamptz default now()
);

-- ───────────── Communication ─────────────
create table if not exists notifications (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  title text not null,
  message text,
  audience text default 'all',
  created_at timestamptz default now()
);

create table if not exists homeworks (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  class_id uuid references classes(id) on delete cascade,
  subject_id uuid references subjects(id) on delete set null,
  title text not null,
  description text,
  due_date date,
  created_at timestamptz default now()
);

create table if not exists timetables (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  class_id uuid references classes(id) on delete cascade,
  subject_id uuid references subjects(id) on delete set null,
  teacher_id uuid references teachers(id) on delete set null,
  day text not null,
  start_time time,
  end_time time,
  created_at timestamptz default now()
);

create table if not exists leaves (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  applicant text not null,
  reason text,
  from_date date,
  to_date date,
  status text default 'pending',
  created_at timestamptz default now()
);

create table if not exists calendar_events (
  id uuid primary key default gen_random_uuid(),
  school_id uuid not null references schools(id) on delete cascade,
  title text not null,
  event_date date not null,
  description text,
  created_at timestamptz default now()
);

-- ───────────── Row Level Security ─────────────
create or replace function public.my_school_id() returns uuid
language sql stable security definer set search_path = public as $$
  select school_id from profiles where id = auth.uid()
$$;

create or replace function public.is_platform_admin() returns boolean
language sql stable security definer set search_path = public as $$
  select coalesce((select role = 'platform_admin' from profiles where id = auth.uid()), false)
$$;

alter table schools enable row level security;
alter table profiles enable row level security;

drop policy if exists schools_read on schools;
create policy schools_read on schools for select using (true);          -- slug lookup for login pages
drop policy if exists schools_write on schools;
create policy schools_write on schools for all
  using (is_platform_admin()) with check (is_platform_admin());

drop policy if exists profiles_read on profiles;
create policy profiles_read on profiles for select
  using (id = auth.uid() or is_platform_admin() or school_id = my_school_id());
drop policy if exists profiles_write on profiles;
create policy profiles_write on profiles for all
  using (is_platform_admin()) with check (is_platform_admin());

do $$
declare t text;
begin
  foreach t in array array[
    'academic_sessions','classes','sections','subjects','students','teachers','staff',
    'exams','date_sheets','exam_marks','attendance','fee_types','student_fees','payments',
    'expenses','notifications','homeworks','timetables','leaves','calendar_events'
  ] loop
    execute format('alter table %I enable row level security', t);
    execute format('drop policy if exists tenant_all on %I', t);
    execute format(
      'create policy tenant_all on %I for all
         using (is_platform_admin() or school_id = my_school_id())
         with check (is_platform_admin() or school_id = my_school_id())', t);
  end loop;
end $$;
