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
      setState(() { _data = res.data as Map<String, dynamic>; _loading = false; });
    } else {
      setState(() { _error = res.message; _loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Statistics'),
        centerTitle: false,
        actions: [IconButton(icon: const Icon(Icons.refresh_rounded), onPressed: _load)],
      ),
      body: _loading
          ? const LoadingWidget()
          : _error != null
              ? ErrorWidget2(message: _error!, onRetry: _load)
              : RefreshIndicator(onRefresh: _load, color: AppTheme.primary, child: _buildBody()),
    );
  }

  Widget _buildBody() {
    final showEarnings = (_data!['show_earnings'] as bool?) ?? false;
    final totals = (_data!['totals'] as Map?) ?? {};
    final daily = ((_data!['daily'] as List?) ?? []).map((e) => Map<String, dynamic>.from(e as Map)).toList();
    final countries = ((_data!['country_breakdown'] as List?) ?? []).map((e) => Map<String, dynamic>.from(e as Map)).toList();
    final links = ((_data!['link_stats'] as List?) ?? []).map((e) => Map<String, dynamic>.from(e as Map)).toList();

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
                child: GestureDetector(
                  onTap: () { setState(() => _period = p.$1); _load(); },
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 9),
                    decoration: BoxDecoration(
                      gradient: _period == p.$1 ? AppTheme.primaryGradient : null,
                      color: _period == p.$1 ? null : Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: _period == p.$1 ? Colors.transparent : AppTheme.border),
                      boxShadow: _period == p.$1 ? AppTheme.cardShadow : null,
                    ),
                    child: Text(
                      p.$2,
                      style: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: _period == p.$1 ? Colors.white : AppTheme.textSecondary,
                      ),
                    ),
                  ),
                ),
              ),
          ]),
        ),
        const SizedBox(height: 20),

        // Totals
        Row(children: [
          Expanded(child: _totalCard('Total Clicks', _fmt(totals['clicks']), AppTheme.info, Icons.mouse_outlined, AppTheme.blueGradient)),
          if (showEarnings) ...[
            const SizedBox(width: 12),
            Expanded(child: _totalCard('Total Earnings', '\$${_toDouble(totals['earnings']).toStringAsFixed(4)}', AppTheme.primary, Icons.attach_money_rounded, AppTheme.primaryGradient)),
          ],
        ]),
        const SizedBox(height: 16),

        // Chart
        SectionCard(
          title: 'Daily Performance',
          child: SizedBox(height: 170, child: _buildChart(daily)),
        ),
        const SizedBox(height: 16),

        // Daily table
        SectionCard(
          title: 'Daily Breakdown',
          padding: EdgeInsets.zero,
          child: Column(children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: const BoxDecoration(
                color: Color(0xFFF8FAFC),
                borderRadius: BorderRadius.only(topLeft: Radius.circular(16), topRight: Radius.circular(16)),
              ),
              child: Row(children: [
                const Expanded(child: Text('DATE', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.textSecondary, letterSpacing: 0.5))),
                const Text('CLICKS', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.textSecondary, letterSpacing: 0.5)),
                if (showEarnings) const SizedBox(width: 70, child: Text('EARN', textAlign: TextAlign.right, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.textSecondary, letterSpacing: 0.5))),
              ]),
            ),
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
        if (countries.isNotEmpty)
          SectionCard(
            title: 'Traffic by Country',
            padding: EdgeInsets.zero,
            child: Column(children: countries.take(15).map((c) => _countryRow(c, showEarnings)).toList()),
          ),
        const SizedBox(height: 80),
      ],
    );
  }

  Widget _totalCard(String label, String value, Color color, IconData icon, Gradient gradient) {
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
          decoration: BoxDecoration(gradient: gradient, borderRadius: BorderRadius.circular(12)),
          child: Icon(icon, size: 22, color: Colors.white),
        ),
        const SizedBox(width: 14),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(label, style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
          const SizedBox(height: 3),
          Text(value, style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: color, letterSpacing: -0.5)),
        ])),
      ]),
    );
  }

  Widget _buildChart(List<Map<String, dynamic>> daily) {
    if (daily.isEmpty) return const Center(child: Text('No data', style: TextStyle(color: AppTheme.textSecondary)));
    final spots = daily.asMap().entries.map((e) => FlSpot(e.key.toDouble(), _toDouble(e.value['clicks']))).toList();
    return LineChart(LineChartData(
      lineBarsData: [
        LineChartBarData(
          spots: spots,
          isCurved: true,
          color: AppTheme.info,
          barWidth: 3,
          dotData: const FlDotData(show: false),
          belowBarData: BarAreaData(
            show: true,
            gradient: LinearGradient(
              colors: [AppTheme.info.withAlpha(50), AppTheme.info.withAlpha(0)],
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
            ),
          ),
        ),
      ],
      titlesData: FlTitlesData(
        bottomTitles: AxisTitles(sideTitles: SideTitles(
          showTitles: true,
          interval: (daily.length / 5).ceilToDouble(),
          getTitlesWidget: (v, _) {
            final i = v.toInt();
            if (i < 0 || i >= daily.length) return const SizedBox.shrink();
            return Padding(
              padding: const EdgeInsets.only(top: 4),
              child: Text(daily[i]['date']?.toString() ?? '', style: const TextStyle(fontSize: 9, color: AppTheme.textSecondary)),
            );
          },
        )),
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

  Widget _dailyRow(Map<String, dynamic> d, bool showEarnings) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 11),
      decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: AppTheme.border, width: 0.5))),
      child: Row(children: [
        Expanded(child: Text(d['date']?.toString() ?? '', style: const TextStyle(fontSize: 13, color: AppTheme.textSecondary))),
        Text(_fmt(d['clicks']), style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppTheme.textPrimary)),
        if (showEarnings) SizedBox(width: 80, child: Text('\$${_toDouble(d['earnings']).toStringAsFixed(4)}', textAlign: TextAlign.right, style: const TextStyle(color: AppTheme.primary, fontSize: 13, fontWeight: FontWeight.w600))),
      ]),
    );
  }

  Widget _linkRow(Map<String, dynamic> l, bool showEarnings) {
    final isActive = l['active'] == true;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: AppTheme.border, width: 0.5))),
      child: Row(children: [
        Container(
          width: 8,
          height: 8,
          decoration: BoxDecoration(
            color: isActive ? AppTheme.primary : AppTheme.textSecondary,
            shape: BoxShape.circle,
          ),
        ),
        const SizedBox(width: 10),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(l['name']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
          Text(l['code']?.toString() ?? '', style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary, fontFamily: 'monospace')),
        ])),
        Text(_fmt(l['clicks']), style: const TextStyle(fontWeight: FontWeight.w700)),
        if (showEarnings && l['earnings'] != null) ...[
          const SizedBox(width: 10),
          Text('\$${_toDouble(l['earnings']).toStringAsFixed(4)}', style: const TextStyle(color: AppTheme.primary, fontSize: 13)),
        ],
      ]),
    );
  }

  Widget _countryRow(Map<String, dynamic> c, bool showEarnings) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 11),
      decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: AppTheme.border, width: 0.5))),
      child: Row(children: [
        Expanded(child: Text(c['country']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w600))),
        Text(_fmt(c['clicks']), style: const TextStyle(fontWeight: FontWeight.w700)),
        if (showEarnings && c['earnings'] != null) ...[
          const SizedBox(width: 12),
          Text('\$${_toDouble(c['earnings']).toStringAsFixed(4)}', style: const TextStyle(color: AppTheme.primary, fontSize: 13)),
        ],
      ]),
    );
  }

  String _fmt(dynamic v) {
    if (v == null) return '0';
    final n = num.tryParse(v.toString()) ?? 0;
    return n.toInt().toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]},');
  }

  double _toDouble(dynamic v) => v == null ? 0.0 : (num.tryParse(v.toString()) ?? 0).toDouble();
}
