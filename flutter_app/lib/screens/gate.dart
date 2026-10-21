import 'dart:async';

import 'package:flutter/material.dart';

import '../main.dart';
import '../session.dart';
import 'home_shell.dart';
import 'login_screen.dart';
import 'platform_screen.dart';

/// Decides what to show: login, platform admin console, or the school app.
class Gate extends StatefulWidget {
  const Gate({super.key});

  @override
  State<Gate> createState() => _GateState();
}

class _GateState extends State<Gate> {
  late Future<AppSession?> _future = AppSession.load(db);
  late final StreamSubscription _sub;

  @override
  void initState() {
    super.initState();
    _sub = db.auth.onAuthStateChange.listen((_) {
      if (mounted) setState(() => _future = AppSession.load(db));
    });
  }

  @override
  void dispose() {
    _sub.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<AppSession?>(
      future: _future,
      builder: (context, snap) {
        if (snap.connectionState != ConnectionState.done) {
          return const Scaffold(body: Center(child: CircularProgressIndicator()));
        }
        final session = snap.data;
        if (session == null) {
          if (db.auth.currentUser != null) {
            // signed in but no usable profile / inactive school
            WidgetsBinding.instance.addPostFrameCallback((_) => db.auth.signOut());
          }
          return const LoginScreen();
        }
        if (session.isPlatformAdmin) return PlatformScreen(session: session);
        return HomeShell(session: session);
      },
    );
  }
}
