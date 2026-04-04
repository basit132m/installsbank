import 'package:flutter/foundation.dart';
import '../services/auth_service.dart';
import '../services/storage_service.dart';

class AuthProvider extends ChangeNotifier {
  Map<String, dynamic>? _user;
  bool _loading = true;

  Map<String, dynamic>? get user => _user;
  bool get loading => _loading;
  bool get isLoggedIn => _user != null;

  Future<void> checkAuth() async {
    _loading = true;
    notifyListeners();
    if (await AuthService.isLoggedIn()) {
      // Token exists; mark as logged in — profile loaded per-screen
      _user = {'id': 0};
    }
    _loading = false;
    notifyListeners();
  }

  Future<void> login(String email, String password) async {
    _user = await AuthService.login(email, password);
    notifyListeners();
  }

  void setUser(Map<String, dynamic> user) {
    _user = user;
    notifyListeners();
  }

  Future<void> logout() async {
    await AuthService.logout();
    _user = null;
    notifyListeners();
  }
}
