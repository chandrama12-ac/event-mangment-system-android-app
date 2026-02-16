import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  User? _user;
  bool _isLoading = false;

  User? get user => _user;
  bool get isLoading => _isLoading;
  bool get isAdmin => _user?.role == 'admin';

  AuthProvider() {
    _loadUser();
  }

  Future<void> _loadUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userStr = prefs.getString('user');
    if (userStr != null) {
      _user = User.fromJson(jsonDecode(userStr));
      notifyListeners();
    }
  }

  Future<void> _saveUser(User user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('user', jsonEncode(user.toJson()));
  }

  Future<Map<String, dynamic>> login({
    String? email,
    String? phone,
    required String password,
  }) async {
    _isLoading = true;
    notifyListeners();

    final response = await ApiService.post('login.php', {
      'email': email,
      'phone': phone,
      'password': password,
    });

    if (response['status'] == 'success' || response['status'] == 200) {
      final userData = response['data'];
      _user = User.fromJson(userData);
      await _saveUser(_user!);
    }

    _isLoading = false;
    notifyListeners();
    return response;
  }

  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String phone,
    required String password,
  }) async {
    _isLoading = true;
    notifyListeners();

    final response = await ApiService.post('register.php', {
      'name': name,
      'email': email,
      'phone': phone,
      'password': password,
    });

    _isLoading = false;
    notifyListeners();
    return response;
  }

  Future<Map<String, dynamic>> requestOtp(String phone) async {
    _isLoading = true;
    notifyListeners();

    final response = await ApiService.post('request_otp.php', {'phone': phone});

    _isLoading = false;
    notifyListeners();
    return response;
  }

  Future<Map<String, dynamic>> verifyOtp(String phone, String otp) async {
    _isLoading = true;
    notifyListeners();

    final response = await ApiService.post('verify_otp.php', {
      'phone': phone,
      'otp': otp,
    });

    _isLoading = false;
    notifyListeners();
    return response;
  }

  Future<Map<String, dynamic>> updateProfile({
    String? name,
    String? phone,
    String? dob,
    String? education,
    String? skills,
    String? gender,
    String? bio,
    String? profilePicPath,
    bool? profileVisible,
    bool? emailVisible,
    bool? phoneVisible,
  }) async {
    _isLoading = true;
    notifyListeners();

    if (_user == null) {
      _isLoading = false;
      notifyListeners();
      return {'status': 'error', 'message': 'User not logged in'};
    }

    // Prepare fields for update
    final Map<String, String> fields = {'user_id': _user!.id.toString()};
    if (name != null) fields['name'] = name;
    if (phone != null) fields['phone'] = phone;
    if (dob != null) {
      fields['dob'] = dob;
    }
    if (education != null) {
      fields['education'] = education;
    }
    if (skills != null) {
      fields['skills'] = skills;
    }
    if (gender != null) {
      fields['gender'] = gender;
    }
    if (bio != null) {
      fields['bio'] = bio;
    }
    if (profileVisible != null) {
      fields['profile_visible'] = profileVisible.toString();
    }
    if (emailVisible != null) {
      fields['email_visible'] = emailVisible.toString();
    }
    if (phoneVisible != null) {
      fields['phone_visible'] = phoneVisible.toString();
    }

    // Use ApiService to post with potential file
    final response = await ApiService.postWithFile(
      'update_profile.php',
      fields,
      fileField: 'profile_pic',
      filePath: profilePicPath,
    );

    if (response['status'] == 'success') {
      final userData = response['data'];
      _user = User.fromJson(userData);
      await _saveUser(_user!);
    }

    _isLoading = false;
    notifyListeners();
    return response;
  }

  Future<void> logout() async {
    _user = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user');
    notifyListeners();
  }
}
