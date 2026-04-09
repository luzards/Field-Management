import 'package:flutter_local_notifications/flutter_local_notifications.dart';

class NotificationService {
  static final FlutterLocalNotificationsPlugin _notifications =
      FlutterLocalNotificationsPlugin();

  static Future<void> initialize() async {
    const androidSettings = AndroidInitializationSettings('@mipmap/ic_launcher');
    const initSettings = InitializationSettings(android: androidSettings);
    
    await _notifications.initialize(
      initSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) {},
    );
  }

  static Future<void> showNotification({
    required int id,
    required String title,
    required String body,
  }) async {
    const androidDetails = AndroidNotificationDetails(
      'am_tracker_channel',
      'F2M Field Management Notifications',
      channelDescription: 'Schedule reminders and check-in notifications',
      importance: Importance.high,
      priority: Priority.high,
      ticker: 'ticker',
    );
    
    const details = NotificationDetails(android: androidDetails);

    await _notifications.show(
      id,
      title,
      body,
      details,
    );
  }

  static Future<void> showScheduleReminder(String storeName, String time) async {
    await showNotification(
      id: DateTime.now().millisecondsSinceEpoch ~/ 1000,
      title: '📅 Upcoming Visit',
      body: 'You have a visit to $storeName at $time',
    );
  }

  static Future<void> showCheckInSuccess(String storeName, bool verified) async {
    await showNotification(
      id: DateTime.now().millisecondsSinceEpoch ~/ 1000,
      title: verified ? '✅ Check-in Verified' : '⚠️ Check-in Recorded',
      body: verified
          ? 'Successfully checked in at $storeName'
          : 'Checked in at $storeName but location is outside the geofence',
    );
  }
}