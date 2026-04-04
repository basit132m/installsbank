import 'api_service.dart';
import 'storage_service.dart';

class AuthService {
  static Future<Map<String, dynamic>?> login(String email, String password) async {
    final res = await ApiService.post(
      '/auth/login',
      {'email': email, 'password': password},
      auth: false,
    );
    if (res.ok) {
      await StorageService.saveToken(res.data['token']);
      return Map<String, dynamic>.from(res.data['user']);
    }
    throw Exception(res.message);
  }

  static Future<void> logout() async {
    await ApiService.post('/auth/logout', {});
    await StorageService.clear();
  }

  static Future<bool> isLoggedIn() async {
    final token = await StorageService.getToken();
    return token != null;
  }
}
