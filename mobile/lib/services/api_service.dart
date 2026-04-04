import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/constants.dart';
import 'storage_service.dart';

class ApiService {
  static Future<Map<String, String>> _headers({bool auth = true}) async {
    final headers = <String, String>{
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    if (auth) {
      final token = await StorageService.getToken();
      if (token != null) headers['Authorization'] = 'Bearer $token';
    }
    return headers;
  }

  static Future<ApiResponse> get(String path) async {
    final res = await http.get(
      Uri.parse('${AppConstants.apiBase}$path'),
      headers: await _headers(),
    );
    return ApiResponse(res.statusCode, _decode(res.body));
  }

  static Future<ApiResponse> post(String path, Map<String, dynamic> body,
      {bool auth = true}) async {
    final res = await http.post(
      Uri.parse('${AppConstants.apiBase}$path'),
      headers: await _headers(auth: auth),
      body: jsonEncode(body),
    );
    return ApiResponse(res.statusCode, _decode(res.body));
  }

  static Future<ApiResponse> put(String path, Map<String, dynamic> body) async {
    final res = await http.put(
      Uri.parse('${AppConstants.apiBase}$path'),
      headers: await _headers(),
      body: jsonEncode(body),
    );
    return ApiResponse(res.statusCode, _decode(res.body));
  }

  static Future<ApiResponse> postMultipart(
      String path, Map<String, String> fields, String? filePath, String fieldName) async {
    final token = await StorageService.getToken();
    final req = http.MultipartRequest('POST', Uri.parse('${AppConstants.apiBase}$path'));
    req.headers['Authorization'] = 'Bearer $token';
    req.headers['Accept'] = 'application/json';
    req.fields.addAll(fields);
    if (filePath != null) {
      req.files.add(await http.MultipartFile.fromPath(fieldName, filePath));
    }
    final streamed = await req.send();
    final body = await streamed.stream.bytesToString();
    return ApiResponse(streamed.statusCode, _decode(body));
  }

  static dynamic _decode(String body) {
    try {
      return jsonDecode(body);
    } catch (_) {
      return {'message': body};
    }
  }
}

class ApiResponse {
  final int statusCode;
  final dynamic data;
  bool get ok => statusCode >= 200 && statusCode < 300;
  String get message => data?['message'] ?? 'Something went wrong.';
  ApiResponse(this.statusCode, this.data);
}
