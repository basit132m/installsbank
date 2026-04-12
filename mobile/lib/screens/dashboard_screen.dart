import 'dart:async';
import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../services/api_service.dart';
import '../widgets/loading_widget.dart';
import '../widgets/section_card.dart';
import '../widgets/stat_card.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> with AutomaticKeepAliveClientMixin {
  Map<String, dynamic>? _data;
  Map<String, dynamic>? _live;
  bool _loading = true;
  String? _error;
  Timer? _liveTimer;

  @override
  bool get wantKeepAlive => true;

  @override
  void initState() {
    super.initState();
    _load();
    _startLivePolling();
  }

  @override
  void dispose() {
    _liveTimer?.cancel();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() { _loading = _data == null; _error = null; });
    try {
      final res = await ApiService.get('/dashboard');
      if (!mounted) return;
      if (res.ok) {
        setState(() { _data = res.data as Map<String, dynamic>; _loading = false; });
      } else {
        setState(() { _error = res.message; _loading = false; });
      }
    } catch (e) {
      if (mounted) setState(() { _error = e.toString(); _loading = false; });
    }
  }

  void _startLivePolling() {
    _fetchLive();
    _liveTimer = Timer.periodic(const Duration(seconds: 30), (_) => _fetchLive());
  }

  Future<void> _fetchLive() async {
    try {
      final res = await ApiService.get('/live-stats');
      if (mounted && res.ok) setState(() => _live = res.data as Map<String, dynamic>);
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: _loading
          ? const LoadingWidget()
          : _error != null
              ? ErrorWidget2(message: _error!, onRetry: _load)
              : RefreshIndicator(
                  onRefresh: _load,
                  color: AppTheme.primary,
                  child: _buildBody(),
                ),
    );
  }

  Widget _buildBody() {
    try {
      final stats = (_data!['stats'] as Map<String, dynamic>?) ?? {};
      final chart = ((_data!['chart'] as List?) ?? []).map((e) => Map<String, dynamic>.from(e as Map)).toList();
      final country = ((_data!['country_breakdown'] as List?) ?? []).map((e) => Map<String, dynamic>.from(e as Map)).toList();
      final showEarnings = (_data!['show_earnings'] as bool?) ?? false;
      final accountStatus = _data!['account_status'] as String? ?? '';
      final contract = _data!['contract'] as Map?;
      return ListView(
        padding: EdgeInsets.zero,
        children: [
          _buildHeader(stats, showEarnings, accountStatus),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 20, 16, 0),
            child: _statsGrid(stats, showEarnings),
          ),
          const SizedBox(height: 16),
          if (_live != null)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
              child: _liveRow(),
            ),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
            child: SectionCard(
              title: 'Last 7 Days',
              child: SizedBox(height: 160, child: _buildChart(chart)),
            ),
          ),
          if (country.isNotEmpty)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
              child: SectionCard(
                title: 'Traffic by Country (Today)',
                padding: EdgeInsets.zero,
                child: Column(
                  children: [
                    ...country.take(6).map((c) => _countryRow(c, showEarnings)),
                  ],
                ),
              ),
            ),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
            child: _contractCard(contract),
          ),
          const SizedBox(height: 80),
        ],
      );
    } catch (e) {
      return ErrorWidget2(message: 'Failed to render dashboard: $e', onRetry: _load);
    }
  }

  Widget _buildHeader(Map stats, bool showEarnings, String accountStatus) {
    final clicksToday = _toInt(stats['clicks_today']);
    return Container(
      decoration: const BoxDecoration(gradient: AppTheme.primaryGradient),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 28),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Row(children: [
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Publisher Dashboard', style: TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.w500)),
                  const SizedBox(height: 2),
                  const Text('Installs Bank', style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w800, letterSpacing: -0.5)),
                ]),
              ),
              GestureDetector(
                onTap: _load,
                child: Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(color: Colors.white.withAlpha(30), borderRadius: BorderRadius.circular(10)),
                  child: const Icon(Icons.refresh_rounded, color: Colors.white, size: 20),
                ),
              ),
            ]),
            const SizedBox(height: 20),
            Row(children: [
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Clicks Today', style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w500)),
                  const SizedBox(height: 4),
                  Text(_fmtNum(clicksToday), style: const TextStyle(color: Colors.white, fontSize: 36, fontWeight: FontWeight.w900, letterSpacing: -1.5)),
                ]),
              ),
              if (accountStatus == 'pending')
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(color: Colors.white.withAlpha(25), borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.white.withAlpha(60))),
                  child: const Text('Pending Review', style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w600)),
                )
              else if (showEarnings)
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(color: Colors.white.withAlpha(20), borderRadius: BorderRadius.circular(12)),
                  child: Column(children: [
                    const Text('Earnings Today', style: TextStyle(color: Colors.white70, fontSize: 10, fontWeight: FontWeight.w500)),
                    const SizedBox(height: 2),
                    Text('\$${_toDouble(stats['earnings_today']).toStringAsFixed(4)}', style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w800)),
                  ]),
                ),
            ]),
          ]),
        ),
      ),
    );
  }

  Widget _liveRow() {
    final showEarnings = (_live?['show_earnings'] as bool?) ?? false;
    final clicksToday  = _live?['clicks_today'] ?? 0;
    final lastHour     = _live?['clicks_last_hour'] ?? 0;
    final earnings     = _toDouble(_live?['earnings_today']);

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
        boxShadow: AppTheme.cardShadow,
      ),
      child: Row(children: [
        // Live indicator
        Container(width: 8, height: 8, decoration: const BoxDecoration(color: AppTheme.primary, shape: BoxShape.circle)),
        const SizedBox(width: 8),
        const Text('Live', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.primary)),
        const SizedBox(width: 16),
        // Today
        Expanded(
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Today', style: TextStyle(fontSize: 10, color: AppTheme.textSecondary, fontWeight: FontWeight.w500)),
            Text(_fmt(clicksToday), style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
          ]),
        ),
        // Last hour
        Expanded(
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Last Hour', style: TextStyle(fontSize: 10, color: AppTheme.textSecondary, fontWeight: FontWeight.w500)),
            Text(_fmt(lastHour), style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
          ]),
        ),
        // Earnings today (only for per-click publishers)
        if (showEarnings)
          Expanded(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('Earnings', style: TextStyle(fontSize: 10, color: AppTheme.textSecondary, fontWeight: FontWeight.w500)),
              Text('\$${earnings.toStringAsFixed(4)}', style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: AppTheme.primary)),
            ]),
          ),
      ]),
    );
  }

  Widget _statsGrid(Map stats, bool showEarnings) {
    final items = <Widget>[
      StatCard(
        label: 'This Week',
        value: _fmt(stats['clicks_this_week']),
        icon: Icons.date_range_outlined,
        gradient: AppTheme.blueGradient,
        valueColor: AppTheme.info,
      ),
      StatCard(
        label: 'This Month',
        value: _fmt(stats['clicks_this_month']),
        icon: Icons.calendar_today_outlined,
        gradient: AppTheme.purpleGradient,
        valueColor: AppTheme.purple,
      ),
    ];
    if (showEarnings) {
      items.add(StatCard(
        label: 'Balance',
        value: '\$${_toDouble(stats['balance']).toStringAsFixed(2)}',
        icon: Icons.account_balance_wallet_rounded,
        gradient: AppTheme.primaryGradient,
        valueColor: AppTheme.primary,
      ));
      items.add(StatCard(
        label: 'Pending',
        value: '\$${_toDouble(stats['pending_balance']).toStringAsFixed(2)}',
        icon: Icons.hourglass_bottom_rounded,
        gradient: AppTheme.orangeGradient,
        valueColor: AppTheme.warning,
      ));
    }
    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisSpacing: 12,
      mainAxisSpacing: 12,
      childAspectRatio: 1.6,
      children: items,
    );
  }

  Widget _buildChart(List<Map<String, dynamic>> chart) {
    if (chart.isEmpty) return const Center(child: Text('No data yet', style: TextStyle(color: AppTheme.textSecondary)));
    final spots = chart.asMap().entries.map((e) => FlSpot(e.key.toDouble(), _toDouble(e.value['clicks']))).toList();
    return LineChart(LineChartData(
      lineBarsData: [
        LineChartBarData(
          spots: spots,
          isCurved: true,
          color: AppTheme.primary,
          barWidth: 3,
          dotData: const FlDotData(show: false),
          belowBarData: BarAreaData(
            show: true,
            gradient: LinearGradient(
              colors: [AppTheme.primary.withAlpha(60), AppTheme.primary.withAlpha(0)],
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
            ),
          ),
        ),
      ],
      titlesData: FlTitlesData(
        bottomTitles: AxisTitles(sideTitles: SideTitles(showTitles: true, interval: 2, getTitlesWidget: (v, _) {
          final idx = v.toInt();
          if (idx < 0 || idx >= chart.length) return const SizedBox.shrink();
          return Padding(
            padding: const EdgeInsets.only(top: 4),
            child: Text(chart[idx]['date']?.toString() ?? '', style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary)),
          );
        })),
        leftTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
        topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
        rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
      ),
      gridData: FlGridData(
        show: true,
        drawVerticalLine: false,
        getDrawingHorizontalLine: (_) => const FlLine(color: Color(0xFFEEF2F7), strokeWidth: 1),
      ),
      borderData: FlBorderData(show: false),
    ));
  }

  Widget _countryRow(Map<String, dynamic> c, bool showEarnings) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 11),
      decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: AppTheme.border, width: 0.5))),
      child: Row(children: [
        Expanded(
          child: Text(
            c['country']?.toString() ?? '',
            style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14, color: AppTheme.textPrimary),
          ),
        ),
        Text(_fmt(c['clicks']), style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
        if (showEarnings && c['earnings'] != null) ...[
          const SizedBox(width: 12),
          Text('\$${_toDouble(c['earnings']).toStringAsFixed(4)}', style: const TextStyle(color: AppTheme.primary, fontSize: 13, fontWeight: FontWeight.w600)),
        ]
      ]),
    );
  }

  Widget _contractCard(Map? contract) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
        boxShadow: AppTheme.cardShadow,
      ),
      child: Row(children: [
        Container(
          width: 44,
          height: 44,
          decoration: BoxDecoration(gradient: AppTheme.primaryGradient, borderRadius: BorderRadius.circular(12)),
          child: const Icon(Icons.description_rounded, color: Colors.white, size: 22),
        ),
        const SizedBox(width: 14),
        Expanded(
          child: contract != null
              ? Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Active Contract', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary, fontWeight: FontWeight.w500)),
                  const SizedBox(height: 3),
                  Text(
                    _contractLabel(contract),
                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: AppTheme.primary),
                  ),
                ])
              : Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('No Contract Yet', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppTheme.textPrimary)),
                  const SizedBox(height: 2),
                  const Text('Contact support to set up your contract', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
                ]),
        ),
        const Icon(Icons.chevron_right_rounded, color: AppTheme.textSecondary),
      ]),
    );
  }

  String _fmt(dynamic v) {
    if (v == null) return '0';
    final n = num.tryParse(v.toString()) ?? 0;
    return n.toInt().toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]},');
  }

  String _fmtNum(int n) => n.toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]},');

  String _contractLabel(Map contract) {
    final type = contract['type']?.toString() ?? '';
    final rate = contract['rate'];
    switch (type) {
      case 'per_click':
        return rate != null ? '\$$rate per 1,000 clicks' : 'Per Click';
      case 'fixed':
        return rate != null ? '\$$rate / day fixed' : 'Fixed Daily Rate';
      case 'installs_base':
        return 'Installs Base Contract';
      default:
        return type.isNotEmpty ? type.replaceAll('_', ' ').toUpperCase() : 'Active';
    }
  }

  int _toInt(dynamic v) => v == null ? 0 : (num.tryParse(v.toString()) ?? 0).toInt();
  double _toDouble(dynamic v) => v == null ? 0.0 : (num.tryParse(v.toString()) ?? 0).toDouble();
}
