import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../services/api_service.dart';
import '../widgets/loading_widget.dart';
import '../widgets/section_card.dart';

class StatsScreen extends StatefulWidget {
  const StatsScreen({super.key});

  @override
  State<StatsScreen> createState() => _StatsScreenState();
}

class _StatsScreenState extends State<StatsScreen> with AutomaticKeepAliveClientMixin {
  String _period = '7';
  Map<String, dynamic>? _data;
  bool _loading = true;
  String? _error;

  @override
  bool get wantKeepAlive => true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() { _loading = _data == null; _error = null; });
    final res = await ApiService.get('/stats?period=$_period');
    if (!mounted) return;
    if (res.ok) {
      setState(() { _data = res.data; _loading = false; });
    } else {
      setState(() { _error = res.message; _loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Scaffold(
      appBar: AppBar(title: const Text('Statistics'), actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _load)]),
      body: _loading
          ? const LoadingWidget()
          : _error != null
              ? ErrorWidget2(message: _error!, onRetry: _load)
              : RefreshIndicator(onRefresh: _load, color: AppTheme.primary, child: _buildBody()),
    );
  }

  Widget _buildBody() {
    final showEarnings = _data!['show_earnings'] as bool;
    final totals = _data!['totals'] as Map;
    final daily = (_data!['daily'] as List).cast<Map>();
    final countries = (_data!['country_breakdown'] as List).cast<Map>();
    final links = (_data!['link_stats'] as List).cast<Map>();

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        // Period filter
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: Row(children: [
            for (final p in [('1', 'Today'), ('7', '7 Days'), ('30', '30 Days'), ('90', '90 Days')])
              Padding(
                padding: const EdgeInsets.only(right: 8),
                child: ChoiceChip(
                  label: Text(p.$2),
                  selected: _period == p.$1,
                  onSelected: (_) { setState(() { _period = p.$1; }); _load(); },
                  selectedColor: AppTheme.primaryLight,
                  labelStyle: TextStyle(color: _period == p.$1 ? AppTheme.primary : AppTheme.textSecondary, fontWeight: FontWeight.w600),
                ),
              ),
          ]),
        ),
        const SizedBox(height: 16),

        // Totals
        Row(children: [
          Expanded(child: _totalCard('Unique Clicks', _fmt(totals['clicks']), AppTheme.textPrimary)),
          if (showEarnings) ...[
            const SizedBox(width: 12),
            Expanded(child: _totalCard('Earnings', '\$${(totals['earnings'] ?? 0).toStringAsFixed(4)}', AppTheme.primary)),
          ]
        ]),
        const SizedBox(height: 16),

        // Chart
        SectionCard(
          title: 'Daily Performance',
          child: SizedBox(height: 180, child: _buildChart(daily)),
        ),
        const SizedBox(height: 16),

        // Daily table
        SectionCard(
          title: 'Daily Breakdown',
          padding: EdgeInsets.zero,
          child: Column(children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              child: Row(children: [
                const Expanded(child: Text('Date', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppTheme.textSecondary))),
                const Text('CLICKS', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppTheme.textSecondary)),
                if (showEarnings) const SizedBox(width: 60, child: Text('EARN', textAlign: TextAlign.right, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppTheme.textSecondary))),
              ]),
            ),
            const Divider(height: 1),
            ...daily.reversed.take(30).map((d) => _dailyRow(d, showEarnings)),
          ]),
        ),
        const SizedBox(height: 16),

        // Per-link stats
        if (links.isNotEmpty) ...[
          SectionCard(
            title: 'Performance by Link',
            padding: EdgeInsets.zero,
            child: Column(children: links.map((l) => _linkRow(l, showEarnings)).toList()),
          ),
          const SizedBox(height: 16),
        ],

        // Countries
        if (countries.isNotEmpty) ...[
          SectionCard(
            title: 'Traffic by Country',
            padding: EdgeInsets.zero,
            child: Column(children: countries.take(15).map((c) => _countryRow(c, showEarnings)).toList()),
          ),
        ],
        const SizedBox(height: 80),
      ],
    );
  }

  Widget _totalCard(String label, String value, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppTheme.border),
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(label, style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
        const SizedBox(height: 4),
        Text(value, style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: color)),
      ]),
    );
  }

  Widget _buildChart(List<Map> daily) {
    if (daily.isEmpty) return const Center(child: Text('No data'));
    final spots = daily.asMap().entries.map((e) => FlSpot(e.key.toDouble(), (e.value['clicks'] as num).toDouble())).toList();
    return LineChart(LineChartData(
      lineBarsData: [
        LineChartBarData(spots: spots, isCurved: true, color: AppTheme.primary, barWidth: 2, dotData: const FlDotData(show: false), belowBarData: BarAreaData(show: true, color: AppTheme.primary.withAlpha(25))),
      ],
      titlesData: FlTitlesData(
        bottomTitles: AxisTitles(sideTitles: SideTitles(showTitles: true, interval: (daily.length / 5).ceilToDouble(), getTitlesWidget: (v, _) {
          final i = v.toInt(); if (i < 0 || i >= daily.length) return const SizedBox.shrink();
          return Text(daily[i]['date'].toString().split(' ').last, style: const TextStyle(fontSize: 9, color: AppTheme.textSecondary));
        })),
        leftTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
        topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
        rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
      ),
      gridData: FlGridData(show: true, drawVerticalLine: false, getDrawingHorizontalLine: (_) => const FlLine(color: Color(0xFFF3F4F6), strokeWidth: 1)),
      borderData: FlBorderData(show: false),
    ));
  }

  Widget _dailyRow(Map d, bool showEarnings) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      child: Row(children: [
        Expanded(child: Text(d['date'], style: const TextStyle(fontSize: 13, color: AppTheme.textSecondary))),
        Text(_fmt(d['clicks']), style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
        if (showEarnings) SizedBox(width: 70, child: Text('\$${(d['earnings'] as num).toStringAsFixed(4)}', textAlign: TextAlign.right, style: const TextStyle(color: AppTheme.primary, fontSize: 13))),
      ]),
    );
  }

  Widget _linkRow(Map l, bool showEarnings) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      child: Row(children: [
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(l['name'], style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
          Text(l['code'], style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary, fontFamily: 'monospace')),
        ])),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
          decoration: BoxDecoration(
            color: l['active'] == true ? AppTheme.primaryLight : const Color(0xFFFEE2E2),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Text(l['active'] == true ? 'Active' : 'Off', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: l['active'] == true ? AppTheme.primary : AppTheme.danger)),
        ),
        const SizedBox(width: 10),
        Text(_fmt(l['clicks']), style: const TextStyle(fontWeight: FontWeight.w700)),
        if (showEarnings && l['earnings'] != null) ...[const SizedBox(width: 10), Text('\$${(l['earnings'] as num).toStringAsFixed(4)}', style: const TextStyle(color: AppTheme.primary, fontSize: 13))],
      ]),
    );
  }

  Widget _countryRow(Map c, bool showEarnings) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      child: Row(children: [
        Expanded(child: Text(c['country'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600))),
        Text(_fmt(c['clicks']), style: const TextStyle(fontWeight: FontWeight.w700)),
        if (showEarnings && c['earnings'] != null) ...[const SizedBox(width: 12), Text('\$${(c['earnings'] as num).toStringAsFixed(4)}', style: const TextStyle(color: AppTheme.primary, fontSize: 13))],
      ]),
    );
  }

  String _fmt(dynamic v) => v == null ? '0' : (v as num).toInt().toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]},');
}
