# EduVerse – Flutter app

Mobile app (Android / iOS / web) for the EduVerse school system. It talks directly to the same
Supabase project as the Next.js site in `../web`, so data is shared. Row Level Security protects each school's data.

## Features
- Login (school admin / platform admin), light + dark theme (toggle in the app bar)
- Dashboard with 3D tilt stat cards and animated 3D header
- Bottom navigation: Home, Students, Attendance, Payments; drawer with all 20 modules
- Add / delete records in every module, swipe a card left to delete
- Attendance by class and date (Present / Absent / Late / Leave)
- Platform admin: list schools, enable / disable, open any school

New schools are registered on the website (it needs the server-side service key).

## Run
```bash
flutter pub get
flutter run                       # connected phone / emulator
flutter run -d chrome             # in the browser
```

Supabase settings are in `lib/config.dart` (public anon key). Override with:
`flutter run --dart-define=SUPABASE_URL=... --dart-define=SUPABASE_ANON_KEY=...`

## Build an Android APK
Needs the Android SDK (install Android Studio).
```bash
flutter build apk --release
```
Output: `build/app/outputs/flutter-apk/app-release.apk`
