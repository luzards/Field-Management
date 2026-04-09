import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class ApiService {
  static String get baseUrl => ApiConfig.baseUrl;

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  static Future<Map<String, String>> _headers({bool isMultipart = false}) async {
    final token = await getToken();
    final headers = <String, String>{
      'Accept': 'application/json',
    };
    if (!isMultipart) {
      headers['Content-Type'] = 'application/json';
    }
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }
    return headers;
  }

  static Future<Map<String, dynamic>> get(String endpoint, {Map<String, String>? queryParams}) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}$endpoint').replace(queryParameters: queryParams);
    try {
      final response = await http.get(uri, headers: await _headers())
          .timeout(const Duration(seconds: 10));
      return _handleResponse(response);
    } catch (e) {
      if (e is Exception) rethrow;
      throw Exception('Network timeout or connection failed: $e');
    }
  }

  static Future<Map<String, dynamic>> post(String endpoint, {Map<String, dynamic>? body}) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}$endpoint');
    try {
      final response = await http.post(
        uri,
        headers: await _headers(),
        body: body != null ? jsonEncode(body) : null,
      ).timeout(const Duration(seconds: 10));
      return _handleResponse(response);
    } catch (e) {
      if (e is Exception) rethrow;
      throw Exception('Network timeout or connection failed: $e');
    }
  }

  static Future<Map<String, dynamic>> put(String endpoint, {Map<String, dynamic>? body}) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}$endpoint');
    try {
      final response = await http.put(
        uri,
        headers: await _headers(),
        body: body != null ? jsonEncode(body) : null,
      ).timeout(const Duration(seconds: 10));
      return _handleResponse(response);
    } catch (e) {
      if (e is Exception) rethrow;
      throw Exception('Network timeout or connection failed: $e');
    }
  }

  static Future<Map<String, dynamic>> postMultipart(
    String endpoint, {
    Map<String, String>? fields,
    String? filePath,
    String? fileField,
  }) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}$endpoint');
    final request = http.MultipartRequest('POST', uri);
    request.headers.addAll(await _headers(isMultipart: true));

    if (fields != null) {
      request.fields.addAll(fields);
    }
    if (filePath != null && fileField != null) {
      request.files.add(await http.MultipartFile.fromPath(fileField, filePath));
    }

    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    return _handleResponse(response);
  }

  /// Create a multipart request with auth headers pre-configured.
  static Future<http.MultipartRequest> createMultipartRequest(String method, Uri uri) async {
    final request = http.MultipartRequest(method, uri);
    request.headers.addAll(await _headers(isMultipart: true));
    return request;
  }

  /// Create a multipart file from a file path.
  static Future<http.MultipartFile> createMultipartFile(String field, String filePath) async {
    return await http.MultipartFile.fromPath(field, filePath);
  }

  /// Send a multipart request and return parsed response.
  static Future<Map<String, dynamic>> sendMultipart(http.MultipartRequest request) async {
    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    return _handleResponse(response);
  }

  static Map<String, dynamic> _handleResponse(http.Response response) {
    final data = jsonDecode(response.body);
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return data;
    } else if (response.statusCode == 401) {
      throw Exception('Unauthorized. Please login again.');
    } else if (response.statusCode == 422) {
      final errors = data['errors'] ?? {};
      final firstError = errors.values.isNotEmpty ? errors.values.first[0] : data['message'] ?? 'Validation error';
      throw Exception(firstError);
    } else {
      throw Exception(data['message'] ?? 'Something went wrong');
    }
  }
}
