import 'package:flutter/material.dart';

import '../main.dart';
import '../session.dart';
import '../theme.dart';
import 'home_shell.dart';

/// Platform admin console: list schools, enable / disable, open one.
class PlatformScreen extends StatefulWidget {
  final AppSession session;
  const PlatformScreen({super.key, required this.session});

  @override
  State<PlatformScreen> createState() => _PlatformScreenState();
}

class _PlatformScreenState extends State<PlatformScreen> {
  late Future<List<Map<String, dynamic>>> _future = _load();

  Future<List<Map<String, dynamic>>> _load() async {
    final rows = await db
        .from('schools')
        .select()
        .order('created_at', ascending: false);
    return List<Map<String, dynamic>>.from(rows);
  }

  Future<void> _toggle(Map<String, dynamic> s) async {
    await db
        .from('schools')
        .update({'status': s['status'] == 'active' ? 'inactive' : 'active'})
        .eq('id', s['id'] as String);
    setState(() => _future = _load());
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Platform Admin'),
        actions: [
          IconButton(
              onPressed: () => themeController.toggle(context),
              icon: const Icon(Icons.brightness_6)),
          IconButton(
              onPressed: () => db.auth.signOut(),
              icon: const Icon(Icons.logout)),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async => setState(() => _future = _load()),
        child: FutureBuilder<List<Map<String, dynamic>>>(
          future: _future,
          builder: (context, snap) {
            if (!snap.hasData) {
              return const Center(child: CircularProgressIndicator());
            }
            final schools = snap.data!;
            if (schools.isEmpty) {
              return ListView(children: const [
                Padding(
                    padding: EdgeInsets.all(40),
                    child: Center(child: Text('No schools yet.')))
              ]);
            }
            return ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: schools.length,
              separatorBuilder: (_, __) => const SizedBox(height: 12),
              itemBuilder: (_, i) {
                final s = schools[i];
                final active = s['status'] == 'active';
                return Card(
                  child: ListTile(
                    contentPadding:
                        const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                    title: Text(s['name'] as String,
                        style: const TextStyle(fontWeight: FontWeight.w700)),
                    subtitle: Text('/${s['slug']}'),
                    leading: CircleAvatar(
                      backgroundColor: brand.withValues(alpha: 0.15),
                      child: const Icon(Icons.apartment, color: brand),
                    ),
                    trailing: Switch(value: active, onChanged: (_) => _toggle(s)),
                    onTap: () => Navigator.of(context).push(MaterialPageRoute(
                      builder: (_) => HomeShell(
                          session: widget.session.withSchool(s), canGoBack: true),
                    )),
                  ),
                );
              },
            );
          },
        ),
      ),
    );
  }
}
