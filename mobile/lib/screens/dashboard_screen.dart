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
    final res = await ApiService.get('/dashboard');
    if (!mounted) return;
    if (res.ok) {
      setState(() { _data = res.data; _loading = false; });
    } else {
      setState(() { _error = res.message; _loading = false; });
    }
  }

  void _startLivePolling() {
    _fetchLive();
    _liveTimer = Timer.periodic(const Duration(seconds: 30), (_) => _fetchLive());
  }

  Future<void> _fetchLive() async {
    final res = await ApiService.get('/live-stats');
    if (mounted && res.ok) setState(() => _live = res.data);
  }

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
      ),
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
    final stats = _data!['stats'] as Map;
    final chart = (_data!['chart'] as List).cast<Map>();
    final country = (_data!['country_breakdown'] as List).cast<Map>();
    final showEarnings = _data!['show_earnings'] as bool;
    final badge = _live?['badge'];
    final accountStatus = _data!['account_status'];
    final contract = _data!['contract'];

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        // Status banner
        if (accountStatus == 'pending') _pendingBanner(),

        // Live badge + counter
        if (_live != null) ...[
          Row(children: [
            _buildBadge(badge),
            const SizedBox(width: 10),
            _buildLiveCounter(),
          ]),
          const SizedBox(height: 16),
        ],

        // Stats grid
        _statsGrid(stats, showEarnings),
        const SizedBox(height: 16),

        // Chart
        SectionCard(
          title: 'Clicks — Last 7 Days',
          child: SizedBox(height: 180, child: _buildChart(chart, showEarnings)),
        ),
        const SizedBox(height: 16),

        // Country breakdown
        if (country.isNotEmpty) ...[
          SectionCard(
            title: 'Traffic by Country (Today)',
            padding: const EdgeInsets.all(0),
            child: Column(
              children: country.take(8).map((c) => _countryRow(c, showEarnings)).toList(),
            ),
          ),
          const SizedBox(height: 16),
        ],

        // Contract
        SectionCard(
          title: 'Contract Status',
          child: contract != null
              ? Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: AppTheme.primaryLight,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    const Text('Active Contract', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
                    const SizedBox(height: 4),
                    Text(
                      contract['type'] == 'per_click'
                          ? '\$${contract['rate']} per 1,000 clicks'
                          : '\$${contract['rate']}/day fixed',
                      style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppTheme.primary),
                    ),
                  ]),
                )
              : const Text('No active contract yet.', style: TextStyle(color: AppTheme.textSecondary, fontSize: 14)),
        ),
        const SizedBox(height: 80),
      ],
    );
  }

  Widget _pendingBanner() {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFFFFF7ED),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFFED7AA)),
      ),
      child: const Row(children: [
        Icon(Icons.access_time, color: Color(0xFFF59E0B)),
        SizedBox(width: 10),
        Expanded(
          child: Text(
            'Your account is pending approval. Our team will review it in 1–3 business days.',
            style: TextStyle(fontSize: 13, color: Color(0xFF78350F)),
          ),
        ),
      ]),
    );
  }

  Widget _buildBadge(Map? badge) {
    if (badge == null) return const SizedBox.shrink();
    final colorHex = badge['color'] as String;
    final color = Color(int.parse(colorHex.replaceFirst('#', '0xFF')));
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
      decoration: BoxDecoration(
        color: color.withAlpha(25),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withAlpha(60)),
      ),
      child: Row(mainAxisSize: MainAxisSize.min, children: [
        Container(width: 7, height: 7, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
        const SizedBox(width: 6),
        Text(badge['label'], style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: color)),
      ]),
    );
  }

  Widget _buildLiveCounter() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppTheme.border),
      ),
      child: Row(mainAxisSize: MainAxisSize.min, children: [
        Container(width: 7, height: 7, decoration: const BoxDecoration(color: AppTheme.primary, shape: BoxShape.circle)),
        const SizedBox(width: 6),
        Text(
          '${_live?['clicks_today'] ?? 0} today  ·  ${_live?['clicks_last_hour'] ?? 0}/hr',
          style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppTheme.textPrimary),
        ),
      ]),
    );
  }

  Widget _statsGrid(Map stats, bool showEarnings) {
    final items = <Widget>[
      StatCard(label: 'Clicks Today', value: _fmt(stats['clicks_today'])),
      StatCard(label: 'This Week', value: _fmt(stats['clicks_this_week'])),
      StatCard(label: 'This Month', value: _fmt(stats['clicks_this_month'])),
    ];
    if (showEarnings) {
      items.add(StatCard(label: 'Earnings Today', value: '\$${(stats['earnings_today'] ?? 0).toStringAsFixed(4)}', valueColor: AppTheme.primary));
      items.add(StatCard(label: 'Balance', value: '\$${(stats['balance'] ?? 0).toStringAsFixed(2)}', valueColor: AppTheme.primary, borderColor: AppTheme.primary));
    }
    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisSpacing: 10,
      mainAxisSpacing: 10,
      childAspectRatio: 1.7,
      children: items,
    );
  }

  Widget _buildChart(List<Map> chart, bool showEarnings) {
    if (chart.isEmpty) return const Center(child: Text('No data'));
    final spots = chart.asMap().entries.map((e) => FlSpot(e.key.toDouble(), (e.value['clicks'] as num).toDouble())).toList();
    return LineChart(LineChartData(
      lineBarsData: [
        LineChartBarData(
          spots: spots,
          isCurved: true,
          color: AppTheme.primary,
          barWidth: 2.5,
          dotData: const FlDotData(show: false),
          belowBarData: BarAreaData(show: true, color: AppTheme.primary.withAlpha(25)),
        ),
      ],
      titlesData: FlTitlesData(
        bottomTitles: AxisTitles(sideTitles: SideTitles(showTitles: true, interval: 2, getTitlesWidget: (v, _) {
          final idx = v.toInt();
          if (idx < 0 || idx >= chart.length) return const SizedBox.shrink();
          return Text(chart[idx]['date'].toString().split(' ').last, style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary));
        })),
        leftTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
        topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
        rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
      ),
      gridData: FlGridData(show: true, drawVerticalLine: false, getDrawingHorizontalLine: (_) => const FlLine(color: Color(0xFFF3F4F6), strokeWidth: 1)),
      borderData: FlBorderData(show: false),
    ));
  }

  Widget _countryRow(Map c, bool showEarnings) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      child: Row(children: [
        Expanded(child: Text(c['country'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14))),
        Text(_fmt(c['clicks']), style: const TextStyle(fontWeight: FontWeight.w700)),
        if (showEarnings && c['earnings'] != null) ...[
          const SizedBox(width: 12),
          Text('\$${(c['earnings'] as num).toStringAsFixed(4)}', style: const TextStyle(color: AppTheme.primary, fontSize: 13)),
        ]
      ]),
    );
  }

  String _fmt(dynamic v) => v == null ? '0' : (v as num).toInt().toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]},');
}
