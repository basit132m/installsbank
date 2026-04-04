import 'package:flutter/material.dart';
import '../config/theme.dart';

class SectionCard extends StatelessWidget {
  final String? title;
  final Widget child;
  final EdgeInsets? padding;

  const SectionCard({super.key, this.title, required this.child, this.padding});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: padding ?? const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppTheme.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (title != null) ...[
            Text(title!, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
            const SizedBox(height: 14),
          ],
          child,
        ],
      ),
    );
  }
}

class AppBadge extends StatelessWidget {
  final String label;
  final Color color;

  const AppBadge({super.key, required this.label, required this.color});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
      decoration: BoxDecoration(
        color: color.withAlpha(25),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withAlpha(60)),
      ),
      child: Text(label, style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: color)),
    );
  }
}
