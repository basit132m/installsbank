import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../services/api_service.dart';
import '../../widgets/loading_widget.dart';

class SupportDetailScreen extends StatefulWidget {
  final int ticketId;
  final String subject;
  const SupportDetailScreen({super.key, required this.ticketId, required this.subject});

  @override
  State<SupportDetailScreen> createState() => _SupportDetailScreenState();
}

class _SupportDetailScreenState extends State<SupportDetailScreen> {
  Map<String, dynamic>? _data;
  bool _loading = true;
  final _replyCtrl = TextEditingController();
  bool _sending = false;
  final _scrollCtrl = ScrollController();

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _replyCtrl.dispose();
    _scrollCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    final res = await ApiService.get('/support/${widget.ticketId}');
    if (!mounted) return;
    if (res.ok) {
      setState(() { _data = res.data as Map<String, dynamic>; _loading = false; });
      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (_scrollCtrl.hasClients) {
          _scrollCtrl.animateTo(_scrollCtrl.position.maxScrollExtent, duration: const Duration(milliseconds: 300), curve: Curves.easeOut);
        }
      });
    } else {
      setState(() => _loading = false);
    }
  }

  Future<void> _reply() async {
    if (_replyCtrl.text.trim().isEmpty) return;
    setState(() => _sending = true);
    final res = await ApiService.post('/support/${widget.ticketId}/reply', {'message': _replyCtrl.text.trim()});
    if (!mounted) return;
    setState(() => _sending = false);
    if (res.ok) {
      _replyCtrl.clear();
      _load();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(res.message),
        backgroundColor: AppTheme.danger,
        behavior: SnackBarBehavior.floating,
      ));
    }
  }

  @override
  Widget build(BuildContext context) {
    final ticket = _data != null ? Map<String, dynamic>.from(_data!['ticket'] as Map) : null;
    final status = ticket?['status']?.toString() ?? '';
    Color statusColor;
    switch (status) {
      case 'open': statusColor = AppTheme.primary; break;
      case 'pending': statusColor = AppTheme.warning; break;
      default: statusColor = AppTheme.textSecondary;
    }

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: Text(widget.subject, overflow: TextOverflow.ellipsis),
        actions: [
          if (ticket != null)
            Padding(
              padding: const EdgeInsets.only(right: 12),
              child: Center(child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(color: statusColor.withAlpha(20), borderRadius: BorderRadius.circular(12)),
                child: Text(status.isNotEmpty ? status[0].toUpperCase() + status.substring(1) : '', style: TextStyle(color: statusColor, fontSize: 12, fontWeight: FontWeight.w700)),
              )),
            ),
        ],
      ),
      body: _loading ? const LoadingWidget() : _buildBody(ticket),
    );
  }

  Widget _buildBody(Map<String, dynamic>? ticket) {
    if (_data == null || ticket == null) {
      return const Center(child: Text('Failed to load ticket.', style: TextStyle(color: AppTheme.textSecondary)));
    }
    final messages = ((_data!['messages'] as List?) ?? []).map((e) => Map<String, dynamic>.from(e as Map)).toList();
    final isClosed = ticket['status'] == 'closed';

    return Column(children: [
      Expanded(
        child: messages.isEmpty
            ? const Center(child: Text('No messages yet.', style: TextStyle(color: AppTheme.textSecondary)))
            : ListView.builder(
                controller: _scrollCtrl,
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                itemCount: messages.length,
                itemBuilder: (_, i) => _messageBubble(messages[i]),
              ),
      ),
      if (isClosed)
        Container(
          padding: const EdgeInsets.all(14),
          color: Colors.white,
          child: Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: AppTheme.textSecondary.withAlpha(15), borderRadius: BorderRadius.circular(10)),
            child: const Row(mainAxisAlignment: MainAxisAlignment.center, children: [
              Icon(Icons.lock_rounded, color: AppTheme.textSecondary, size: 16),
              SizedBox(width: 6),
              Text('This ticket is closed', style: TextStyle(color: AppTheme.textSecondary, fontSize: 13, fontWeight: FontWeight.w600)),
            ]),
          ),
        )
      else
        Container(
          padding: const EdgeInsets.fromLTRB(12, 8, 8, 8),
          decoration: const BoxDecoration(
            color: Colors.white,
            border: Border(top: BorderSide(color: AppTheme.border)),
          ),
          child: SafeArea(
            top: false,
            child: Row(children: [
              Expanded(
                child: TextField(
                  controller: _replyCtrl,
                  decoration: InputDecoration(
                    hintText: 'Write a reply...',
                    hintStyle: const TextStyle(color: AppTheme.textSecondary),
                    contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(24), borderSide: const BorderSide(color: AppTheme.border)),
                    enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(24), borderSide: const BorderSide(color: AppTheme.border)),
                    focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(24), borderSide: const BorderSide(color: AppTheme.primary, width: 1.5)),
                  ),
                  maxLines: null,
                ),
              ),
              const SizedBox(width: 8),
              GestureDetector(
                onTap: _sending ? null : _reply,
                child: Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    gradient: _sending ? null : AppTheme.primaryGradient,
                    color: _sending ? AppTheme.border : null,
                    borderRadius: BorderRadius.circular(22),
                  ),
                  child: _sending
                      ? const Padding(padding: EdgeInsets.all(12), child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary))
                      : const Icon(Icons.send_rounded, color: Colors.white, size: 20),
                ),
              ),
            ]),
          ),
        ),
    ]);
  }

  Widget _messageBubble(Map<String, dynamic> m) {
    final isStaff = m['is_staff'] == true || m['is_staff'] == 1;
    return Align(
      alignment: isStaff ? Alignment.centerLeft : Alignment.centerRight,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.78),
        child: Column(
          crossAxisAlignment: isStaff ? CrossAxisAlignment.start : CrossAxisAlignment.end,
          children: [
            if (isStaff)
              const Padding(
                padding: EdgeInsets.only(left: 4, bottom: 4),
                child: Text('Support Team', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.primary)),
              ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
              decoration: BoxDecoration(
                gradient: isStaff ? null : AppTheme.primaryGradient,
                color: isStaff ? Colors.white : null,
                borderRadius: BorderRadius.only(
                  topLeft: const Radius.circular(16),
                  topRight: const Radius.circular(16),
                  bottomLeft: Radius.circular(isStaff ? 4 : 16),
                  bottomRight: Radius.circular(isStaff ? 16 : 4),
                ),
                border: isStaff ? Border.all(color: AppTheme.border) : null,
                boxShadow: AppTheme.cardShadow,
              ),
              child: Text(
                m['message']?.toString() ?? '',
                style: TextStyle(
                  fontSize: 14,
                  color: isStaff ? AppTheme.textPrimary : Colors.white,
                  height: 1.4,
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.only(top: 4, left: 4, right: 4),
              child: Text(
                m['created_at']?.toString() ?? '',
                style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
