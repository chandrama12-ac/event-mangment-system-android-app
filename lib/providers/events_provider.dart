import 'package:flutter/material.dart';
import '../models/event.dart';
import '../services/api_service.dart';

class EventsProvider with ChangeNotifier {
  List<Event> _events = [];
  List<Event> _trendingEvents = [];
  bool _isLoading = false;

  List<Event> get events => _events;
  List<Event> get trendingEvents => _trendingEvents;
  bool get isLoading => _isLoading;

  Future<void> fetchEvents({int? userId, String? search}) async {
    _isLoading = true;
    notifyListeners();

    String url = 'events.php?action=list';
    if (userId != null) {
      url += "&user_id=$userId";
    }
    if (search != null && search.isNotEmpty) {
      url += "&search=${Uri.encodeComponent(search)}";
    }

    final response = await ApiService.get(url);
    if (response['status'] == 'success') {
      _events = (response['data'] as List)
          .map((e) => Event.fromJson(e))
          .toList();
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchTrendingEvents({int? userId}) async {
    _isLoading = true;
    notifyListeners();

    final response = await ApiService.get(
      'events.php?action=trending${userId != null ? "&user_id=$userId" : ""}',
    );
    if (response['status'] == 'success') {
      _trendingEvents = (response['data'] as List)
          .map((e) => Event.fromJson(e))
          .toList();
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<Map<String, dynamic>> registerForEvent({
    required int userId,
    required int eventId,
    String? fullName,
    String? email,
    String? phone,
    String? college,
    String? department,
    String? year,
    String? gender,
    String? address,
    String? idCardPath,
  }) async {
    final Map<String, dynamic> data = {
      'user_id': userId,
      'event_id': eventId,
      'full_name': fullName,
      'email': email,
      'phone': phone,
      'college': college,
      'department': department,
      'year': year,
      'gender': gender,
      'address': address,
    };

    return await ApiService.post('register_event.php', data);
  }

  Future<Map<String, dynamic>> fetchMyTickets(int userId) async {
    return await ApiService.get(
      'registrations.php?action=user_registrations&user_id=$userId',
    );
  }

  Future<Map<String, dynamic>> verifyTicket(String ticketId) async {
    return await ApiService.post('verify_ticket.php', {'ticket_id': ticketId});
  }

  Future<Map<String, dynamic>> markAttendance(
    String ticketId,
    String action,
  ) async {
    return await ApiService.post('mark_attendance.php', {
      'ticket_id': ticketId,
      'action': action,
    });
  }

  Future<Map<String, dynamic>> createEvent({
    required String title,
    required String description,
    required String location,
    required String date,
    required int createdBy,
    String? category,
    String? imagePath,
  }) async {
    final Map<String, String> fields = {
      'title': title,
      'description': description,
      'location': location,
      'event_date': date,
      'created_by': createdBy.toString(),
      'category': category ?? 'General',
    };

    return await ApiService.postWithFile(
      'events.php?action=create',
      fields,
      fileField: 'image',
      filePath: imagePath,
    );
  }
}
