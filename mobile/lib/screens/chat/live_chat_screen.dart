import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../../config/theme.dart';
import '../../services/api_service.dart';
import '../../services/notification_service.dart';

class LiveChatScreen extends StatefulWidget {
  const LiveChatScreen({super.key});

  @override
  State<LiveChatScreen> createState() => _LiveChatScreenState();
}

class _LiveChatScreenState extends State<LiveChatScreen> with WidgetsBindingObserver {
  List<Map<String, dynamic>> _messages = [];
  int? _ticketId;
  String? _chatStatus;
  bool _loading = true;
  bool _sending = false;
  bool _polling = false;
  bool _chatClosed = false;
  int _lastMessageId = 0;

  final _inputCtrl = TextEditingController();
  final _scrollCtrl = ScrollController();
  final _inputFocus = FocusNode();
  Timer? _pollTimer;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    NotificationService.isInChat = true;
    _load(initial: true);
  }

  @override
  void dispose() {
    NotificationService.isInChat = false;
    _pollTimer?.cancel();
    _inputCtrl.dispose();
    _scrollCtrl.dispose();
    _inputFocus.dispose();
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      _startPolling();
    } else if (state == AppLifecycleState.paused) {
      _pollTimer?.cancel();
    }
  }

  void _startPolling() {
    _pollTimer?.cancel();
    _pollTimer = Timer.periodic(const Duration(seconds: 15), (_) => _poll());
  }

  Future<void> _load({bool initial = false}) async {
    if (initial) setState(() => _loading = true);
    final res = await ApiService.get('/chat/messages');
    if (!mounted) return;

    if (res.ok) {
      final msgs = ((res.data['messages'] as List?) ?? [])
          .map((e) => Map<String, dynamic>.from(e as Map))
          .toList();
      final status = res.data['status']?.toString();
      final ticketId = res.data['ticket_id'] as int?;

      setState(() {
        _messages = msgs;
        _ticketId = ticketId;
        _chatStatus = status;
        _chatClosed = status == 'closed';
        _loading = false;
        if (msgs.isNotEmpty) _lastMessageId = msgs.last['id'] as int? ?? 0;
      });

      _scrollToBottom();
    } else {
      setState(() => _loading = false);
    }

    _startPolling();
  }

  Future<void> _poll() async {
    if (_polling) return;
    _polling = true;
    final res = await ApiService.get('/chat/messages');
    _polling = false;
    if (!mounted) return;
    if (!res.ok) return;

    final msgs = ((res.data['messages'] as List?) ?? [])
        .map((e) => Map<String, dynamic>.from(e as Map))
        .toList();
    final status = res.data['status']?.toString();
    final ticketId = res.data['ticket_id'] as int?;

    // Detect new staff message when chat is open
    if (msgs.isNotEmpty) {
      final latestId = msgs.last['id'] as int? ?? 0;
      if (latestId > _lastMessageId) {
        final newMsg = msgs.last;
        final isStaff = newMsg['is_staff'] == true || newMsg['is_staff'] == 1;
        // Vibrate on new staff message
        if (isStaff) {
          HapticFeedback.mediumImpact();
        }
        _lastMessageId = latestId;
      }
    }

    // Detect ticket switch (new chat after close)
    if (ticketId != null && ticketId != _ticketId) {
      setState(() {
        _messages = msgs;
        _ticketId = ticketId;
        _chatStatus = status;
        _chatClosed = status == 'closed';
        if (msgs.isNotEmpty) _lastMessageId = msgs.last['id'] as int? ?? 0;
      });
      _scrollToBottom();
      return;
    }

    if (msgs.length != _messages.length || status != _chatStatus) {
      setState(() {
        _messages = msgs;
        _chatStatus = status;
        _chatClosed = status == 'closed';
        if (msgs.isNotEmpty) _lastMessageId = msgs.last['id'] as int? ?? 0;
      });
      _scrollToBottom();
    }
  }

  Future<void> _send() async {
    final text = _inputCtrl.text.trim();
    if (text.isEmpty || _sending) return;

    setState(() => _sending = true);
    _inputCtrl.clear();
    _inputFocus.unfocus();

    final res = await ApiService.post('/chat/send', {'message': text});
    if (!mounted) return;
    setState(() => _sending = false);

    if (res.ok) {
      // Add sent message immediately
      final newMsg = {
        'id': res.data['id'],
        'message': res.data['message'],
        'is_staff': false,
        'sender': res.data['sender'],
        'time': 'just now',
        'created_at': res.data['created_at'],
      };
      final ticketId = res.data['ticket_id'] as int?;

      // Fetch latest to get auto-reply too
      await _poll();
      if (ticketId != null && ticketId != _ticketId) {
        setState(() {
          _ticketId = ticketId;
          _chatClosed = false;
          _chatStatus = 'open';
        });
      }
    } else {
      // Restore text on failure
      _inputCtrl.text = text;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(res.message),
        backgroundColor: AppTheme.danger,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ));
    }
  }

  void _scrollToBottom({bool animated = true}) {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollCtrl.hasClients) {
        if (animated) {
          _scrollCtrl.animateTo(
            _scrollCtrl.position.maxScrollExtent,
            duration: const Duration(milliseconds: 300),
            curve: Curves.easeOut,
          );
        } else {
          _scrollCtrl.jumpTo(_scrollCtrl.position.maxScrollExtent);
        }
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF0F4F8),
      appBar: _buildAppBar(),
      body: _loading ? _buildLoading() : _buildBody(),
    );
  }

  PreferredSizeWidget _buildAppBar() {
    return AppBar(
      backgroundColor: Colors.white,
      surfaceTintColor: Colors.white,
      elevation: 0,
      leading: IconButton(
        icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 18),
        onPressed: () => Navigator.pop(context),
      ),
      title: Row(children: [
        Container(
          width: 36,
          height: 36,
          decoration: BoxDecoration(
            gradient: AppTheme.primaryGradient,
            borderRadius: BorderRadius.circular(10),
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(10),
            child: Image.network(
              'https://installsbank.com/images/installs-bank.webp',
              fit: BoxFit.cover,
              errorBuilder: (_, __, ___) => const Icon(Icons.support_agent_rounded, color: Colors.white, size: 20),
            ),
          ),
        ),
        const SizedBox(width: 10),
        Column(crossAxisAlignment: CrossAxisAlignment.start, mainAxisSize: MainAxisSize.min, children: [
          const Text('Live Support', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: AppTheme.textPrimary, letterSpacing: -0.3)),
          Row(children: [
            Container(
              width: 7,
              height: 7,
              decoration: BoxDecoration(
                color: _chatClosed ? AppTheme.textSecondary : AppTheme.primary,
                shape: BoxShape.circle,
              ),
            ),
            const SizedBox(width: 4),
            Text(
              _chatClosed ? 'Session closed' : 'Online',
              style: TextStyle(
                fontSize: 11,
                color: _chatClosed ? AppTheme.textSecondary : AppTheme.primary,
                fontWeight: FontWeight.w600,
              ),
            ),
          ]),
        ]),
      ]),
      bottom: PreferredSize(
        preferredSize: const Size.fromHeight(1),
        child: Container(height: 1, color: AppTheme.border),
      ),
    );
  }

  Widget _buildLoading() {
    return const Center(
      child: CircularProgressIndicator(color: AppTheme.primary, strokeWidth: 2.5),
    );
  }

  Widget _buildBody() {
    return Column(children: [
      // Messages area
      Expanded(
        child: _messages.isEmpty
            ? _buildEmptyState()
            : ListView.builder(
                controller: _scrollCtrl,
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 12),
                itemCount: _messages.length,
                itemBuilder: (_, i) {
                  final prev = i > 0 ? _messages[i - 1] : null;
                  return _buildMessageItem(_messages[i], prev);
                },
              ),
      ),
      // Input area
      _buildInputArea(),
    ]);
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(mainAxisSize: MainAxisSize.min, children: [
        Container(
          width: 90,
          height: 90,
          decoration: BoxDecoration(
            gradient: AppTheme.primaryGradient,
            borderRadius: BorderRadius.circular(28),
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(28),
            child: Image.network(
              'https://installsbank.com/images/installs-bank.webp',
              fit: BoxFit.cover,
              errorBuilder: (_, __, ___) => const Icon(Icons.support_agent_rounded, color: Colors.white, size: 44),
            ),
          ),
        ),
        const SizedBox(height: 18),
        const Text(
          'How can we help you?',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppTheme.textPrimary, letterSpacing: -0.3),
        ),
        const SizedBox(height: 8),
        const Text(
          'Send a message and our team\nwill get back to you shortly.',
          textAlign: TextAlign.center,
          style: TextStyle(fontSize: 14, color: AppTheme.textSecondary, height: 1.5),
        ),
        const SizedBox(height: 80),
      ]),
    );
  }

  Widget _buildMessageItem(Map<String, dynamic> m, Map<String, dynamic>? prev) {
    final isStaff = m['is_staff'] == true || m['is_staff'] == 1;
    final isSystem = isStaff && (m['sender'] == null || m['sender'] == 'Support');
    final message = m['message']?.toString() ?? '';

    // Check if system closure message
    final isClosure = isSystem && message.contains('chat session has been closed');

    if (isClosure) {
      return _buildSystemPill(message);
    }

    final prevIsStaff = prev != null && (prev['is_staff'] == true || prev['is_staff'] == 1);
    final showAvatar = isStaff;

    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        mainAxisAlignment: isStaff ? MainAxisAlignment.start : MainAxisAlignment.end,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          if (isStaff) ...[
            // Staff avatar
            Container(
              width: 32,
              height: 32,
              margin: const EdgeInsets.only(right: 8),
              decoration: BoxDecoration(
                gradient: AppTheme.primaryGradient,
                borderRadius: BorderRadius.circular(10),
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(10),
                child: Image.network(
                  'https://installsbank.com/images/installs-bank.webp',
                  fit: BoxFit.cover,
                  errorBuilder: (_, __, ___) => const Icon(Icons.support_agent_rounded, color: Colors.white, size: 16),
                ),
              ),
            ),
          ],
          // Bubble
          Flexible(
            child: ConstrainedBox(
              constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.72),
              child: Column(
                crossAxisAlignment: isStaff ? CrossAxisAlignment.start : CrossAxisAlignment.end,
                children: [
                  if (isStaff && showAvatar)
                    Padding(
                      padding: const EdgeInsets.only(left: 2, bottom: 3),
                      child: Text(
                        'Support Team',
                        style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.primary.withAlpha(200)),
                      ),
                    ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    decoration: BoxDecoration(
                      gradient: isStaff ? null : AppTheme.primaryGradient,
                      color: isStaff ? Colors.white : null,
                      borderRadius: BorderRadius.only(
                        topLeft: const Radius.circular(18),
                        topRight: const Radius.circular(18),
                        bottomLeft: Radius.circular(isStaff ? 4 : 18),
                        bottomRight: Radius.circular(isStaff ? 18 : 4),
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: isStaff
                              ? const Color(0xFF0F1923).withAlpha(8)
                              : const Color(0xFF00C06A).withAlpha(40),
                          blurRadius: 8,
                          offset: const Offset(0, 2),
                        ),
                      ],
                      border: isStaff ? Border.all(color: AppTheme.border.withAlpha(180)) : null,
                    ),
                    child: Text(
                      message,
                      style: TextStyle(
                        fontSize: 14.5,
                        color: isStaff ? AppTheme.textPrimary : Colors.white,
                        height: 1.45,
                      ),
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.only(top: 3, left: 2, right: 2),
                    child: Text(
                      m['time']?.toString() ?? m['created_at']?.toString() ?? '',
                      style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary),
                    ),
                  ),
                ],
              ),
            ),
          ),
          if (!isStaff) const SizedBox(width: 4),
        ],
      ),
    );
  }

  Widget _buildSystemPill(String message) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Center(
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: Colors.black.withAlpha(12),
            borderRadius: BorderRadius.circular(20),
          ),
          child: Text(
            message,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary, fontStyle: FontStyle.italic),
          ),
        ),
      ),
    );
  }

  Widget _buildInputArea() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        border: const Border(top: BorderSide(color: AppTheme.border)),
        boxShadow: [
          BoxShadow(color: Colors.black.withAlpha(8), blurRadius: 12, offset: const Offset(0, -3)),
        ],
      ),
      child: SafeArea(
        top: false,
        child: _chatClosed ? _buildClosedBanner() : _buildInput(),
      ),
    );
  }

  Widget _buildClosedBanner() {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(mainAxisSize: MainAxisSize.min, children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
          decoration: BoxDecoration(
            color: AppTheme.textSecondary.withAlpha(12),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppTheme.border),
          ),
          child: const Row(mainAxisAlignment: MainAxisAlignment.center, children: [
            Icon(Icons.lock_rounded, color: AppTheme.textSecondary, size: 15),
            SizedBox(width: 7),
            Text('This chat session has been closed by support', style: TextStyle(color: AppTheme.textSecondary, fontSize: 13)),
          ]),
        ),
        const SizedBox(height: 10),
        SizedBox(
          width: double.infinity,
          child: ElevatedButton.icon(
            onPressed: () {
              setState(() {
                _chatClosed = false;
                _messages = [];
                _ticketId = null;
                _chatStatus = null;
                _lastMessageId = 0;
              });
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.primary,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              padding: const EdgeInsets.symmetric(vertical: 13),
              elevation: 0,
            ),
            icon: const Icon(Icons.add_comment_outlined, size: 18),
            label: const Text('Start New Chat', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
          ),
        ),
      ]),
    );
  }

  Widget _buildInput() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(12, 10, 8, 10),
      child: Row(crossAxisAlignment: CrossAxisAlignment.end, children: [
        Expanded(
          child: Container(
            constraints: const BoxConstraints(minHeight: 44, maxHeight: 120),
            decoration: BoxDecoration(
              color: const Color(0xFFF4F7FA),
              borderRadius: BorderRadius.circular(22),
              border: Border.all(color: AppTheme.border),
            ),
            child: TextField(
              controller: _inputCtrl,
              focusNode: _inputFocus,
              maxLines: null,
              keyboardType: TextInputType.multiline,
              textCapitalization: TextCapitalization.sentences,
              style: const TextStyle(fontSize: 14.5, color: AppTheme.textPrimary),
              decoration: const InputDecoration(
                hintText: 'Type a message...',
                hintStyle: TextStyle(color: AppTheme.textSecondary, fontSize: 14.5),
                contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 11),
                border: InputBorder.none,
                enabledBorder: InputBorder.none,
                focusedBorder: InputBorder.none,
              ),
              onSubmitted: (_) => _send(),
            ),
          ),
        ),
        const SizedBox(width: 8),
        GestureDetector(
          onTap: _sending ? null : _send,
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              gradient: _sending ? null : AppTheme.primaryGradient,
              color: _sending ? AppTheme.border : null,
              borderRadius: BorderRadius.circular(22),
              boxShadow: _sending
                  ? []
                  : [BoxShadow(color: AppTheme.primary.withAlpha(80), blurRadius: 10, offset: const Offset(0, 4))],
            ),
            child: _sending
                ? const Padding(
                    padding: EdgeInsets.all(12),
                    child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary),
                  )
                : const Icon(Icons.send_rounded, color: Colors.white, size: 20),
          ),
        ),
      ]),
    );
  }
}
