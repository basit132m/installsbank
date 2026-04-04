import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../services/api_service.dart';
import '../widgets/loading_widget.dart';
import '../widgets/stat_card.dart';

class WithdrawalsScreen extends StatefulWidget {
  const WithdrawalsScreen({super.key});

  @override
  State<WithdrawalsScreen> createState() => _WithdrawalsScreenState();
}

class _WithdrawalsScreenState extends State<WithdrawalsScreen> with AutomaticKeepAliveClientMixin {
  Map<String, dynamic>? _data;
  bool _loading = true;
  String? _error;
  final _networkCtrl = ValueNotifier<String>('trc20');
  final _addressCtrl = TextEditingController();

  @override
  bool get wantKeepAlive => true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _addressCtrl.dispose();
    _networkCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() { _loading = _data == null; _error = null; });
    final res = await ApiService.get('/withdrawals');
    if (!mounted) return;
    if (res.ok) {
      setState(() { _data = res.data; _loading = false; });
      _addressCtrl.text = _data!['payment_address'] ?? '';
      _networkCtrl.value = _data!['payment_network'] ?? 'trc20';
    } else {
      setState(() { _error = res.message; _loading = false; });
    }
  }

  Future<void> _saveAddress() async {
    if (_addressCtrl.text.trim().isEmpty) return;
    final res = await ApiService.post('/withdrawals/save-address', {
      'payment_network': _networkCtrl.value,
      'payment_address': _addressCtrl.text.trim(),
    });
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(res.message),
      backgroundColor: res.ok ? AppTheme.primary : AppTheme.danger,
    ));
    if (res.ok) _load();
  }

  Future<void> _requestWithdrawal() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Confirm Withdrawal'),
        content: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text('Amount: \$${(_data!['balance'] as num).toStringAsFixed(2)}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
          const SizedBox(height: 8),
          Text('Network: ${(_data!['payment_network'] ?? '').toString().toUpperCase()}'),
          const SizedBox(height: 4),
          Text('Address: ${_data!['payment_address'] ?? ''}', style: const TextStyle(fontFamily: 'monospace', fontSize: 12)),
          const SizedBox(height: 12),
          const Text('Payments are processed before end of Sunday.', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
        ]),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
          ElevatedButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Confirm')),
        ],
      ),
    );

    if (confirm != true || !mounted) return;

    final res = await ApiService.post('/withdrawals', {});
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(res.message),
      backgroundColor: res.ok ? AppTheme.primary : AppTheme.danger,
    ));
    if (res.ok) _load();
  }

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Scaffold(
      appBar: AppBar(title: const Text('Payments'), actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _load)]),
      body: _loading
          ? const LoadingWidget()
          : _error != null
              ? ErrorWidget2(message: _error!, onRetry: _load)
              : RefreshIndicator(onRefresh: _load, color: AppTheme.primary, child: _buildBody()),
    );
  }

  Widget _buildBody() {
    final paymentEnabled = _data!['payment_enabled'] as bool;
    final isWeekend = _data!['is_weekend'] as bool;
    final hasPending = _data!['has_pending'] as bool;
    final balance = (_data!['balance'] as num).toDouble();
    final threshold = (_data!['threshold'] as num).toDouble();
    final withdrawals = (_data!['withdrawals'] as List).cast<Map>();

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        if (!paymentEnabled) _disabledBanner(),

        // Balance cards
        Row(children: [
          Expanded(child: BalanceCard(label: 'AVAILABLE', value: '\$${balance.toStringAsFixed(2)}', color: AppTheme.primary, subtitle: 'Threshold: \$${threshold.toStringAsFixed(2)}')),
          const SizedBox(width: 10),
          Expanded(child: BalanceCard(label: 'PENDING', value: '\$${(_data!['pending_balance'] as num).toStringAsFixed(2)}', color: AppTheme.warning)),
          const SizedBox(width: 10),
          Expanded(child: BalanceCard(label: 'PAID OUT', value: '\$${(_data!['total_withdrawn'] as num).toStringAsFixed(2)}', color: AppTheme.textSecondary)),
        ]),
        const SizedBox(height: 16),

        // Payment address
        _addressCard(),
        const SizedBox(height: 16),

        // Withdrawal request
        if (paymentEnabled) _withdrawCard(isWeekend, hasPending, balance, threshold),
        const SizedBox(height: 16),

        // History
        _historyCard(withdrawals),
        const SizedBox(height: 80),
      ],
    );
  }

  Widget _disabledBanner() => Container(
    margin: const EdgeInsets.only(bottom: 16),
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(color: const Color(0xFFFFF7ED), borderRadius: BorderRadius.circular(10), border: Border.all(color: const Color(0xFFFED7AA))),
    child: const Row(children: [
      Icon(Icons.info_outline, color: AppTheme.warning, size: 20),
      SizedBox(width: 10),
      Expanded(child: Text('Withdrawals are not yet enabled for your account.', style: TextStyle(fontSize: 13, color: Color(0xFF78350F)))),
    ]),
  );

  Widget _addressCard() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppTheme.border)),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        const Text('Payment Address', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
        const SizedBox(height: 14),
        ValueListenableBuilder(
          valueListenable: _networkCtrl,
          builder: (_, v, __) => Row(children: [
            Expanded(child: _networkBtn('TRC20 (Tron)', 'trc20', v)),
            const SizedBox(width: 10),
            Expanded(child: _networkBtn('BEP20 (BSC)', 'bep20', v)),
          ]),
        ),
        const SizedBox(height: 12),
        TextField(
          controller: _addressCtrl,
          decoration: const InputDecoration(labelText: 'USDT Wallet Address', hintText: 'Paste your wallet address'),
          style: const TextStyle(fontFamily: 'monospace', fontSize: 13),
        ),
        const SizedBox(height: 12),
        SizedBox(width: double.infinity, child: ElevatedButton(onPressed: _saveAddress, child: const Text('Save Address'))),
      ]),
    );
  }

  Widget _networkBtn(String label, String value, String current) {
    final selected = current == value;
    return GestureDetector(
      onTap: () => _networkCtrl.value = value,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: selected ? AppTheme.primaryLight : const Color(0xFFF9FAFB),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: selected ? AppTheme.primary : AppTheme.border, width: selected ? 1.5 : 1),
        ),
        child: Text(label, textAlign: TextAlign.center, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: selected ? AppTheme.primary : AppTheme.textSecondary)),
      ),
    );
  }

  Widget _withdrawCard(bool isWeekend, bool hasPending, double balance, double threshold) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: isWeekend && !hasPending && balance >= threshold ? AppTheme.primary : AppTheme.border),
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Icon(isWeekend ? Icons.check_circle : Icons.access_time, color: isWeekend ? AppTheme.primary : AppTheme.warning, size: 18),
          const SizedBox(width: 8),
          Text(isWeekend ? 'Withdrawals are open (Weekend)' : 'Withdrawals open Sat–Sun (USA Eastern)', style: TextStyle(fontWeight: FontWeight.w600, color: isWeekend ? AppTheme.primary : AppTheme.warning, fontSize: 13)),
        ]),
        const SizedBox(height: 12),
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(8)),
          child: const Text('Your full balance is submitted as one withdrawal. Payments are sent before end of Sunday.', style: TextStyle(fontSize: 12, color: AppTheme.primaryDark)),
        ),
        if (hasPending) ...[
          const SizedBox(height: 12),
          const Text('⏳ You have a pending withdrawal being processed.', style: TextStyle(color: AppTheme.warning, fontSize: 13, fontWeight: FontWeight.w600)),
        ] else if (!isWeekend) ...[
          const SizedBox(height: 12),
          const Text('Come back on Saturday or Sunday to request a withdrawal.', style: TextStyle(color: AppTheme.textSecondary, fontSize: 13)),
        ] else if (balance < threshold) ...[
          const SizedBox(height: 12),
          Text('Balance (\$${balance.toStringAsFixed(2)}) is below the threshold (\$${threshold.toStringAsFixed(2)}).', style: const TextStyle(color: AppTheme.textSecondary, fontSize: 13)),
        ] else ...[
          const SizedBox(height: 14),
          SizedBox(width: double.infinity, child: ElevatedButton(onPressed: _requestWithdrawal, child: const Text('Request Withdrawal'))),
        ],
      ]),
    );
  }

  Widget _historyCard(List<Map> withdrawals) {
    return Container(
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppTheme.border)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(padding: EdgeInsets.all(16), child: Text('Withdrawal History', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700))),
          const Divider(height: 1),
          if (withdrawals.isEmpty)
            const Padding(padding: EdgeInsets.all(24), child: Center(child: Text('No withdrawals yet', style: TextStyle(color: AppTheme.textSecondary))))
          else
            ...withdrawals.map(_withdrawalRow),
        ],
      ),
    );
  }

  Widget _withdrawalRow(Map w) {
    final status = w['status'] as String;
    final color = status == 'paid' ? AppTheme.primary : status == 'pending' ? AppTheme.warning : AppTheme.danger;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(children: [
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text('\$${(w['amount'] as num).toStringAsFixed(2)}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
          Text(_networkLabel(w['network'] ?? w['method'] ?? ''), style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
          if (status == 'paid' && w['receipt_hash'] != null)
            Text('Tx: ${w['receipt_hash'].toString().substring(0, 12)}...', style: const TextStyle(fontSize: 11, color: AppTheme.primary, fontFamily: 'monospace')),
          if (status == 'rejected' && w['admin_note'] != null)
            Text(w['admin_note'], style: const TextStyle(fontSize: 11, color: AppTheme.danger)),
        ])),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
          decoration: BoxDecoration(color: color.withAlpha(25), borderRadius: BorderRadius.circular(12)),
          child: Text(status[0].toUpperCase() + status.substring(1), style: TextStyle(color: color, fontSize: 12, fontWeight: FontWeight.w600)),
        ),
      ]),
    );
  }

  String _networkLabel(String n) {
    if (n.contains('trc20')) return 'USDT (TRC20)';
    if (n.contains('bep20')) return 'USDT (BEP20)';
    return n.toUpperCase();
  }
}
