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
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(color: Colors.black.withAlpha(12), blurRadius: 20, offset: const Offset(0, -4))],
        ),
        child: SafeArea(
          top: false,
          child: NavigationBar(
            selectedIndex: _index,
            onDestinationSelected: (i) => setState(() => _index = i),
            backgroundColor: Colors.white,
            surfaceTintColor: Colors.white,
            indicatorColor: AppTheme.primaryLight,
            elevation: 0,
            height: 64,
            labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
            destinations: [
              _dest(Icons.dashboard_outlined, Icons.dashboard_rounded, 'Dashboard'),
              _dest(Icons.bar_chart_outlined, Icons.bar_chart_rounded, 'Stats'),
              _dest(Icons.account_balance_wallet_outlined, Icons.account_balance_wallet_rounded, 'Payments'),
              _dest(Icons.headset_mic_outlined, Icons.headset_mic_rounded, 'Support'),
              _dest(Icons.person_outline_rounded, Icons.person_rounded, 'Profile'),
            ],
          ),
        ),
      ),
    );
  }

  NavigationDestination _dest(IconData icon, IconData selectedIcon, String label) {
    return NavigationDestination(
      icon: Icon(icon, color: AppTheme.textSecondary),
      selectedIcon: Icon(selectedIcon, color: AppTheme.primary),
      label: label,
    );
  }
}
