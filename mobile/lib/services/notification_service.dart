import 'dart:async';
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
  static const _chatChannel = AndroidNotificationChannel(
    'installs_bank_chat',
    'Live Chat',
    description: 'Live chat messages from support',
    importance: Importance.max,
  );

  /// Set to true while the LiveChatScreen is open — suppresses in-app chat pings
  static bool isInChat = false;

  static int _lastChatMessageId = 0;
  static Timer? _chatPollTimer;

  static Future<void> init() async {
    await _localNotifications.initialize(
      const InitializationSettings(
        android: AndroidInitializationSettings('@mipmap/ic_launcher'),
        iOS: DarwinInitializationSettings(
          requestAlertPermission: true,
          requestBadgePermission: true,
          requestSoundPermission: true,
        ),
      ),
    );

    final androidPlugin = _localNotifications
        .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
    await androidPlugin?.createNotificationChannel(_channel);
    await androidPlugin?.createNotificationChannel(_chatChannel);

    // Request iOS permissions
    await FirebaseMessaging.instance.requestPermission(
      alert: true,
      badge: true,
      sound: true,
    );

    // Handle FCM foreground messages
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
              priority: Priority.high,
              icon: '@mipmap/ic_launcher',
            ),
            iOS: const DarwinNotificationDetails(
              presentAlert: true,
              presentBadge: true,
              presentSound: true,
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

  /// Start polling for new chat messages (call after user logs in)
  static void startChatPolling() {
    _chatPollTimer?.cancel();
    // Initial baseline fetch (silent — don't notify for existing messages)
    _initChatBaseline();
    _chatPollTimer = Timer.periodic(const Duration(seconds: 10), (_) => _pollChat());
  }

  /// Stop chat polling (call on logout)
  static void stopChatPolling() {
    _chatPollTimer?.cancel();
    _chatPollTimer = null;
    _lastChatMessageId = 0;
  }

  static Future<void> _initChatBaseline() async {
    try {
      final res = await ApiService.get('/chat/messages');
      if (res.ok) {
        final msgs = (res.data['messages'] as List?) ?? [];
        if (msgs.isNotEmpty) {
          _lastChatMessageId = (msgs.last as Map)['id'] as int? ?? 0;
        }
      }
    } catch (_) {}
  }

  static Future<void> _pollChat() async {
    if (isInChat) return; // Live chat screen handles its own updates

    try {
      final res = await ApiService.get('/chat/messages');
      if (!res.ok) return;

      final msgs = (res.data['messages'] as List?) ?? [];
      if (msgs.isEmpty) return;

      final latest = msgs.last as Map;
      final latestId = latest['id'] as int? ?? 0;
      final isStaff = latest['is_staff'] == true || latest['is_staff'] == 1;

      if (latestId > _lastChatMessageId && isStaff) {
        _lastChatMessageId = latestId;
        _showChatNotification(latest['message']?.toString() ?? 'New message from support');
      } else if (latestId > _lastChatMessageId) {
        _lastChatMessageId = latestId;
      }
    } catch (_) {}
  }

  static Future<void> _showChatNotification(String message) async {
    await _localNotifications.show(
      9999, // fixed ID so we replace instead of stacking
      'Live Support',
      message,
      NotificationDetails(
        android: AndroidNotificationDetails(
          _chatChannel.id,
          _chatChannel.name,
          channelDescription: _chatChannel.description,
          importance: Importance.max,
          priority: Priority.max,
          icon: '@mipmap/ic_launcher',
          playSound: true,
          enableVibration: true,
        ),
        iOS: const DarwinNotificationDetails(
          presentAlert: true,
          presentBadge: true,
          presentSound: true,
        ),
      ),
    );
  }
}
