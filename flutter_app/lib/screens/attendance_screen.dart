import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../main.dart';
import '../session.dart';
import '../widgets/hero_banner.dart';

class AttendanceScreen extends StatefulWidget {
  final AppSession session;
  const AttendanceScreen({super.key, required this.session});

  @override
  State<AttendanceScreen> createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends State<AttendanceScreen> {
  List<Map<String, dynamic>> _classes = [];
  List<Map<String, dynamic>> _students = [];
  final Map<String, String> _status = {};
  String? _classId;
  DateTime _date = DateTime.now();
  bool _loading = false;
  bool _saving = false;

  String get _school => widget.session.schoolId!;
  String get _dateStr => DateFormat('yyyy-MM-dd').format(_date);

  @override
  void initState() {
    super.initState();
    _loadClasses();
  }

  Future<void> _loadClasses() async {
    final rows = await db
        .from('classes')
        .select('id, name')
        .eq('school_id', _school)
        .order('numeric_name');
    if (mounted) setState(() => _classes = List<Map<String, dynamic>>.from(rows));
  }

  Future<void> _loadStudents() async {
    if (_classId == null) return;
    setState(() => _loading = true);
    final students = await db
        .from('students')
        .select('id, name, roll_no, admission_no')
        .eq('school_id', _school)
        .eq('class_id', _classId!)
        .eq('status', 'active')
        .order('name');
    final att = await db
        .from('attendance')
        .select('student_id, status')
        .eq('school_id', _school)
        .eq('date', _dateStr);
    final existing = {for (final a in att) a['student_id'] as String: a['status'] as String};
    if (!mounted) return;
    setState(() {
      _students = List<Map<String, dynamic>>.from(students);
      _status
        ..clear()
        ..addEntries(_students.map((s) => MapEntry(s['id'] as String, existing[s['id']] ?? 'present')));
      _loading = false;
    });
  }

  Future<void> _save() async {
    setState(() => _saving = true);
    try {
      await db.from('attendance').upsert([
        for (final e in _status.entries)
          {'school_id': _school, 'student_id': e.key, 'date': _dateStr, 'status': e.value},
      ], onConflict: 'student_id,date');
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(const SnackBar(content: Text('Attendance saved')));
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Could not save: $e')));
      }
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  Future<void> _pickDate() async {
    final d = await showDatePicker(
        context: context,
        initialDate: _date,
        firstDate: DateTime(2020),
        lastDate: DateTime(2100));
    if (d != null) {
      setState(() => _date = d);
      _loadStudents();
    }
  }

  @override
  Widget build(BuildContext context) {
    final t = Theme.of(context).textTheme;
    return Scaffold(
      floatingActionButton: _students.isEmpty
          ? null
          : FloatingActionButton.extended(
              onPressed: _saving ? null : _save,
              icon: const Icon(Icons.check),
              label: Text(_saving ? 'Saving…' : 'Save'),
            ),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
        children: [
          const HeroBanner(title: 'Attendance', subtitle: 'Mark daily attendance class by class'),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: DropdownButtonFormField<String>(
                  isExpanded: true,
                  decoration: const InputDecoration(labelText: 'Class'),
                  initialValue: _classId,
                  items: [
                    for (final c in _classes)
                      DropdownMenuItem(value: c['id'] as String, child: Text('${c['name']}')),
                  ],
                  onChanged: (v) {
                    setState(() => _classId = v);
                    _loadStudents();
                  },
                ),
              ),
              const SizedBox(width: 12),
              OutlinedButton.icon(
                onPressed: _pickDate,
                icon: const Icon(Icons.calendar_today, size: 18),
                label: Text(DateFormat('d MMM').format(_date)),
              ),
            ],
          ),
          const SizedBox(height: 16),
          if (_loading)
            const Padding(padding: EdgeInsets.all(32), child: Center(child: CircularProgressIndicator()))
          else if (_classId != null && _students.isEmpty)
            Padding(
              padding: const EdgeInsets.all(32),
              child: Center(child: Text('No active students in this class.', style: t.bodyLarge)),
            )
          else
            for (final s in _students)
              Padding(
                padding: const EdgeInsets.only(bottom: 10),
                child: Card(
                  child: Padding(
                    padding: const EdgeInsets.all(14),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('${s['name']}',
                            style: t.titleMedium?.copyWith(fontWeight: FontWeight.w700)),
                        Text('Roll ${s['roll_no'] ?? s['admission_no']}', style: t.bodySmall),
                        const SizedBox(height: 10),
                        SizedBox(
                          width: double.infinity,
                          child: SegmentedButton<String>(
                            showSelectedIcon: false,
                            segments: const [
                              ButtonSegment(value: 'present', label: Text('P')),
                              ButtonSegment(value: 'absent', label: Text('A')),
                              ButtonSegment(value: 'late', label: Text('Late')),
                              ButtonSegment(value: 'leave', label: Text('Leave')),
                            ],
                            selected: {_status[s['id']] ?? 'present'},
                            onSelectionChanged: (v) =>
                                setState(() => _status[s['id'] as String] = v.first),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
        ],
      ),
    );
  }
}
