import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../services/api_service.dart';
import '../../widgets/loading_widget.dart';
import 'support_detail_screen.dart';
import 'support_create_screen.dart';

class SupportListScreen extends StatefulWidget {
  const SupportListScreen({super.key});

  @override
  State<SupportListScreen> createState() => _SupportListScreenState();
}

class _SupportListScreenState extends State<SupportListScreen> with AutomaticKeepAliveClientMixin {
  List<Map> _tickets = [];
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
      setState(() { _tickets = (res.data['tickets'] as List).cast<Map>(); _loading = false; });
    } else {
      setState(() { _error = res.message; _loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Scaffold(
      appBar: AppBar(title: const Text('Support'), actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _load)]),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async {
          await Navigator.push(context, MaterialPageRoute(builder: (_) => const SupportCreateScreen()));
          _load();
        },
        backgroundColor: AppTheme.primary,
        icon: const Icon(Icons.add, color: Colors.white),
        label: const Text('New Ticket', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
      ),
      body: _loading
          ? const LoadingWidget()
          : _error != null
              ? ErrorWidget2(message: _error!, onRetry: _load)
              : RefreshIndicator(
                  onRefresh: _load,
                  color: AppTheme.primary,
                  child: _tickets.isEmpty
                      ? const Center(child: Text('No tickets yet. Create one if you need help.', style: TextStyle(color: AppTheme.textSecondary), textAlign: TextAlign.center))
                      : ListView.separated(
                          padding: const EdgeInsets.all(16),
                          itemCount: _tickets.length,
                          separatorBuilder: (_, __) => const SizedBox(height: 10),
                          itemBuilder: (_, i) => _ticketCard(_tickets[i]),
                        ),
                ),
    );
  }

  Widget _ticketCard(Map t) {
    final status = t['status'] as String;
    final color = status == 'open' ? AppTheme.primary : status == 'pending' ? AppTheme.warning : AppTheme.textSecondary;
    return GestureDetector(
      onTap: () async {
        await Navigator.push(context, MaterialPageRoute(builder: (_) => SupportDetailScreen(ticketId: t['id'], subject: t['subject'])));
        _load();
      },
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppTheme.border)),
        child: Row(children: [
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(t['subject'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
            const SizedBox(height: 4),
            Text(t['last_reply_at'] ?? t['created_at'] ?? '', style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
          ])),
          const SizedBox(width: 10),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(color: color.withAlpha(25), borderRadius: BorderRadius.circular(12)),
            child: Text(status[0].toUpperCase() + status.substring(1), style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: color)),
          ),
          const Icon(Icons.chevron_right, color: AppTheme.textSecondary),
        ]),
      ),
    );
  }
}
