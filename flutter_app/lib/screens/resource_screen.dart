import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../main.dart';
import '../resources.dart';
import '../session.dart';

typedef Rec = Map<String, dynamic>;

/// Generic list + add + delete screen for any module in [resources].
class ResourceScreen extends StatefulWidget {
  final AppSession session;
  final ResourceDef def;
  final bool showAppBar;
  const ResourceScreen(
      {super.key, required this.session, required this.def, this.showAppBar = true});

  @override
  State<ResourceScreen> createState() => _ResourceScreenState();
}

class _Data {
  final List<Rec> rows;
  final Map<String, Map<String, String>> lookups; // field -> id -> label
  const _Data(this.rows, this.lookups);
}

class _ResourceScreenState extends State<ResourceScreen> {
  late Future<_Data> _future = _load();

  String get _school => widget.session.schoolId!;

  Future<_Data> _load() async {
    final d = widget.def;
    final lookups = <String, Map<String, String>>{};
    final fkFields = d.fields.where((f) => f.type == FieldType.fk).toList();

    final rowsFuture = d.order != null
        ? db.from(d.table).select().eq('school_id', _school).order(d.order!).limit(500)
        : db.from(d.table).select().eq('school_id', _school).order('created_at', ascending: false).limit(500);
    final fkFutures = fkFields.map((f) async {
      final data = await db
          .from(f.fkTable!)
          .select('id, name')
          .eq('school_id', _school)
          .limit(1000);
      lookups[f.name] = {for (final r in data) r['id'] as String: '${r['name']}'};
    }).toList();

    final results = await Future.wait<Object?>([rowsFuture, ...fkFutures]);
    return _Data(List<Rec>.from(results.first as List), lookups);
  }

  void _reload() => setState(() => _future = _load());

  String _label(_Data data, String col, Object? v) {
    final f = widget.def.field(col);
    if (f?.type == FieldType.fk) return data.lookups[col]?[v] ?? '—';
    if (v == null || v == '') return '—';
    if (v is bool) return v ? 'Yes' : 'No';
    if (col == 'created_at') {
      return DateFormat.yMMMd().format(DateTime.parse('$v').toLocal());
    }
    return '$v';
  }

  String _head(String col) =>
      widget.def.field(col)?.label ?? col.replaceAll('_', ' ');

  Future<void> _delete(Rec row) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete record?'),
        content: const Text('This cannot be undone.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
          FilledButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Delete')),
        ],
      ),
    );
    if (ok != true) return;
    try {
      await db.from(widget.def.table).delete().eq('id', row['id'] as String);
      _reload();
    } catch (e) {
      _snack('Could not delete: $e');
    }
  }

  void _snack(String m) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(m)));
  }

  Future<void> _add(_Data data) async {
    final saved = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      showDragHandle: true,
      builder: (_) => _RecordForm(
        session: widget.session,
        def: widget.def,
        lookups: data.lookups,
      ),
    );
    if (saved == true) _reload();
  }

  @override
  Widget build(BuildContext context) {
    final d = widget.def;
    final t = Theme.of(context).textTheme;
    return FutureBuilder<_Data>(
      future: _future,
      builder: (context, snap) {
        final data = snap.data;
        return Scaffold(
          appBar: widget.showAppBar ? AppBar(title: Text(d.title)) : null,
          floatingActionButton: data == null
              ? null
              : FloatingActionButton.extended(
                  onPressed: () => _add(data),
                  icon: const Icon(Icons.add),
                  label: const Text('Add'),
                ),
          body: snap.hasError
              ? Center(child: Text('Could not load: ${snap.error}'))
              : data == null
                  ? const Center(child: CircularProgressIndicator())
                  : RefreshIndicator(
                      onRefresh: () async => _reload(),
                      child: data.rows.isEmpty
                          ? ListView(children: [
                              const SizedBox(height: 120),
                              Center(child: Text('No records yet.', style: t.bodyLarge)),
                            ])
                          : ListView.separated(
                              padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
                              itemCount: data.rows.length,
                              separatorBuilder: (_, __) => const SizedBox(height: 10),
                              itemBuilder: (_, i) {
                                final row = data.rows[i];
                                return Dismissible(
                                  key: ValueKey(row['id']),
                                  direction: DismissDirection.endToStart,
                                  confirmDismiss: (_) async {
                                    await _delete(row);
                                    return false; // list is reloaded after delete
                                  },
                                  background: Container(
                                    alignment: Alignment.centerRight,
                                    padding: const EdgeInsets.only(right: 20),
                                    decoration: BoxDecoration(
                                      color: Colors.red,
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: const Icon(Icons.delete, color: Colors.white),
                                  ),
                                  child: Card(
                                    child: Padding(
                                      padding: const EdgeInsets.all(16),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(_label(data, d.columns.first, row[d.columns.first]),
                                              style: t.titleMedium?.copyWith(fontWeight: FontWeight.w700)),
                                          const SizedBox(height: 6),
                                          for (final c in d.columns.skip(1))
                                            Padding(
                                              padding: const EdgeInsets.symmetric(vertical: 2),
                                              child: Row(
                                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                children: [
                                                  Text(_head(c), style: t.bodySmall),
                                                  Flexible(
                                                    child: Text(_label(data, c, row[c]),
                                                        textAlign: TextAlign.right,
                                                        style: t.bodyMedium),
                                                  ),
                                                ],
                                              ),
                                            ),
                                        ],
                                      ),
                                    ),
                                  ),
                                );
                              },
                            ),
                    ),
        );
      },
    );
  }
}

class _RecordForm extends StatefulWidget {
  final AppSession session;
  final ResourceDef def;
  final Map<String, Map<String, String>> lookups;
  const _RecordForm({required this.session, required this.def, required this.lookups});

  @override
  State<_RecordForm> createState() => _RecordFormState();
}

class _RecordFormState extends State<_RecordForm> {
  final _key = GlobalKey<FormState>();
  final Map<String, String> _values = {};
  final Map<String, TextEditingController> _ctrl = {};
  bool _saving = false;
  String? _error;

  TextEditingController _c(String name) =>
      _ctrl.putIfAbsent(name, () => TextEditingController());

  @override
  void dispose() {
    for (final c in _ctrl.values) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _save() async {
    if (!_key.currentState!.validate()) return;
    setState(() {
      _saving = true;
      _error = null;
    });
    final d = widget.def;
    final school = widget.session.schoolId!;
    final row = <String, dynamic>{'school_id': school};
    for (final f in d.fields) {
      final v = (_values[f.name] ?? '').trim();
      if (v.isEmpty) continue;
      if (f.type == FieldType.number) {
        row[f.name] = num.tryParse(v);
      } else if (f.name == 'is_current') {
        row[f.name] = v == 'true';
      } else {
        row[f.name] = v;
      }
    }
    try {
      if (d.table == 'academic_sessions' && row['is_current'] == true) {
        await db.from('academic_sessions').update({'is_current': false}).eq('school_id', school);
      }
      await db.from(d.table).insert(row);
      if (d.table == 'payments' && row['student_id'] != null) {
        await _settleFees(school, row['student_id'] as String, (row['amount'] as num).toDouble());
      }
      if (mounted) Navigator.pop(context, true);
    } catch (e) {
      setState(() {
        _saving = false;
        _error = '$e';
      });
    }
  }

  /// Apply a payment to the student's oldest unpaid fees (same as the web app).
  Future<void> _settleFees(String school, String student, double amount) async {
    final fees = await db
        .from('student_fees')
        .select('id, amount, status')
        .eq('school_id', school)
        .eq('student_id', student)
        .neq('status', 'paid')
        .order('due_date');
    var left = amount;
    for (final f in fees) {
      if (left <= 0) break;
      final fee = (f['amount'] as num).toDouble();
      if (left >= fee) {
        await db.from('student_fees').update({'status': 'paid'}).eq('id', f['id'] as String);
        left -= fee;
      } else {
        await db.from('student_fees').update({'status': 'partial'}).eq('id', f['id'] as String);
        left = 0;
      }
    }
  }

  Widget _input(FieldDef f) {
    final label = f.required ? '${f.label} *' : f.label;
    String? req(String? v) =>
        f.required && (v == null || v.trim().isEmpty) ? 'Required' : null;

    switch (f.type) {
      case FieldType.select:
        return DropdownButtonFormField<String>(
          decoration: InputDecoration(labelText: label),
          items: [for (final o in f.options) DropdownMenuItem(value: o, child: Text(o))],
          onChanged: (v) => _values[f.name] = v ?? '',
        );
      case FieldType.fk:
        final opts = widget.lookups[f.name] ?? {};
        return DropdownButtonFormField<String>(
          isExpanded: true,
          decoration: InputDecoration(labelText: label),
          items: [
            for (final e in opts.entries)
              DropdownMenuItem(value: e.key, child: Text(e.value, overflow: TextOverflow.ellipsis)),
          ],
          onChanged: (v) => _values[f.name] = v ?? '',
        );
      case FieldType.date:
        return TextFormField(
          controller: _c(f.name),
          readOnly: true,
          validator: req,
          decoration: InputDecoration(labelText: label, suffixIcon: const Icon(Icons.calendar_today)),
          onTap: () async {
            final d = await showDatePicker(
              context: context,
              initialDate: DateTime.now(),
              firstDate: DateTime(1990),
              lastDate: DateTime(2100),
            );
            if (d != null) {
              final s = DateFormat('yyyy-MM-dd').format(d);
              _c(f.name).text = s;
              _values[f.name] = s;
            }
          },
        );
      case FieldType.time:
        return TextFormField(
          controller: _c(f.name),
          readOnly: true,
          validator: req,
          decoration: InputDecoration(labelText: label, suffixIcon: const Icon(Icons.access_time)),
          onTap: () async {
            final t = await showTimePicker(context: context, initialTime: TimeOfDay.now());
            if (t != null) {
              final s =
                  '${t.hour.toString().padLeft(2, '0')}:${t.minute.toString().padLeft(2, '0')}';
              _c(f.name).text = s;
              _values[f.name] = s;
            }
          },
        );
      case FieldType.number:
        return TextFormField(
          keyboardType: const TextInputType.numberWithOptions(decimal: true),
          decoration: InputDecoration(labelText: label),
          validator: req,
          onChanged: (v) => _values[f.name] = v,
        );
      case FieldType.textarea:
        return TextFormField(
          maxLines: 3,
          decoration: InputDecoration(labelText: label),
          validator: req,
          onChanged: (v) => _values[f.name] = v,
        );
      case FieldType.text:
        return TextFormField(
          decoration: InputDecoration(labelText: label),
          validator: req,
          onChanged: (v) => _values[f.name] = v,
        );
    }
  }

  @override
  Widget build(BuildContext context) {
    final bottom = MediaQuery.of(context).viewInsets.bottom;
    return Padding(
      padding: EdgeInsets.fromLTRB(20, 0, 20, bottom + 20),
      child: Form(
        key: _key,
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text('Add ${widget.def.title}',
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w800)),
              const SizedBox(height: 16),
              for (final f in widget.def.fields) ...[
                _input(f),
                const SizedBox(height: 12),
              ],
              if (_error != null)
                Padding(
                  padding: const EdgeInsets.only(bottom: 8),
                  child: Text(_error!, style: TextStyle(color: Theme.of(context).colorScheme.error)),
                ),
              FilledButton(
                onPressed: _saving ? null : _save,
                child: _saving
                    ? const SizedBox(height: 18, width: 18, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Text('Save'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
