import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../services/api_service.dart';
import '../../widgets/loading_widget.dart';
import '../chat/live_chat_screen.dart';
import 'support_detail_screen.dart';
import 'support_create_screen.dart';

class SupportListScreen extends StatefulWidget {
  const SupportListScreen({super.key});

  @override
  State<SupportListScreen> createState() => _SupportListScreenState();
}

class _SupportListScreenState extends State<SupportListScreen> with AutomaticKeepAliveClientMixin {
  List<Map<String, dynamic>> _tickets = [];
  bool _loading = true;
  String? _error;

  @override
  bool get wantKeepAlive => true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() { _loading = _tickets.isEmpty; _error = null; });
    final res = await ApiService.get('/support');
    if (!mounted) return;
    if (res.ok) {
      setState(() {
        _tickets = ((res.data['tickets'] as List?) ?? []).map((e) => Map<String, dynamic>.from(e as Map)).toList();
        _loading = false;
      });
    } else {
      setState(() { _error = res.message; _loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Support'),
        centerTitle: false,
        actions: [IconButton(icon: const Icon(Icons.refresh_rounded), onPressed: _load)],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async {
          await Navigator.push(context, MaterialPageRoute(builder: (_) => const SupportCreateScreen()));
          _load();
        },
        backgroundColor: AppTheme.primary,
        icon: const Icon(Icons.add_rounded, color: Colors.white),
        label: const Text('New Ticket', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
      ),
      body: _loading
          ? const LoadingWidget()
          : _error != null
              ? ErrorWidget2(message: _error!, onRetry: _load)
              : RefreshIndicator(
                  onRefresh: _load,
                  color: AppTheme.primary,
                  child: ListView(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
                    children: [
                      _liveChatCard(),
                      const SizedBox(height: 20),
                      if (_tickets.isNotEmpty) ...[
                        const Padding(
                          padding: EdgeInsets.only(left: 2, bottom: 10),
                          child: Text(
                            'SUPPORT TICKETS',
                            style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: AppTheme.textSecondary, letterSpacing: 1.2),
                          ),
                        ),
                        ..._tickets.asMap().entries.map((entry) {
                          final i = entry.key;
                          return Padding(
                            padding: EdgeInsets.only(bottom: i < _tickets.length - 1 ? 10 : 0),
                            child: _ticketCard(_tickets[i]),
                          );
                        }),
                      ] else
                        _emptyTicketsState(),
                    ],
                  ),
                ),
    );
  }

  Widget _liveChatCard() {
    return GestureDetector(
      onTap: () {
        Navigator.push(context, MaterialPageRoute(builder: (_) => const LiveChatScreen()));
      },
      child: Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          gradient: AppTheme.primaryGradient,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(color: AppTheme.primary.withAlpha(70), blurRadius: 20, offset: const Offset(0, 6)),
          ],
        ),
        child: Row(children: [
          Container(
            width: 54,
            height: 54,
            decoration: BoxDecoration(
              color: Colors.white.withAlpha(30),
              borderRadius: BorderRadius.circular(16),
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(16),
              child: Image.network(
                'https://installsbank.com/images/installs-bank.webp',
                fit: BoxFit.cover,
                errorBuilder: (_, __, ___) => const Icon(Icons.chat_bubble_rounded, color: Colors.white, size: 28),
              ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text(
              'Live Chat',
              style: TextStyle(fontSize: 17, fontWeight: FontWeight.w800, color: Colors.white, letterSpacing: -0.3),
            ),
            const SizedBox(height: 3),
            Text(
              'Chat with our support team in real-time',
              style: TextStyle(fontSize: 13, color: Colors.white.withAlpha(200)),
            ),
            const SizedBox(height: 8),
            Row(children: [
              Container(
                width: 7,
                height: 7,
                decoration: const BoxDecoration(color: Color(0xFF7FFFB8), shape: BoxShape.circle),
              ),
              const SizedBox(width: 5),
              Text('Usually replies quickly', style: TextStyle(fontSize: 12, color: Colors.white.withAlpha(220), fontWeight: FontWeight.w600)),
            ]),
          ])),
          const SizedBox(width: 8),
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: Colors.white.withAlpha(30),
              borderRadius: BorderRadius.circular(10),
            ),
            child: const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white, size: 16),
          ),
        ]),
      ),
    );
  }

  Widget _emptyTicketsState() {
    return Padding(
      padding: const EdgeInsets.only(top: 20),
      child: Column(mainAxisSize: MainAxisSize.min, children: [
        Container(
          width: 64,
          height: 64,
          decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(20)),
          child: const Icon(Icons.inbox_outlined, color: AppTheme.primary, size: 30),
        ),
        const SizedBox(height: 12),
        const Text('No Support Tickets', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppTheme.textPrimary)),
        const SizedBox(height: 5),
        const Text('Tap "+ New Ticket" to create one', style: TextStyle(color: AppTheme.textSecondary, fontSize: 13)),
      ]),
    );
  }

  Widget _ticketCard(Map<String, dynamic> t) {
    final status = t['status']?.toString() ?? '';
    Color statusColor;
    IconData statusIcon;
    switch (status) {
      case 'open':
        statusColor = AppTheme.primary;
        statusIcon = Icons.radio_button_checked_rounded;
        break;
      case 'pending':
        statusColor = AppTheme.warning;
        statusIcon = Icons.hourglass_bottom_rounded;
        break;
      default:
        statusColor = AppTheme.textSecondary;
        statusIcon = Icons.check_circle_outlined;
    }

    return GestureDetector(
      onTap: () async {
        await Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => SupportDetailScreen(ticketId: t['id'] as int, subject: t['subject']?.toString() ?? '')),
        );
        _load();
      },
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppTheme.border),
          boxShadow: AppTheme.cardShadow,
        ),
        child: Row(children: [
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              color: statusColor.withAlpha(15),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(statusIcon, color: statusColor, size: 22),
          ),
          const SizedBox(width: 14),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(t['subject']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppTheme.textPrimary)),
            const SizedBox(height: 3),
            Text(
              t['last_reply_at']?.toString() ?? t['created_at']?.toString() ?? '',
              style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary),
            ),
          ])),
          const SizedBox(width: 10),
          Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(color: statusColor.withAlpha(15), borderRadius: BorderRadius.circular(20)),
              child: Text(
                status.isNotEmpty ? status[0].toUpperCase() + status.substring(1) : '',
                style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: statusColor),
              ),
            ),
            const SizedBox(height: 4),
            const Icon(Icons.chevron_right_rounded, color: AppTheme.textSecondary, size: 18),
          ]),
        ]),
      ),
    );
  }
}
