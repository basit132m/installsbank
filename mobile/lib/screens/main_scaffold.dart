import 'dart:async';
import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../services/api_service.dart';
import '../services/notification_service.dart';
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
  bool _hasUnreadChat = false;
  Timer? _badgeTimer;
  int _lastSeenChatId = 0;

  final List<Widget> _screens = const [
    DashboardScreen(),
    StatsScreen(),
    WithdrawalsScreen(),
    SupportListScreen(),
    ProfileScreen(),
  ];

  @override
  void initState() {
    super.initState();
    NotificationService.startChatPolling();
    _initChatBadge();
  }

  @override
  void dispose() {
    _badgeTimer?.cancel();
    NotificationService.stopChatPolling();
    super.dispose();
  }

  Future<void> _initChatBadge() async {
    // Get initial baseline silently
    await _checkUnreadChat(silent: true);
    _badgeTimer = Timer.periodic(const Duration(seconds: 10), (_) => _checkUnreadChat());
  }

  Future<void> _checkUnreadChat({bool silent = false}) async {
    try {
      final res = await ApiService.get('/chat/messages');
      if (!res.ok || !mounted) return;
      final msgs = (res.data['messages'] as List?) ?? [];
      if (msgs.isEmpty) return;

      final latest = msgs.last as Map;
      final latestId = latest['id'] as int? ?? 0;
      final isStaff = latest['is_staff'] == true || latest['is_staff'] == 1;

      if (silent) {
        _lastSeenChatId = latestId;
        return;
      }

      // Show badge if latest message is from staff and we haven't seen it
      if (latestId > _lastSeenChatId && isStaff) {
        setState(() => _hasUnreadChat = true);
      }
    } catch (_) {}
  }

  void _clearChatBadge() {
    if (_hasUnreadChat) {
      setState(() => _hasUnreadChat = false);
    }
    // Mark current latest as seen
    ApiService.get('/chat/messages').then((res) {
      if (!res.ok) return;
      final msgs = (res.data['messages'] as List?) ?? [];
      if (msgs.isNotEmpty) {
        _lastSeenChatId = (msgs.last as Map)['id'] as int? ?? 0;
      }
    });
  }

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
            onDestinationSelected: (i) {
              setState(() => _index = i);
              // Clear chat badge when switching to support tab
              if (i == 3) _clearChatBadge();
            },
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
              _destWithBadge(Icons.headset_mic_outlined, Icons.headset_mic_rounded, 'Support', _hasUnreadChat),
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

  NavigationDestination _destWithBadge(IconData icon, IconData selectedIcon, String label, bool showBadge) {
    return NavigationDestination(
      icon: Badge(
        isLabelVisible: showBadge,
        backgroundColor: AppTheme.danger,
        smallSize: 8,
        child: Icon(icon, color: AppTheme.textSecondary),
      ),
      selectedIcon: Badge(
        isLabelVisible: showBadge,
        backgroundColor: AppTheme.danger,
        smallSize: 8,
        child: Icon(selectedIcon, color: AppTheme.primary),
      ),
      label: label,
    );
  }
}
