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

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _replyCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    final res = await ApiService.get('/support/${widget.ticketId}');
    if (!mounted) return;
    if (res.ok) setState(() { _data = res.data; _loading = false; });
    else setState(() => _loading = false);
  }

  Future<void> _reply() async {
    if (_replyCtrl.text.trim().isEmpty) return;
    setState(() => _sending = true);
    final res = await ApiService.post('/support/${widget.ticketId}/reply', {'message': _replyCtrl.text.trim()});
    if (!mounted) return;
    setState(() => _sending = false);
    if (res.ok) { _replyCtrl.clear(); _load(); }
    else ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res.message), backgroundColor: AppTheme.danger));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(widget.subject, overflow: TextOverflow.ellipsis)),
      body: _loading ? const LoadingWidget() : _buildBody(),
    );
  }

  Widget _buildBody() {
    final messages = (_data!['messages'] as List).cast<Map>();
    final ticket = _data!['ticket'] as Map;
    final isClosed = ticket['status'] == 'closed';

    return Column(children: [
      Expanded(
        child: ListView.builder(
          padding: const EdgeInsets.all(16),
          itemCount: messages.length,
          itemBuilder: (_, i) => _messageBubble(messages[i]),
        ),
      ),
      if (!isClosed)
        Container(
          padding: const EdgeInsets.all(12),
          decoration: const BoxDecoration(color: Colors.white, border: Border(top: BorderSide(color: AppTheme.border))),
          child: Row(children: [
            Expanded(child: TextField(controller: _replyCtrl, decoration: const InputDecoration(hintText: 'Write a reply...', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10)), maxLines: null)),
            const SizedBox(width: 8),
            IconButton(
              onPressed: _sending ? null : _reply,
              icon: _sending ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary)) : const Icon(Icons.send, color: AppTheme.primary),
            ),
          ]),
        )
      else
        const Padding(padding: EdgeInsets.all(12), child: Text('This ticket is closed.', style: TextStyle(color: AppTheme.textSecondary, fontSize: 13), textAlign: TextAlign.center)),
    ]);
  }

  Widget _messageBubble(Map m) {
    final isStaff = m['is_staff'] == true || m['is_staff'] == 1;
    return Align(
      alignment: isStaff ? Alignment.centerLeft : Alignment.centerRight,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.75),
        decoration: BoxDecoration(
          color: isStaff ? const Color(0xFFF3F4F6) : AppTheme.primaryLight,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(12),
            topRight: const Radius.circular(12),
            bottomLeft: Radius.circular(isStaff ? 0 : 12),
            bottomRight: Radius.circular(isStaff ? 12 : 0),
          ),
        ),
        child: Column(crossAxisAlignment: isStaff ? CrossAxisAlignment.start : CrossAxisAlignment.end, children: [
          if (isStaff) const Text('Support Team', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppTheme.textSecondary)),
          Text(m['message'] ?? '', style: const TextStyle(fontSize: 14)),
          const SizedBox(height: 4),
          Text(m['created_at'] ?? '', style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary)),
        ]),
      ),
    );
  }
}
