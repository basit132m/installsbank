import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../providers/auth_provider.dart';
import 'login_screen.dart';
import 'main_scaffold.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _init();
  }

  Future<void> _init() async {
    final auth = context.read<AuthProvider>();
    await auth.checkAuth();
    if (!mounted) return;
    await Future.delayed(const Duration(milliseconds: 800));
    if (!mounted) return;
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(
        builder: (_) => auth.isLoggedIn ? const MainScaffold() : const LoginScreen(),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.primary,
      body: Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 90,
              height: 90,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(22),
              ),
              child: const Center(
                child: Text('IB', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w800, color: AppTheme.primary)),
              ),
            ),
            const SizedBox(height: 20),
            const Text('Installs Bank', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: Colors.white)),
            const SizedBox(height: 8),
            const Text('Publisher Dashboard', style: TextStyle(fontSize: 14, color: Colors.white70)),
            const SizedBox(height: 40),
            const CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
          ],
        ),
      ),
    );
  }
}
