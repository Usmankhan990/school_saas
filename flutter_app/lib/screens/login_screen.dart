import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

import '../main.dart';
import '../theme.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _email = TextEditingController();
  final _password = TextEditingController();
  bool _busy = false;
  bool _hide = true;
  String? _error;

  Future<void> _submit() async {
    setState(() {
      _busy = true;
      _error = null;
    });
    try {
      await db.auth.signInWithPassword(
          email: _email.text.trim(), password: _password.text);
    } on AuthException {
      setState(() => _error = 'Invalid email or password.');
    } catch (_) {
      setState(() => _error = 'Could not connect. Check your internet.');
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  void dispose() {
    _email.dispose();
    _password.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final t = Theme.of(context).textTheme;
    return Scaffold(
      body: Stack(
        children: [
          Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xFF4F46E5), Color(0xFF7C3AED), Color(0xFFC026D3)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
          ),
          SafeArea(
            child: Align(
              alignment: Alignment.topRight,
              child: IconButton(
                color: Colors.white,
                onPressed: () => themeController.toggle(context),
                icon: Icon(Theme.of(context).brightness == Brightness.dark
                    ? Icons.light_mode
                    : Icons.dark_mode),
              ),
            ),
          ),
          Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 420),
                child: Card(
                  child: Padding(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Row(children: [
                          Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                  colors: [brand, brand2]),
                              borderRadius: BorderRadius.circular(14),
                            ),
                            child: const Icon(Icons.school_rounded,
                                color: Colors.white),
                          ),
                          const SizedBox(width: 12),
                          Text('EduVerse',
                              style: t.titleLarge
                                  ?.copyWith(fontWeight: FontWeight.w800)),
                        ]),
                        const SizedBox(height: 22),
                        Text('Welcome back',
                            style: t.headlineSmall
                                ?.copyWith(fontWeight: FontWeight.w800)),
                        const SizedBox(height: 4),
                        Text('Sign in to your school or platform account.',
                            style: t.bodyMedium),
                        const SizedBox(height: 20),
                        TextField(
                          controller: _email,
                          keyboardType: TextInputType.emailAddress,
                          autofillHints: const [AutofillHints.email],
                          decoration: const InputDecoration(
                              labelText: 'Email',
                              prefixIcon: Icon(Icons.mail_outline)),
                        ),
                        const SizedBox(height: 14),
                        TextField(
                          controller: _password,
                          obscureText: _hide,
                          onSubmitted: (_) => _submit(),
                          autofillHints: const [AutofillHints.password],
                          decoration: InputDecoration(
                            labelText: 'Password',
                            prefixIcon: const Icon(Icons.lock_outline),
                            suffixIcon: IconButton(
                              icon: Icon(_hide
                                  ? Icons.visibility_off
                                  : Icons.visibility),
                              onPressed: () => setState(() => _hide = !_hide),
                            ),
                          ),
                        ),
                        if (_error != null) ...[
                          const SizedBox(height: 12),
                          Text(_error!,
                              style: TextStyle(
                                  color: Theme.of(context).colorScheme.error)),
                        ],
                        const SizedBox(height: 20),
                        FilledButton(
                          onPressed: _busy ? null : _submit,
                          child: _busy
                              ? const SizedBox(
                                  height: 18,
                                  width: 18,
                                  child: CircularProgressIndicator(strokeWidth: 2))
                              : const Text('Sign in'),
                        ),
                        const SizedBox(height: 12),
                        Text(
                          'New school or new teacher? Sign up on the EduVerse website, then sign in here.',
                          textAlign: TextAlign.center,
                          style: t.bodySmall,
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
