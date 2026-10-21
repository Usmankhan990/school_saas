export type Field = {
  name: string;
  label: string;
  type?: "text" | "number" | "date" | "time" | "textarea" | "select" | "fk";
  options?: string[];
  fk?: { table: string; label: string }; // foreign key lookup
  required?: boolean;
};

export type Resource = {
  key: string; // url segment
  table: string;
  title: string;
  group: "Academics" | "People" | "Exams" | "Finance" | "Communication";
  icon: string;
  columns: string[]; // field names shown in list
  fields: Field[];
  order?: string;
};

const fk = (name: string, label: string, table: string, labelCol = "name"): Field => ({
  name, label, type: "fk", fk: { table, label: labelCol },
});

const active = ["active", "inactive"];

export const resources: Resource[] = [
  {
    key: "students", table: "students", title: "Students", group: "People", icon: "GraduationCap",
    columns: ["admission_no", "name", "father_name", "class_id", "section_id", "status"],
    fields: [
      { name: "admission_no", label: "Admission No", required: true },
      { name: "roll_no", label: "Roll No" },
      { name: "name", label: "Student Name", required: true },
      { name: "father_name", label: "Father Name" },
      { name: "gender", label: "Gender", type: "select", options: ["male", "female", "other"] },
      { name: "dob", label: "Date of Birth", type: "date" },
      { name: "phone", label: "Phone" },
      fk("class_id", "Class", "classes"),
      fk("section_id", "Section", "sections"),
      { name: "address", label: "Address", type: "textarea" },
      { name: "status", label: "Status", type: "select", options: ["active", "inactive", "left"] },
    ],
  },
  {
    key: "teachers", table: "teachers", title: "Teachers", group: "People", icon: "UserRound",
    columns: ["name", "phone", "qualification", "salary", "status"],
    fields: [
      { name: "name", label: "Name", required: true },
      { name: "phone", label: "Phone" },
      { name: "email", label: "Email" },
      { name: "qualification", label: "Qualification" },
      { name: "salary", label: "Salary", type: "number" },
      { name: "joining_date", label: "Joining Date", type: "date" },
      { name: "status", label: "Status", type: "select", options: active },
    ],
  },
  {
    key: "staff", table: "staff", title: "Staff", group: "People", icon: "Briefcase",
    columns: ["name", "designation", "phone", "salary", "status"],
    fields: [
      { name: "name", label: "Name", required: true },
      { name: "designation", label: "Designation" },
      { name: "phone", label: "Phone" },
      { name: "salary", label: "Salary", type: "number" },
      { name: "status", label: "Status", type: "select", options: active },
    ],
  },
  {
    key: "classes", table: "classes", title: "Classes", group: "Academics", icon: "School",
    columns: ["name", "numeric_name"],
    fields: [
      { name: "name", label: "Class Name", required: true },
      { name: "numeric_name", label: "Numeric Order", type: "number" },
    ],
    order: "numeric_name",
  },
  {
    key: "sections", table: "sections", title: "Sections", group: "Academics", icon: "LayoutGrid",
    columns: ["name", "class_id"],
    fields: [fk("class_id", "Class", "classes"), { name: "name", label: "Section Name", required: true }],
  },
  {
    key: "subjects", table: "subjects", title: "Subjects", group: "Academics", icon: "BookOpen",
    columns: ["name", "code", "class_id"],
    fields: [
      { name: "name", label: "Subject", required: true },
      { name: "code", label: "Code" },
      fk("class_id", "Class", "classes"),
    ],
  },
  {
    key: "academic-sessions", table: "academic_sessions", title: "Academic Sessions", group: "Academics", icon: "CalendarRange",
    columns: ["name", "start_date", "end_date", "is_current"],
    fields: [
      { name: "name", label: "Session (e.g. 2026-27)", required: true },
      { name: "start_date", label: "Start Date", type: "date", required: true },
      { name: "end_date", label: "End Date", type: "date", required: true },
      { name: "is_current", label: "Current?", type: "select", options: ["true", "false"] },
    ],
    order: "start_date",
  },
  {
    key: "timetable", table: "timetables", title: "Timetable", group: "Academics", icon: "Clock",
    columns: ["day", "class_id", "subject_id", "teacher_id", "start_time", "end_time"],
    fields: [
      { name: "day", label: "Day", type: "select", options: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"] },
      fk("class_id", "Class", "classes"),
      fk("subject_id", "Subject", "subjects"),
      fk("teacher_id", "Teacher", "teachers"),
      { name: "start_time", label: "Start", type: "time" },
      { name: "end_time", label: "End", type: "time" },
    ],
  },
  {
    key: "homework", table: "homeworks", title: "Homework", group: "Academics", icon: "NotebookPen",
    columns: ["title", "class_id", "subject_id", "due_date"],
    fields: [
      { name: "title", label: "Title", required: true },
      fk("class_id", "Class", "classes"),
      fk("subject_id", "Subject", "subjects"),
      { name: "due_date", label: "Due Date", type: "date" },
      { name: "description", label: "Details", type: "textarea" },
    ],
  },
  {
    key: "exams", table: "exams", title: "Exams", group: "Exams", icon: "FileText",
    columns: ["name", "start_date", "end_date"],
    fields: [
      { name: "name", label: "Exam Name", required: true },
      { name: "start_date", label: "Start Date", type: "date" },
      { name: "end_date", label: "End Date", type: "date" },
    ],
  },
  {
    key: "date-sheets", table: "date_sheets", title: "Date Sheets", group: "Exams", icon: "CalendarDays",
    columns: ["exam_id", "class_id", "subject_id", "exam_date", "start_time", "end_time"],
    fields: [
      fk("exam_id", "Exam", "exams"),
      fk("class_id", "Class", "classes"),
      fk("subject_id", "Subject", "subjects"),
      { name: "exam_date", label: "Date", type: "date", required: true },
      { name: "start_time", label: "Start", type: "time" },
      { name: "end_time", label: "End", type: "time" },
    ],
    order: "exam_date",
  },
  {
    key: "results", table: "exam_marks", title: "Results / Marks", group: "Exams", icon: "Award",
    columns: ["exam_id", "student_id", "subject_id", "obtained_marks", "total_marks"],
    fields: [
      fk("exam_id", "Exam", "exams"),
      fk("student_id", "Student", "students"),
      fk("subject_id", "Subject", "subjects"),
      { name: "obtained_marks", label: "Obtained", type: "number" },
      { name: "total_marks", label: "Total", type: "number" },
    ],
  },
  {
    key: "fee-types", table: "fee_types", title: "Fee Types", group: "Finance", icon: "Tags",
    columns: ["name", "amount", "frequency"],
    fields: [
      { name: "name", label: "Fee Name", required: true },
      { name: "amount", label: "Amount", type: "number", required: true },
      { name: "frequency", label: "Frequency", type: "select", options: ["monthly", "quarterly", "yearly", "one-time"] },
    ],
  },
  {
    key: "student-fees", table: "student_fees", title: "Student Fees", group: "Finance", icon: "Receipt",
    columns: ["student_id", "title", "amount", "due_date", "status"],
    fields: [
      fk("student_id", "Student", "students"),
      fk("fee_type_id", "Fee Type", "fee_types"),
      { name: "title", label: "Title (e.g. March Fee)", required: true },
      { name: "amount", label: "Amount", type: "number", required: true },
      { name: "due_date", label: "Due Date", type: "date" },
      { name: "status", label: "Status", type: "select", options: ["unpaid", "partial", "paid"] },
    ],
  },
  {
    key: "payments", table: "payments", title: "Payments", group: "Finance", icon: "Wallet",
    columns: ["student_id", "amount", "method", "paid_on"],
    fields: [
      fk("student_id", "Student", "students"),
      { name: "amount", label: "Amount", type: "number", required: true },
      { name: "method", label: "Method", type: "select", options: ["cash", "bank", "easypaisa", "jazzcash", "card"] },
      { name: "paid_on", label: "Paid On", type: "date" },
      { name: "note", label: "Note" },
    ],
    order: "paid_on",
  },
  {
    key: "expenses", table: "expenses", title: "Expenses", group: "Finance", icon: "TrendingDown",
    columns: ["title", "category", "amount", "spent_on"],
    fields: [
      { name: "title", label: "Title", required: true },
      { name: "category", label: "Category" },
      { name: "amount", label: "Amount", type: "number", required: true },
      { name: "spent_on", label: "Date", type: "date" },
    ],
    order: "spent_on",
  },
  {
    key: "notifications", table: "notifications", title: "Notifications", group: "Communication", icon: "Bell",
    columns: ["title", "audience", "created_at"],
    fields: [
      { name: "title", label: "Title", required: true },
      { name: "audience", label: "Audience", type: "select", options: ["all", "teachers", "students", "parents", "staff"] },
      { name: "message", label: "Message", type: "textarea" },
    ],
  },
  {
    key: "leaves", table: "leaves", title: "Leave Requests", group: "Communication", icon: "Plane",
    columns: ["applicant", "from_date", "to_date", "status"],
    fields: [
      { name: "applicant", label: "Applicant", required: true },
      { name: "from_date", label: "From", type: "date" },
      { name: "to_date", label: "To", type: "date" },
      { name: "reason", label: "Reason", type: "textarea" },
      { name: "status", label: "Status", type: "select", options: ["pending", "approved", "rejected"] },
    ],
  },
  {
    key: "events", table: "calendar_events", title: "Calendar Events", group: "Communication", icon: "CalendarCheck",
    columns: ["title", "event_date"],
    fields: [
      { name: "title", label: "Event", required: true },
      { name: "event_date", label: "Date", type: "date", required: true },
      { name: "description", label: "Details", type: "textarea" },
    ],
    order: "event_date",
  },
];

export const byKey = (key: string) => resources.find((r) => r.key === key);
