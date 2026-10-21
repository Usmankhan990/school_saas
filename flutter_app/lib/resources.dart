// ignore_for_file: prefer_const_constructors
import 'package:flutter/material.dart';

enum FieldType { text, number, date, time, textarea, select, fk }

class FieldDef {
  final String name;
  final String label;
  final FieldType type;
  final List<String> options;
  final String? fkTable;
  final bool required;
  const FieldDef(this.name, this.label,
      {this.type = FieldType.text,
      this.options = const [],
      this.fkTable,
      this.required = false});
}

class ResourceDef {
  final String key;
  final String table;
  final String title;
  final String group;
  final IconData icon;
  final List<String> columns;
  final List<FieldDef> fields;
  final String? order;
  const ResourceDef(this.key, this.table, this.title, this.group, this.icon,
      this.columns, this.fields,
      {this.order});

  FieldDef? field(String name) {
    for (final f in fields) {
      if (f.name == name) return f;
    }
    return null;
  }
}

FieldDef _fk(String name, String label, String table) =>
    FieldDef(name, label, type: FieldType.fk, fkTable: table);

const _active = ['active', 'inactive'];

/// Mirrors web/lib/resources.ts so the web app and the mobile app share the
/// same Supabase tables.
final List<ResourceDef> resources = [
  ResourceDef('students', 'students', 'Students', 'People', Icons.school,
      ['admission_no', 'name', 'father_name', 'class_id', 'section_id', 'status'], [
    const FieldDef('admission_no', 'Admission No', required: true),
    const FieldDef('roll_no', 'Roll No'),
    const FieldDef('name', 'Student Name', required: true),
    const FieldDef('father_name', 'Father Name'),
    const FieldDef('gender', 'Gender',
        type: FieldType.select, options: ['male', 'female', 'other']),
    const FieldDef('dob', 'Date of Birth', type: FieldType.date),
    const FieldDef('phone', 'Phone'),
    _fk('class_id', 'Class', 'classes'),
    _fk('section_id', 'Section', 'sections'),
    const FieldDef('address', 'Address', type: FieldType.textarea),
    const FieldDef('status', 'Status',
        type: FieldType.select, options: ['active', 'inactive', 'left']),
  ]),
  ResourceDef('teachers', 'teachers', 'Teachers', 'People', Icons.person,
      ['name', 'phone', 'qualification', 'salary', 'status'], [
    const FieldDef('name', 'Name', required: true),
    const FieldDef('phone', 'Phone'),
    const FieldDef('email', 'Email'),
    const FieldDef('qualification', 'Qualification'),
    const FieldDef('salary', 'Salary', type: FieldType.number),
    const FieldDef('joining_date', 'Joining Date', type: FieldType.date),
    const FieldDef('status', 'Status',
        type: FieldType.select, options: _active),
  ]),
  ResourceDef('staff', 'staff', 'Staff', 'People', Icons.work,
      ['name', 'designation', 'phone', 'salary', 'status'], [
    const FieldDef('name', 'Name', required: true),
    const FieldDef('designation', 'Designation'),
    const FieldDef('phone', 'Phone'),
    const FieldDef('salary', 'Salary', type: FieldType.number),
    const FieldDef('status', 'Status',
        type: FieldType.select, options: _active),
  ]),
  ResourceDef('classes', 'classes', 'Classes', 'Academics', Icons.apartment,
      ['name', 'numeric_name'], [
    const FieldDef('name', 'Class Name', required: true),
    const FieldDef('numeric_name', 'Numeric Order', type: FieldType.number),
  ],
      order: 'numeric_name'),
  ResourceDef('sections', 'sections', 'Sections', 'Academics', Icons.grid_view,
      ['name', 'class_id'], [
    _fk('class_id', 'Class', 'classes'),
    const FieldDef('name', 'Section Name', required: true),
  ]),
  ResourceDef('subjects', 'subjects', 'Subjects', 'Academics', Icons.menu_book,
      ['name', 'code', 'class_id'], [
    const FieldDef('name', 'Subject', required: true),
    const FieldDef('code', 'Code'),
    _fk('class_id', 'Class', 'classes'),
  ]),
  ResourceDef('academic-sessions', 'academic_sessions', 'Academic Sessions',
      'Academics', Icons.date_range,
      ['name', 'start_date', 'end_date', 'is_current'], [
    const FieldDef('name', 'Session (e.g. 2026-27)', required: true),
    const FieldDef('start_date', 'Start Date',
        type: FieldType.date, required: true),
    const FieldDef('end_date', 'End Date',
        type: FieldType.date, required: true),
    const FieldDef('is_current', 'Current?',
        type: FieldType.select, options: ['true', 'false']),
  ],
      order: 'start_date'),
  ResourceDef('timetable', 'timetables', 'Timetable', 'Academics', Icons.schedule,
      ['day', 'class_id', 'subject_id', 'teacher_id', 'start_time', 'end_time'], [
    const FieldDef('day', 'Day', type: FieldType.select, options: [
      'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
    ]),
    _fk('class_id', 'Class', 'classes'),
    _fk('subject_id', 'Subject', 'subjects'),
    _fk('teacher_id', 'Teacher', 'teachers'),
    const FieldDef('start_time', 'Start', type: FieldType.time),
    const FieldDef('end_time', 'End', type: FieldType.time),
  ]),
  ResourceDef('homework', 'homeworks', 'Homework', 'Academics', Icons.edit_note,
      ['title', 'class_id', 'subject_id', 'due_date'], [
    const FieldDef('title', 'Title', required: true),
    _fk('class_id', 'Class', 'classes'),
    _fk('subject_id', 'Subject', 'subjects'),
    const FieldDef('due_date', 'Due Date', type: FieldType.date),
    const FieldDef('description', 'Details', type: FieldType.textarea),
  ]),
  ResourceDef('exams', 'exams', 'Exams', 'Exams', Icons.description,
      ['name', 'start_date', 'end_date'], [
    const FieldDef('name', 'Exam Name', required: true),
    const FieldDef('start_date', 'Start Date', type: FieldType.date),
    const FieldDef('end_date', 'End Date', type: FieldType.date),
  ]),
  ResourceDef('date-sheets', 'date_sheets', 'Date Sheets', 'Exams',
      Icons.calendar_month,
      ['exam_id', 'class_id', 'subject_id', 'exam_date', 'start_time', 'end_time'], [
    _fk('exam_id', 'Exam', 'exams'),
    _fk('class_id', 'Class', 'classes'),
    _fk('subject_id', 'Subject', 'subjects'),
    const FieldDef('exam_date', 'Date', type: FieldType.date, required: true),
    const FieldDef('start_time', 'Start', type: FieldType.time),
    const FieldDef('end_time', 'End', type: FieldType.time),
  ],
      order: 'exam_date'),
  ResourceDef('results', 'exam_marks', 'Results / Marks', 'Exams',
      Icons.emoji_events,
      ['exam_id', 'student_id', 'subject_id', 'obtained_marks', 'total_marks'], [
    _fk('exam_id', 'Exam', 'exams'),
    _fk('student_id', 'Student', 'students'),
    _fk('subject_id', 'Subject', 'subjects'),
    const FieldDef('obtained_marks', 'Obtained', type: FieldType.number),
    const FieldDef('total_marks', 'Total', type: FieldType.number),
  ]),
  ResourceDef('fee-types', 'fee_types', 'Fee Types', 'Finance', Icons.sell,
      ['name', 'amount', 'frequency'], [
    const FieldDef('name', 'Fee Name', required: true),
    const FieldDef('amount', 'Amount', type: FieldType.number, required: true),
    const FieldDef('frequency', 'Frequency',
        type: FieldType.select,
        options: ['monthly', 'quarterly', 'yearly', 'one-time']),
  ]),
  ResourceDef('student-fees', 'student_fees', 'Student Fees', 'Finance',
      Icons.receipt_long,
      ['student_id', 'title', 'amount', 'due_date', 'status'], [
    _fk('student_id', 'Student', 'students'),
    _fk('fee_type_id', 'Fee Type', 'fee_types'),
    const FieldDef('title', 'Title (e.g. March Fee)', required: true),
    const FieldDef('amount', 'Amount', type: FieldType.number, required: true),
    const FieldDef('due_date', 'Due Date', type: FieldType.date),
    const FieldDef('status', 'Status',
        type: FieldType.select, options: ['unpaid', 'partial', 'paid']),
  ]),
  ResourceDef('payments', 'payments', 'Payments', 'Finance',
      Icons.account_balance_wallet,
      ['student_id', 'amount', 'method', 'paid_on'], [
    _fk('student_id', 'Student', 'students'),
    const FieldDef('amount', 'Amount', type: FieldType.number, required: true),
    const FieldDef('method', 'Method',
        type: FieldType.select,
        options: ['cash', 'bank', 'easypaisa', 'jazzcash', 'card']),
    const FieldDef('paid_on', 'Paid On', type: FieldType.date),
    const FieldDef('note', 'Note'),
  ],
      order: 'paid_on'),
  ResourceDef('expenses', 'expenses', 'Expenses', 'Finance',
      Icons.trending_down, ['title', 'category', 'amount', 'spent_on'], [
    const FieldDef('title', 'Title', required: true),
    const FieldDef('category', 'Category'),
    const FieldDef('amount', 'Amount', type: FieldType.number, required: true),
    const FieldDef('spent_on', 'Date', type: FieldType.date),
  ],
      order: 'spent_on'),
  ResourceDef('notifications', 'notifications', 'Notifications',
      'Communication', Icons.notifications, ['title', 'audience', 'created_at'], [
    const FieldDef('title', 'Title', required: true),
    const FieldDef('audience', 'Audience',
        type: FieldType.select,
        options: ['all', 'teachers', 'students', 'parents', 'staff']),
    const FieldDef('message', 'Message', type: FieldType.textarea),
  ]),
  ResourceDef('leaves', 'leaves', 'Leave Requests', 'Communication',
      Icons.flight, ['applicant', 'from_date', 'to_date', 'status'], [
    const FieldDef('applicant', 'Applicant', required: true),
    const FieldDef('from_date', 'From', type: FieldType.date),
    const FieldDef('to_date', 'To', type: FieldType.date),
    const FieldDef('reason', 'Reason', type: FieldType.textarea),
    const FieldDef('status', 'Status',
        type: FieldType.select, options: ['pending', 'approved', 'rejected']),
  ]),
  ResourceDef('events', 'calendar_events', 'Calendar Events', 'Communication',
      Icons.event_available, ['title', 'event_date'], [
    const FieldDef('title', 'Event', required: true),
    const FieldDef('event_date', 'Date', type: FieldType.date, required: true),
    const FieldDef('description', 'Details', type: FieldType.textarea),
  ],
      order: 'event_date'),
];

const groupOrder = ['People', 'Academics', 'Exams', 'Finance', 'Communication'];

ResourceDef? resourceByKey(String key) {
  for (final r in resources) {
    if (r.key == key) return r;
  }
  return null;
}
