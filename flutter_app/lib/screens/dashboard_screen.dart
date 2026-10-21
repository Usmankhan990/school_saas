import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

import '../main.dart';
import '../session.dart';
import '../widgets/hero_banner.dart';
import '../widgets/tilt_card.dart';

class _Stat {
  final String label;
  final String value;
  final IconData icon;
  final List<Color> colors;
  const _Stat(this.label, this.value, this.icon, this.colors);
}

class DashboardScreen extends StatefulWidget {
  final AppSession session;
  const DashboardScreen({super.key, required this.session});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  late Future<List<_Stat>> _future = _load();

  Future<int> _count(String table, [Map<String, Object> eq = const {}]) async {
    var q = db.from(table).select('id').eq('school_id', widget.session.schoolId!);
    for (final e in eq.entries) {
      q = q.eq(e.key, e.value);
    }
    final res = await q.count(CountOption.exact);
    return res.count;
  }

  Future<double> _sum(String table, String dateCol, String from,
      {String? notStatus}) async {
    var q = db
        .from(table)
        .select('amount')
        .eq('school_id', widget.session.schoolId!);
    if (dateCol.isNotEmpty) q = q.gte(dateCol, from);
    if (notStatus != null) q = q.neq('status', notStatus);
    final rows = await q;
    return rows.fold<double>(0, (a, r) => a + (r['amount'] as num).toDouble());
  }

  Future<List<_Stat>> _load() async {
    final now = DateTime.now();
    final today = DateFormat('yyyy-MM-dd').format(now);
    final month = '${today.substring(0, 8)}01';
    final cur = widget.session.currency;
    final money = NumberFormat.decimalPattern();
    final r = await Future.wait<Object>([
      _count('students'),
      _count('teachers'),
      _count('attendance', {'date': today, 'status': 'present'}),
      _count('attendance', {'date': today, 'status': 'absent'}),
      _sum('payments', 'paid_on', month),
      _sum('student_fees', '', '', notStatus: 'paid'),
      _sum('expenses', 'spent_on', month),
      _count('staff'),
    ]);
    String m(Object v) => '$cur ${money.format((v as double).round())}';
    return [
      _Stat('Students', '${r[0]}', Icons.school, const [Color(0xFF6366F1), Color(0xFF3B82F6)]),
      _Stat('Teachers', '${r[1]}', Icons.person, const [Color(0xFFD946EF), Color(0xFFEC4899)]),
      _Stat('Present today', '${r[2]}', Icons.how_to_reg, const [Color(0xFF10B981), Color(0xFF14B8A6)]),
      _Stat('Absent today', '${r[3]}', Icons.person_off, const [Color(0xFFF97316), Color(0xFFEF4444)]),
      _Stat('Collected this month', m(r[4]), Icons.account_balance_wallet, const [Color(0xFF06B6D4), Color(0xFF0EA5E9)]),
      _Stat('Fees pending', m(r[5]), Icons.warning_amber, const [Color(0xFFF59E0B), Color(0xFFF97316)]),
      _Stat('Expenses this month', m(r[6]), Icons.trending_down, const [Color(0xFFF43F5E), Color(0xFFEF4444)]),
      _Stat('Staff', '${r[7]}', Icons.work, const [Color(0xFF8B5CF6), Color(0xFFA855F7)]),
    ];
  }

  @override
  Widget build(BuildContext context) {
    final t = Theme.of(context).textTheme;
    return RefreshIndicator(
      onRefresh: () async => setState(() => _future = _load()),
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          HeroBanner(
              title: 'Welcome, ${widget.session.name}',
              subtitle: '${widget.session.schoolName} overview'),
          const SizedBox(height: 16),
          FutureBuilder<List<_Stat>>(
            future: _future,
            builder: (context, snap) {
              if (snap.hasError) {
                return Padding(
                    padding: const EdgeInsets.all(24),
                    child: Text('Could not load: ${snap.error}'));
              }
              if (!snap.hasData) {
                return const Padding(
                    padding: EdgeInsets.all(40),
                    child: Center(child: CircularProgressIndicator()));
              }
              return GridView.count(
                crossAxisCount: 2,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                mainAxisSpacing: 12,
                crossAxisSpacing: 12,
                childAspectRatio: 1.25,
                children: [
                  for (final s in snap.data!)
                    TiltCard(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              gradient: LinearGradient(colors: s.colors),
                              borderRadius: BorderRadius.circular(14),
                            ),
                            child: Icon(s.icon, color: Colors.white, size: 20),
                          ),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              FittedBox(
                                fit: BoxFit.scaleDown,
                                child: Text(s.value,
                                    style: t.titleLarge
                                        ?.copyWith(fontWeight: FontWeight.w800)),
                              ),
                              Text(s.label,
                                  style: t.bodySmall,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis),
                            ],
                          ),
                        ],
                      ),
                    ),
                ],
              );
            },
          ),
        ],
      ),
    );
  }
}
