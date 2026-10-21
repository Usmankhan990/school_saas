import 'package:flutter/material.dart';

import '../main.dart';
import '../resources.dart';
import '../session.dart';
import '../theme.dart';
import 'attendance_screen.dart';
import 'resource_screen.dart';
import 'teacher_home.dart';
import 'teacher_payslips.dart';

/// App shell for teacher accounts: My Day, student attendance, homework, payslips.
class TeacherShell extends StatefulWidget {
  final AppSession session;
  const TeacherShell({super.key, required this.session});

  @override
  State<TeacherShell> createState() => _TeacherShellState();
}

class _TeacherShellState extends State<TeacherShell> {
  int _index = 0;

  @override
  Widget build(BuildContext context) {
    final s = widget.session;
    final pages = <Widget>[
      TeacherHome(session: s),
      AttendanceScreen(session: s),
      ResourceScreen(
          key: const ValueKey('homework'),
          session: s,
          def: resourceByKey('homework')!,
          showAppBar: false),
      TeacherPayslips(session: s),
    ];
    return Scaffold(
      appBar: AppBar(
        title: Text(s.schoolName ?? 'EduVerse',
            style: const TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(
              tooltip: 'Toggle theme',
              onPressed: () => themeController.toggle(context),
              icon: const Icon(Icons.brightness_6)),
          IconButton(
              tooltip: 'Logout',
              onPressed: () => db.auth.signOut(),
              icon: const Icon(Icons.logout)),
        ],
      ),
      body: IndexedStack(index: _index, children: pages),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (i) => setState(() => _index = i),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.wb_sunny_outlined), selectedIcon: Icon(Icons.wb_sunny), label: 'My Day'),
          NavigationDestination(icon: Icon(Icons.how_to_reg_outlined), selectedIcon: Icon(Icons.how_to_reg), label: 'Attendance'),
          NavigationDestination(icon: Icon(Icons.edit_note_outlined), selectedIcon: Icon(Icons.edit_note), label: 'Homework'),
          NavigationDestination(icon: Icon(Icons.payments_outlined), selectedIcon: Icon(Icons.payments), label: 'Payslips'),
        ],
      ),
    );
  }
}
