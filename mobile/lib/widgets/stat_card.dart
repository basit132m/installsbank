import 'package:flutter/material.dart';
import '../config/theme.dart';

class StatCard extends StatelessWidget {
  final String label;
  final String value;
  final Color? valueColor;
  final Color? borderColor;
  final IconData? icon;
  final Gradient? gradient;

  const StatCard({
    super.key,
    required this.label,
    required this.value,
    this.valueColor,
    this.borderColor,
    this.icon,
    this.gradient,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: borderColor ?? AppTheme.border),
        boxShadow: AppTheme.cardShadow,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          if (icon != null)
            Container(
              width: 34,
              height: 34,
              decoration: BoxDecoration(
                gradient: gradient,
                color: gradient == null ? AppTheme.primaryLight : null,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, size: 18, color: gradient != null ? Colors.white : (valueColor ?? AppTheme.primary)),
            ),
          const Spacer(),
          Text(value, style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: valueColor ?? AppTheme.textPrimary, letterSpacing: -0.5)),
          const SizedBox(height: 2),
          Text(label, style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary, fontWeight: FontWeight.w500)),
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
  final IconData? icon;
  final Gradient? gradient;

  const BalanceCard({
    super.key,
    required this.label,
    required this.value,
    required this.color,
    this.subtitle,
    this.icon,
    this.gradient,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppTheme.border),
        boxShadow: AppTheme.cardShadow,
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          if (icon != null) ...[
            Container(
              width: 28,
              height: 28,
              decoration: BoxDecoration(
                gradient: gradient,
                color: gradient == null ? color.withAlpha(20) : null,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Icon(icon, size: 14, color: gradient != null ? Colors.white : color),
            ),
            const SizedBox(width: 6),
          ],
          Expanded(child: Text(label, style: TextStyle(fontSize: 10, color: color, fontWeight: FontWeight.w700, letterSpacing: 0.5))),
        ]),
        const SizedBox(height: 8),
        Text(value, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: AppTheme.textPrimary, letterSpacing: -0.5)),
        if (subtitle != null) ...[const SizedBox(height: 2), Text(subtitle!, style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary))],
      ]),
    );
  }
}
