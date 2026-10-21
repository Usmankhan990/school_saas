import 'dart:async';
import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';

import '../config.dart';
import '../main.dart';
import '../session.dart';
import '../widgets/hero_banner.dart';

/// Teacher "My Day": big Check in / Check out button plus this month's summary.
class TeacherHome extends StatefulWidget {
  final AppSession session;
  const TeacherHome({super.key, required this.session});

  @override
  State<TeacherHome> createState() => _TeacherHomeState();
}

class _TeacherHomeState extends State<TeacherHome> {
  Map<String, dynamic>? _today;
  List<Map<String, dynamic>> _month = [];
  bool _loading = true;
  bool _busy = false;
  String? _message;
  String? _error;
  String? _noTeacher;
  late Timer _tick;
  DateTime _now = DateTime.now();

  @override
  void initState() {
    super.initState();
    _tick = Timer.periodic(const Duration(seconds: 1), (_) {
      if (mounted) setState(() => _now = DateTime.now());
    });
    _load();
  }

  @override
  void dispose() {
    _tick.cancel();
    super.dispose();
  }

  Future<void> _load() async {
    try {
      final teacher = await db
          .from('teachers')
          .select('id')
          .eq('user_id', widget.session.userId)
          .maybeSingle();
      if (teacher == null) {
        setState(() {
          _noTeacher = 'No teacher record is linked to your account. Contact the school admin.';
          _loading = false;
        });
        return;
      }
      final today = DateFormat('yyyy-MM-dd').format(DateTime.now());
      final monthStart = '${today.substring(0, 8)}01';
      final rows = await db
          .from('teacher_attendance')
          .select()
          .eq('teacher_id', teacher['id'] as String)
          .gte('date', monthStart)
          .order('date', ascending: false);
      final list = List<Map<String, dynamic>>.from(rows);
      if (!mounted) return;
      setState(() {
        _month = list;
        _today = list.where((r) => r['date'] == today).cast<Map<String, dynamic>?>().firstWhere((_) => true, orElse: () => null);
        _loading = false;
      });
    } catch (e) {
      if (mounted) setState(() { _error = '$e'; _loading = false; });
    }
  }

  Future<void> _check(String action) async {
    setState(() {
      _busy = true;
      _message = null;
      _error = null;
    });
    try {
      final token = db.auth.currentSession?.accessToken;
      final res = await http.post(
        Uri.parse('$webBaseUrl/api/teacher/attendance'),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
        body: jsonEncode({'action': action}),
      );
      final body = jsonDecode(res.body) as Map<String, dynamic>;
      if (body['ok'] == true) {
        _message = body['message'] as String?;
      } else {
        _error = (body['error'] as String?) ?? 'Something went wrong.';
      }
    } catch (_) {
      _error = 'Could not reach the server. Check your internet.';
    }
    if (mounted) setState(() => _busy = false);
    await _load();
  }

  String _time(String? iso) {
    if (iso == null) return '—';
    return DateFormat.Hm().format(DateTime.parse(iso).toLocal());
  }

  int _count(String status) => _month.where((r) => r['status'] == status).length;

  @override
  Widget build(BuildContext context) {
    final t = Theme.of(context).textTheme;
    final checkedIn = _today?['check_in'] != null;
    final checkedOut = _today?['check_out'] != null;
    final blocked = _today != null &&
        !checkedIn &&
        (_today!['status'] == 'leave' || _today!['status'] == 'absent');

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          HeroBanner(
              title: 'Hello, ${widget.session.name}',
              subtitle: '${widget.session.schoolName} · your day'),
          const SizedBox(height: 16),
          if (_loading)
            const Padding(padding: EdgeInsets.all(40), child: Center(child: CircularProgressIndicator()))
          else if (_noTeacher != null)
            Card(child: Padding(padding: const EdgeInsets.all(20), child: Text(_noTeacher!)))
          else ...[
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  children: [
                    Text(DateFormat('EEEE, d MMMM').format(_now), style: t.bodyMedium),
                    const SizedBox(height: 4),
                    Text(DateFormat('HH:mm:ss').format(_now),
                        style: t.displaySmall?.copyWith(fontWeight: FontWeight.w800, fontFeatures: const [FontFeature.tabularFigures()])),
                    const SizedBox(height: 16),
                    if (blocked)
                      Text('Today is marked as ${_today!['status']} by the school admin.',
                          textAlign: TextAlign.center)
                    else if (checkedOut)
                      const Chip(
                          avatar: Icon(Icons.check_circle, color: Colors.green),
                          label: Text('Done for today'))
                    else
                      SizedBox(
                        width: double.infinity,
                        height: 64,
                        child: FilledButton.icon(
                          style: FilledButton.styleFrom(
                            backgroundColor: checkedIn ? Colors.deepOrange : Colors.green,
                          ),
                          onPressed: _busy ? null : () => _check(checkedIn ? 'check_out' : 'check_in'),
                          icon: _busy
                              ? const SizedBox(height: 18, width: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                              : Icon(checkedIn ? Icons.logout : Icons.login),
                          label: Text(checkedIn ? 'Check out' : 'Check in',
                              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
                        ),
                      ),
                    if (_message != null) ...[
                      const SizedBox(height: 10),
                      Text(_message!, style: const TextStyle(color: Colors.green)),
                    ],
                    if (_error != null) ...[
                      const SizedBox(height: 10),
                      Text(_error!, style: TextStyle(color: Theme.of(context).colorScheme.error)),
                    ],
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        _pill('Check in', _time(_today?['check_in'] as String?)),
                        const SizedBox(width: 8),
                        _pill('Check out', _time(_today?['check_out'] as String?)),
                        const SizedBox(width: 8),
                        _pill('Status', '${_today?['status'] ?? '—'}'),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                _stat('Present', _count('present'), Colors.green),
                const SizedBox(width: 8),
                _stat('Late', _count('late'), Colors.orange),
                const SizedBox(width: 8),
                _stat('Leave/Absent', _count('leave') + _count('absent'), Colors.red),
              ],
            ),
            const SizedBox(height: 12),
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Recent attendance', style: t.titleMedium?.copyWith(fontWeight: FontWeight.w700)),
                    const SizedBox(height: 8),
                    if (_month.isEmpty) Text('No records this month yet.', style: t.bodySmall),
                    for (final r in _month.take(7))
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 4),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(DateFormat('EEE d MMM').format(DateTime.parse('${r['date']}'))),
                            Text('${_time(r['check_in'] as String?)} – ${_time(r['check_out'] as String?)}', style: t.bodySmall),
                            Text('${r['status']}'),
                          ],
                        ),
                      ),
                  ],
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _pill(String label, String value) => Expanded(
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10),
          decoration: BoxDecoration(
            color: Theme.of(context).colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
            borderRadius: BorderRadius.circular(14),
          ),
          child: Column(children: [
            Text(label, style: Theme.of(context).textTheme.bodySmall),
            Text(value, style: const TextStyle(fontWeight: FontWeight.w700)),
          ]),
        ),
      );

  Widget _stat(String label, int n, Color c) => Expanded(
        child: Card(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 14),
            child: Column(children: [
              Text('$n', style: TextStyle(fontSize: 26, fontWeight: FontWeight.w800, color: c)),
              Text(label, style: Theme.of(context).textTheme.bodySmall),
            ]),
          ),
        ),
      );
}
