import 'package:flutter/material.dart';
import '../config/theme.dart';

class LoadingWidget extends StatelessWidget {
  const LoadingWidget({super.key});

  @override
  Widget build(BuildContext context) => const Center(
        child: CircularProgressIndicator(color: AppTheme.primary),
      );
}

class ErrorWidget2 extends StatelessWidget {
  final String message;
  final VoidCallback? onRetry;
  const ErrorWidget2({super.key, required this.message, this.onRetry});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          const Icon(Icons.error_outline, size: 48, color: AppTheme.textSecondary),
          const SizedBox(height: 12),
          Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppTheme.textSecondary)),
          if (onRetry != null) ...[
            const SizedBox(height: 16),
            ElevatedButton(onPressed: onRetry, child: const Text('Retry')),
          ]
        ]),
      ),
    );
  }
}
