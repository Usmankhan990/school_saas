import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

import 'config.dart';
import 'screens/gate.dart';
import 'theme.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Supabase.initialize(url: supabaseUrl, anonKey: supabaseAnonKey);
  await themeController.load();
  runApp(const EduVerseApp());
}

SupabaseClient get db => Supabase.instance.client;

class EduVerseApp extends StatelessWidget {
  const EduVerseApp({super.key});

  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder<ThemeMode>(
      valueListenable: themeController,
      builder: (_, mode, __) => MaterialApp(
        title: 'EduVerse',
        debugShowCheckedModeBanner: false,
        theme: buildTheme(Brightness.light),
        darkTheme: buildTheme(Brightness.dark),
        themeMode: mode,
        home: const Gate(),
      ),
    );
  }
}
