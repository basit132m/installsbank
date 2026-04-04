import 'package:flutter/material.dart';

class AppTheme {
  static const Color primary = Color(0xFF01BF63);
  static const Color primaryDark = Color(0xFF00A354);
  static const Color primaryLight = Color(0xFFE6FAF2);
  static const Color background = Color(0xFFF9FAFB);
  static const Color card = Colors.white;
  static const Color textPrimary = Color(0xFF111827);
  static const Color textSecondary = Color(0xFF6B7280);
  static const Color border = Color(0xFFE5E7EB);
  static const Color danger = Color(0xFFEF4444);
  static const Color warning = Color(0xFFF59E0B);
  static const Color info = Color(0xFF3B82F6);

  static ThemeData get theme => ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: primary,
          primary: primary,
          surface: background,
        ),
        scaffoldBackgroundColor: background,
        appBarTheme: const AppBarTheme(
          backgroundColor: Colors.white,
          foregroundColor: textPrimary,
          elevation: 0,
          surfaceTintColor: Colors.white,
          titleTextStyle: TextStyle(
            color: textPrimary,
            fontSize: 18,
            fontWeight: FontWeight.w700,
          ),
        ),
        cardTheme: CardThemeData(
          color: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
            side: const BorderSide(color: border),
          ),
          margin: EdgeInsets.zero,
        ),
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: primary,
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
            padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 24),
            textStyle: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15),
          ),
        ),
        inputDecorationTheme: InputDecorationTheme(
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: const BorderSide(color: border),
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: const BorderSide(color: border),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: const BorderSide(color: primary, width: 1.5),
          ),
          filled: true,
          fillColor: Colors.white,
          contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        ),
        fontFamily: 'Roboto',
      );
}
