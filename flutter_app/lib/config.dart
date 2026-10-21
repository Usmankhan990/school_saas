// Supabase project settings. The anon key is a public client key (data is
// protected by Row Level Security). Override at build time with
// --dart-define=SUPABASE_URL=... --dart-define=SUPABASE_ANON_KEY=...
const supabaseUrl = String.fromEnvironment(
  'SUPABASE_URL',
  defaultValue: 'https://luyvmptpamzvnobyeppw.supabase.co',
);
const supabaseAnonKey = String.fromEnvironment(
  'SUPABASE_ANON_KEY',
  defaultValue: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imx1eXZtcHRwYW16dm5vYnllcHB3Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3OTI1ODQ1NDEsImV4cCI6MjEwODE2MDU0MX0.el4CZ5Da4cmN2-IBZLy7q41Kr7b00q9B1CG050HcUoU',
);

/// The deployed EduVerse website. Teacher check-in / check-out goes through its API
/// so the server (not the phone) decides the time.
const webBaseUrl = String.fromEnvironment(
  'WEB_BASE_URL',
  defaultValue: 'https://web-cdh13.vercel.app',
);
