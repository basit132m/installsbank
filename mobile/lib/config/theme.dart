import 'package:flutter/material.dart';

class AppTheme {
  static const Color primary = Color(0xFF00C06A);
  static const Color primaryDark = Color(0xFF009952);
  static const Color primaryLight = Color(0xFFE6FAF2);
  static const Color background = Color(0xFFF2F5F9);
  static const Color card = Colors.white;
  static const Color textPrimary = Color(0xFF0F1923);
  static const Color textSecondary = Color(0xFF6B7E95);
  static const Color border = Color(0xFFE4EAF2);
  static const Color danger = Color(0xFFEF4444);
  static const Color warning = Color(0xFFF59E0B);
  static const Color info = Color(0xFF3B82F6);
  static const Color purple = Color(0xFF7C3AED);

  static const LinearGradient primaryGradient = LinearGradient(
    colors: [Color(0xFF00C06A), Color(0xFF007D45)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient blueGradient = LinearGradient(
    colors: [Color(0xFF3B82F6), Color(0xFF1D4ED8)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient purpleGradient = LinearGradient(
    colors: [Color(0xFF7C3AED), Color(0xFF5B21B6)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient orangeGradient = LinearGradient(
    colors: [Color(0xFFF59E0B), Color(0xFFD97706)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static List<BoxShadow> get cardShadow => [
    BoxShadow(color: const Color(0xFF0F1923).withAlpha(10), blurRadius: 12, offset: const Offset(0, 4)),
    BoxShadow(color: const Color(0xFF0F1923).withAlpha(5), blurRadius: 4, offset: const Offset(0, 1)),
  ];

  static List<BoxShadow> get largeShadow => [
    BoxShadow(color: const Color(0xFF0F1923).withAlpha(15), blurRadius: 24, offset: const Offset(0, 8)),
  ];

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
            letterSpacing: -0.3,
          ),
        ),
        cardTheme: CardThemeData(
          color: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
            side: const BorderSide(color: border),
          ),
          margin: EdgeInsets.zero,
        ),
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: primary,
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            padding: const EdgeInsets.symmetric(vertical: 15, horizontal: 24),
            textStyle: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15),
            elevation: 0,
          ),
        ),
        inputDecorationTheme: InputDecorationTheme(
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: const BorderSide(color: border),
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: const BorderSide(color: border),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: const BorderSide(color: primary, width: 2),
          ),
          filled: true,
          fillColor: Colors.white,
          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          labelStyle: const TextStyle(color: textSecondary),
        ),
        fontFamily: 'Roboto',
        chipTheme: ChipThemeData(
          backgroundColor: Colors.white,
          selectedColor: primaryLight,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          side: const BorderSide(color: border),
          labelStyle: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
        ),
      );
}
