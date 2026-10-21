import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

const brand = Color(0xFF6366F1);
const brand2 = Color(0xFFC026D3);

/// Light / dark / system theme, remembered between launches.
class ThemeController extends ValueNotifier<ThemeMode> {
  ThemeController() : super(ThemeMode.system);

  Future<void> load() async {
    final p = await SharedPreferences.getInstance();
    switch (p.getString('theme')) {
      case 'light':
        value = ThemeMode.light;
      case 'dark':
        value = ThemeMode.dark;
      default:
        value = ThemeMode.system;
    }
  }

  Future<void> toggle(BuildContext context) async {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    value = isDark ? ThemeMode.light : ThemeMode.dark;
    final p = await SharedPreferences.getInstance();
    await p.setString('theme', isDark ? 'light' : 'dark');
  }
}

final themeController = ThemeController();

ThemeData buildTheme(Brightness b) {
  final dark = b == Brightness.dark;
  final scheme = ColorScheme.fromSeed(seedColor: brand, brightness: b);
  final surface = dark ? const Color(0xFF11142B) : Colors.white;
  return ThemeData(
    useMaterial3: true,
    colorScheme: scheme,
    scaffoldBackgroundColor:
        dark ? const Color(0xFF080A1C) : const Color(0xFFF3F5FB),
    cardTheme: CardThemeData(
      color: surface,
      elevation: 0,
      margin: EdgeInsets.zero,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
        side: BorderSide(
            color: dark ? Colors.white12 : const Color(0xFFE2E8F0)),
      ),
    ),
    appBarTheme: AppBarTheme(
      backgroundColor: surface,
      surfaceTintColor: Colors.transparent,
      elevation: 0,
    ),
    navigationBarTheme: NavigationBarThemeData(
      backgroundColor: surface,
      surfaceTintColor: Colors.transparent,
    ),
    drawerTheme: DrawerThemeData(backgroundColor: surface),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: dark ? Colors.white10 : Colors.white,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: BorderSide(
            color: dark ? Colors.white24 : const Color(0xFFCBD5E1)),
      ),
    ),
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 14),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      ),
    ),
  );
}
