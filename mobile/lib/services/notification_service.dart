import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'api_service.dart';

class NotificationService {
  static final _localNotifications = FlutterLocalNotificationsPlugin();
  static const _channel = AndroidNotificationChannel(
    'installs_bank',
    'Installs Bank',
    description: 'Installs Bank notifications',
    importance: Importance.high,
  );

  static Future<void> init() async {
    await _localNotifications.initialize(
      const InitializationSettings(
        android: AndroidInitializationSettings('@mipmap/ic_launcher'),
      ),
    );

    await _localNotifications
        .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
        ?.createNotificationChannel(_channel);

    FirebaseMessaging.onMessage.listen((RemoteMessage msg) {
      final notification = msg.notification;
      if (notification != null) {
        _localNotifications.show(
          notification.hashCode,
          notification.title,
          notification.body,
          NotificationDetails(
            android: AndroidNotificationDetails(
              _channel.id,
              _channel.name,
              channelDescription: _channel.description,
              importance: Importance.high,
            ),
          ),
        );
      }
    });

    await _registerToken();
  }

  static Future<void> _registerToken() async {
    try {
      final token = await FirebaseMessaging.instance.getToken();
      if (token != null) {
        await ApiService.put('/device-token', {'fcm_token': token});
      }
      FirebaseMessaging.instance.onTokenRefresh.listen((newToken) {
        ApiService.put('/device-token', {'fcm_token': newToken});
      });
    } catch (_) {}
  }
}
