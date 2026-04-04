import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../providers/auth_provider.dart';
import '../services/notification_service.dart';
import 'main_scaffold.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  bool _loading = false;
  bool _obscure = true;
  String? _error;

  Future<void> _login() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() { _loading = true; _error = null; });
    try {
      await context.read<AuthProvider>().login(_emailCtrl.text.trim(), _passCtrl.text);
      await NotificationService.init();
      if (!mounted) return;
      Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => const MainScaffold()));
    } catch (e) {
      setState(() { _error = e.toString().replaceAll('Exception: ', ''); });
    } finally {
      if (mounted) setState(() { _loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(
            children: [
              const SizedBox(height: 48),
              // Logo
              Container(
                width: 76,
                height: 76,
                decoration: BoxDecoration(color: AppTheme.primary, borderRadius: BorderRadius.circular(18)),
                child: const Center(
                  child: Text('IB', style: TextStyle(fontSize: 28, fontWeight: FontWeight.w800, color: Colors.white)),
                ),
              ),
              const SizedBox(height: 16),
              const Text('Installs Bank', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
              const SizedBox(height: 4),
              const Text('Sign in to your publisher account', style: TextStyle(fontSize: 14, color: AppTheme.textSecondary)),
              const SizedBox(height: 36),

              // Form
              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: AppTheme.border),
                ),
                child: Form(
                  key: _formKey,
                  child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
                    if (_error != null) ...[
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: const Color(0xFFFEE2E2),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(_error!, style: const TextStyle(color: AppTheme.danger, fontSize: 13)),
                      ),
                      const SizedBox(height: 16),
                    ],
                    TextFormField(
                      controller: _emailCtrl,
                      keyboardType: TextInputType.emailAddress,
                      decoration: const InputDecoration(labelText: 'Email', prefixIcon: Icon(Icons.email_outlined)),
                      validator: (v) => v!.isEmpty ? 'Enter your email' : null,
                    ),
                    const SizedBox(height: 14),
                    TextFormField(
                      controller: _passCtrl,
                      obscureText: _obscure,
                      decoration: InputDecoration(
                        labelText: 'Password',
                        prefixIcon: const Icon(Icons.lock_outline),
                        suffixIcon: IconButton(
                          icon: Icon(_obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                          onPressed: () => setState(() => _obscure = !_obscure),
                        ),
                      ),
                      validator: (v) => v!.isEmpty ? 'Enter your password' : null,
                    ),
                    const SizedBox(height: 20),
                    SizedBox(
                      height: 48,
                      child: ElevatedButton(
                        onPressed: _loading ? null : _login,
                        child: _loading
                            ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                            : const Text('Sign In'),
                      ),
                    ),
                  ]),
                ),
              ),
              const SizedBox(height: 16),
              const Text('For support, visit installsbank.com', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
            ],
          ),
        ),
      ),
    );
  }
}
