import 'package:flutter/material.dart';
import '../config/theme.dart';
import 'dashboard_screen.dart';
import 'stats_screen.dart';
import 'withdrawals_screen.dart';
import 'support/support_list_screen.dart';
import 'profile_screen.dart';

class MainScaffold extends StatefulWidget {
  const MainScaffold({super.key});

  @override
  State<MainScaffold> createState() => _MainScaffoldState();
}

class _MainScaffoldState extends State<MainScaffold> {
  int _index = 0;

  final List<Widget> _screens = const [
    DashboardScreen(),
    StatsScreen(),
    WithdrawalsScreen(),
    SupportListScreen(),
    ProfileScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(index: _index, children: _screens),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (i) => setState(() => _index = i),
        backgroundColor: Colors.white,
        surfaceTintColor: Colors.white,
        indicatorColor: AppTheme.primaryLight,
        destinations: const [
          NavigationDestination(icon: Icon(Icons.dashboard_outlined), selectedIcon: Icon(Icons.dashboard), label: 'Dashboard'),
          NavigationDestination(icon: Icon(Icons.bar_chart_outlined), selectedIcon: Icon(Icons.bar_chart), label: 'Stats'),
          NavigationDestination(icon: Icon(Icons.account_balance_wallet_outlined), selectedIcon: Icon(Icons.account_balance_wallet), label: 'Payments'),
          NavigationDestination(icon: Icon(Icons.headset_mic_outlined), selectedIcon: Icon(Icons.headset_mic), label: 'Support'),
          NavigationDestination(icon: Icon(Icons.person_outline), selectedIcon: Icon(Icons.person), label: 'Profile'),
        ],
      ),
    );
  }
}
