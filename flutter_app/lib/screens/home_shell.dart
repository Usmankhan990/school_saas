import 'package:flutter/material.dart';

import '../main.dart';
import '../resources.dart';
import '../session.dart';
import '../theme.dart';
import 'attendance_screen.dart';
import 'dashboard_screen.dart';
import 'resource_screen.dart';

/// School app shell: bottom navigation for the daily tasks, drawer for all modules.
class HomeShell extends StatefulWidget {
  final AppSession session;
  final bool canGoBack; // true when a platform admin opened this school
  const HomeShell({super.key, required this.session, this.canGoBack = false});

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  int _index = 0;

  AppSession get s => widget.session;

  Widget _body() {
    switch (_index) {
      case 1:
        return ResourceScreen(
            key: const ValueKey('students'),
            session: s,
            def: resourceByKey('students')!,
            showAppBar: false);
      case 2:
        return AttendanceScreen(session: s);
      case 3:
        return ResourceScreen(
            key: const ValueKey('payments'),
            session: s,
            def: resourceByKey('payments')!,
            showAppBar: false);
      default:
        return DashboardScreen(session: s);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: widget.canGoBack
            ? const BackButton()
            : Builder(
                builder: (ctx) => IconButton(
                    icon: const Icon(Icons.menu),
                    onPressed: () => Scaffold.of(ctx).openDrawer()),
              ),
        title: Text(s.schoolName ?? 'EduVerse',
            style: const TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          if (widget.canGoBack)
            Builder(
                builder: (ctx) => IconButton(
                    icon: const Icon(Icons.apps),
                    onPressed: () => Scaffold.of(ctx).openDrawer())),
          IconButton(
              tooltip: 'Toggle theme',
              onPressed: () => themeController.toggle(context),
              icon: const Icon(Icons.brightness_6)),
        ],
      ),
      drawer: _drawer(context),
      body: _body(),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (i) => setState(() => _index = i),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.dashboard_outlined), selectedIcon: Icon(Icons.dashboard), label: 'Home'),
          NavigationDestination(icon: Icon(Icons.school_outlined), selectedIcon: Icon(Icons.school), label: 'Students'),
          NavigationDestination(icon: Icon(Icons.how_to_reg_outlined), selectedIcon: Icon(Icons.how_to_reg), label: 'Attendance'),
          NavigationDestination(icon: Icon(Icons.account_balance_wallet_outlined), selectedIcon: Icon(Icons.account_balance_wallet), label: 'Payments'),
        ],
      ),
    );
  }

  Widget _drawer(BuildContext context) {
    final t = Theme.of(context).textTheme;
    return Drawer(
      child: SafeArea(
        child: ListView(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
              child: Row(children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(colors: [brand, brand2]),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.school_rounded, color: Colors.white),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(s.schoolName ?? 'EduVerse',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: t.titleMedium?.copyWith(fontWeight: FontWeight.w800)),
                      Text(s.name, style: t.bodySmall),
                    ],
                  ),
                ),
              ]),
            ),
            ListTile(
              leading: const Icon(Icons.how_to_reg),
              title: const Text('Attendance'),
              onTap: () {
                Navigator.pop(context);
                setState(() => _index = 2);
              },
            ),
            for (final g in groupOrder) ...[
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 14, 20, 4),
                child: Text(g.toUpperCase(),
                    style: t.labelSmall?.copyWith(letterSpacing: 1.2)),
              ),
              for (final r in resources.where((r) => r.group == g))
                ListTile(
                  leading: Icon(r.icon),
                  title: Text(r.title),
                  onTap: () {
                    Navigator.pop(context);
                    Navigator.of(context).push(MaterialPageRoute(
                        builder: (_) => ResourceScreen(session: s, def: r)));
                  },
                ),
            ],
            const Divider(),
            if (!widget.canGoBack)
              ListTile(
                leading: const Icon(Icons.logout),
                title: const Text('Logout'),
                onTap: () => db.auth.signOut(),
              ),
          ],
        ),
      ),
    );
  }
}
