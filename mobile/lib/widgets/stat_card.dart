import 'package:flutter/material.dart';
import '../config/theme.dart';

class StatCard extends StatelessWidget {
  final String label;
  final String value;
  final Color? valueColor;
  final Color? borderColor;

  const StatCard({
    super.key,
    required this.label,
    required this.value,
    this.valueColor,
    this.borderColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: borderColor ?? AppTheme.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary, fontWeight: FontWeight.w500)),
          const SizedBox(height: 6),
          Text(value, style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: valueColor ?? AppTheme.textPrimary)),
        ],
      ),
    );
  }
}

class BalanceCard extends StatelessWidget {
  final String label;
  final String value;
  final Color color;
  final String? subtitle;

  const BalanceCard({super.key, required this.label, required this.value, required this.color, this.subtitle});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppTheme.border),
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(label, style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary, fontWeight: FontWeight.w600, letterSpacing: 0.5)),
        const SizedBox(height: 8),
        Text(value, style: TextStyle(fontSize: 26, fontWeight: FontWeight.w800, color: color)),
        if (subtitle != null) ...[const SizedBox(height: 4), Text(subtitle!, style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary))],
      ]),
    );
  }
}
