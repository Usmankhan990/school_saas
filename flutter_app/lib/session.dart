import 'package:supabase_flutter/supabase_flutter.dart';

/// The signed-in user's profile plus the school they are working in.
class AppSession {
  final String userId;
  final String name;
  final String role;
  final String? schoolId;
  final String? schoolName;
  final String currency;

  const AppSession({
    required this.userId,
    required this.name,
    required this.role,
    this.schoolId,
    this.schoolName,
    this.currency = 'PKR',
  });

  bool get isPlatformAdmin => role == 'platform_admin';

  AppSession withSchool(Map<String, dynamic> school) => AppSession(
        userId: userId,
        name: name,
        role: role,
        schoolId: school['id'] as String,
        schoolName: school['name'] as String,
        currency: (school['currency'] as String?) ?? 'PKR',
      );

  static Future<AppSession?> load(SupabaseClient db) async {
    final user = db.auth.currentUser;
    if (user == null) return null;
    final profile =
        await db.from('profiles').select().eq('id', user.id).maybeSingle();
    if (profile == null || profile['status'] != 'active') return null;
    final base = AppSession(
      userId: user.id,
      name: (profile['name'] as String?) ?? 'User',
      role: profile['role'] as String,
    );
    final schoolId = profile['school_id'] as String?;
    if (schoolId == null) return base;
    final school =
        await db.from('schools').select().eq('id', schoolId).maybeSingle();
    if (school == null || school['status'] != 'active') return null;
    return base.withSchool(school);
  }
}
