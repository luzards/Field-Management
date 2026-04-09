import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import 'api_service.dart';

class AuthService extends ChangeNotifier {
  UserModel? _user;
  String? _token;
  bool _isLoading = true;

  UserModel? get user => _user;
  String? get token => _token;
  bool get isAuthenticated => _token != null && _user != null;
  bool get isLoading => _isLoading;

  AuthService() {
    _loadStoredAuth();
  }

  Future<void> _loadStoredAuth() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');

    if (_token != null) {
      try {
        final response = await ApiService.get('/profile');
        if (response['success'] == true) {
          _user = UserModel.fromJson(response['data']);
        } else {
          await _clearAuth();
        }
      } catch (e) {
        await _clearAuth();
      }
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<String?> login(String email, String password) async {
    try {
      final response = await ApiService.post('/login', body: {
        'email': email,
        'password': password,
      });

      if (response['success'] == true) {
        _token = response['data']['token'];
        _user = UserModel.fromJson(response['data']['user']);

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);

        notifyListeners();
        return null; // success
      } else {
        return response['message'] ?? 'Login failed';
      }
    } catch (e) {
      return e.toString().replaceFirst('Exception: ', '');
    }
  }

  Future<void> logout() async {
    try {
      await ApiService.post('/logout');
    } catch (_) {}
    await _clearAuth();
    notifyListeners();
  }

  Future<void> refreshProfile() async {
    try {
      final response = await ApiService.get('/profile');
      if (response['success'] == true) {
        _user = UserModel.fromJson(response['data']);
        notifyListeners();
      }
    } catch (_) {}
  }

  Future<void> _clearAuth() async {
    _token = null;
    _user = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }
}
