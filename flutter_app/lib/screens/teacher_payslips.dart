import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../main.dart';
import '../session.dart';

/// The teacher's own payslips (Row Level Security only returns their rows).
class TeacherPayslips extends StatefulWidget {
  final AppSession session;
  const TeacherPayslips({super.key, required this.session});

  @override
  State<TeacherPayslips> createState() => _TeacherPayslipsState();
}

class _TeacherPayslipsState extends State<TeacherPayslips> {
  late Future<List<Map<String, dynamic>>> _future = _load();

  Future<List<Map<String, dynamic>>> _load() async {
    final rows = await db
        .from('payslips')
        .select()
        .eq('school_id', widget.session.schoolId!)
        .order('month', ascending: false);
    return List<Map<String, dynamic>>.from(rows);
  }

  String _month(String ym) =>
      DateFormat('MMMM yyyy').format(DateTime.parse('$ym-01'));

  @override
  Widget build(BuildContext context) {
    final cur = widget.session.currency;
    final money = NumberFormat.decimalPattern();
    final t = Theme.of(context).textTheme;
    return RefreshIndicator(
      onRefresh: () async => setState(() => _future = _load()),
      child: FutureBuilder<List<Map<String, dynamic>>>(
        future: _future,
        builder: (context, snap) {
          if (snap.hasError) return Center(child: Text('Could not load: ${snap.error}'));
          if (!snap.hasData) return const Center(child: CircularProgressIndicator());
          final slips = snap.data!;
          if (slips.isEmpty) {
            return ListView(children: const [
              SizedBox(height: 120),
              Center(child: Text('Your first payslip appears on the 1st of next month.')),
            ]);
          }
          return ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: slips.length,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (_, i) {
              final p = slips[i];
              final paid = p['status'] == 'paid';
              return Card(
                child: InkWell(
                  borderRadius: BorderRadius.circular(20),
                  onTap: () => showModalBottomSheet(
                    context: context,
                    showDragHandle: true,
                    isScrollControlled: true,
                    builder: (_) => _Detail(p: p, cur: cur, month: _month('${p['month']}')),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(_month('${p['month']}'),
                                  style: t.titleMedium?.copyWith(fontWeight: FontWeight.w700)),
                              Text('Absent days: ${p['absent_days']}', style: t.bodySmall),
                            ],
                          ),
                        ),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Text('$cur ${money.format((p['net_salary'] as num).round())}',
                                style: t.titleMedium?.copyWith(fontWeight: FontWeight.w800)),
                            Text(paid ? 'Paid' : 'Unpaid',
                                style: TextStyle(color: paid ? Colors.green : Colors.orange)),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }
}

class _Detail extends StatelessWidget {
  final Map<String, dynamic> p;
  final String cur;
  final String month;
  const _Detail({required this.p, required this.cur, required this.month});

  @override
  Widget build(BuildContext context) {
    final f = NumberFormat('#,##0.00');
    String m(String k) => '$cur ${f.format((p[k] as num).toDouble())}';
    Widget row(String l, String v, {bool bold = false, Color? color}) => Padding(
          padding: const EdgeInsets.symmetric(vertical: 6),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(l, style: TextStyle(fontWeight: bold ? FontWeight.w800 : null, color: color)),
              Text(v, style: TextStyle(fontWeight: bold ? FontWeight.w800 : null, color: color)),
            ],
          ),
        );
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 0, 24, 32),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text('Salary slip · $month',
              style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w800)),
          const SizedBox(height: 12),
          row('Working days', '${p['working_days']}'),
          row('Present', '${p['present_days']}'),
          row('Late (counted present)', '${p['late_days']}'),
          row('Leave (paid)', '${p['leave_days']}'),
          row('Absent / not marked', '${p['absent_days']}'),
          const Divider(),
          row('Basic salary', m('basic_salary')),
          row('Bonus', m('bonus')),
          row('Deduction', '- ${m('deduction')}', color: Colors.red),
          const Divider(),
          row('Net salary', m('net_salary'), bold: true),
        ],
      ),
    );
  }
}
