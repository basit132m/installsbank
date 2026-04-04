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
      setState(() { _data = res.data as Map<String, dynamic>; _loading = false; });
      _addressCtrl.text = _data!['payment_address']?.toString() ?? '';
      _networkCtrl.value = _data!['payment_network']?.toString() ?? 'trc20';
    } else {
      setState(() { _error = res.message; _loading = false; });
    }
  }

  Future<void> _saveAddress() async {
    if (_addressCtrl.text.trim().isEmpty) {
      _showSnack('Please enter a wallet address', false);
      return;
    }
    final res = await ApiService.post('/withdrawals/save-address', {
      'payment_network': _networkCtrl.value,
      'payment_address': _addressCtrl.text.trim(),
    });
    if (!mounted) return;
    _showSnack(res.message, res.ok);
    if (res.ok) _load();
  }

  Future<void> _requestWithdrawal() async {
    final balance = _toDouble(_data!['balance']);
    final network = _data!['payment_network']?.toString() ?? '';
    final address = _data!['payment_address']?.toString() ?? '';

    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text('Confirm Withdrawal', style: TextStyle(fontWeight: FontWeight.w800)),
        content: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(gradient: AppTheme.primaryGradient, borderRadius: BorderRadius.circular(12)),
            child: Row(children: [
              const Icon(Icons.account_balance_wallet_rounded, color: Colors.white, size: 20),
              const SizedBox(width: 10),
              Text('\$${balance.toStringAsFixed(2)} USDT', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 18)),
            ]),
          ),
          const SizedBox(height: 14),
          _confirmRow('Network', network.toUpperCase()),
          const SizedBox(height: 6),
          _confirmRow('Address', address.length > 16 ? '${address.substring(0, 8)}...${address.substring(address.length - 8)}' : address),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(color: AppTheme.warning.withAlpha(15), borderRadius: BorderRadius.circular(8)),
            child: const Text('Payments are processed before end of Sunday.', style: TextStyle(fontSize: 12, color: AppTheme.warning, fontWeight: FontWeight.w600)),
          ),
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
    _showSnack(res.message, res.ok);
    if (res.ok) _load();
  }

  Widget _confirmRow(String label, String value) {
    return Row(children: [
      Text('$label: ', style: const TextStyle(color: AppTheme.textSecondary, fontSize: 13)),
      Expanded(child: Text(value, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13))),
    ]);
  }

  void _showSnack(String msg, bool ok) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg),
      backgroundColor: ok ? AppTheme.primary : AppTheme.danger,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
    ));
  }

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Payments'),
        centerTitle: false,
        actions: [IconButton(icon: const Icon(Icons.refresh_rounded), onPressed: _load)],
      ),
      body: _loading
          ? const LoadingWidget()
          : _error != null
              ? ErrorWidget2(message: _error!, onRetry: _load)
              : RefreshIndicator(onRefresh: _load, color: AppTheme.primary, child: _buildBody()),
    );
  }

  Widget _buildBody() {
    final paymentEnabled = (_data!['payment_enabled'] as bool?) ?? false;
    final isWeekend = (_data!['is_weekend'] as bool?) ?? false;
    final hasPending = (_data!['has_pending'] as bool?) ?? false;
    final balance = _toDouble(_data!['balance']);
    final threshold = _toDouble(_data!['threshold']);
    final pendingBalance = _toDouble(_data!['pending_balance']);
    final totalWithdrawn = _toDouble(_data!['total_withdrawn']);
    final withdrawals = ((_data!['withdrawals'] as List?) ?? []).map((e) => Map<String, dynamic>.from(e as Map)).toList();

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        if (!paymentEnabled) _disabledBanner(),

        // Balance cards
        Row(children: [
          Expanded(child: BalanceCard(
            label: 'AVAILABLE',
            value: '\$${balance.toStringAsFixed(2)}',
            color: AppTheme.primary,
            subtitle: 'Min: \$${threshold.toStringAsFixed(0)}',
            icon: Icons.account_balance_wallet_rounded,
            gradient: AppTheme.primaryGradient,
          )),
          const SizedBox(width: 10),
          Expanded(child: BalanceCard(
            label: 'PENDING',
            value: '\$${pendingBalance.toStringAsFixed(2)}',
            color: AppTheme.warning,
            icon: Icons.hourglass_bottom_rounded,
            gradient: AppTheme.orangeGradient,
          )),
          const SizedBox(width: 10),
          Expanded(child: BalanceCard(
            label: 'PAID OUT',
            value: '\$${totalWithdrawn.toStringAsFixed(2)}',
            color: AppTheme.info,
            icon: Icons.check_circle_rounded,
            gradient: AppTheme.blueGradient,
          )),
        ]),
        const SizedBox(height: 16),

        // Payment address
        _addressCard(),
        const SizedBox(height: 16),

        // Withdrawal request
        if (paymentEnabled) _withdrawCard(isWeekend, hasPending, balance, threshold),
        if (paymentEnabled) const SizedBox(height: 16),

        // History
        _historyCard(withdrawals),
        const SizedBox(height: 80),
      ],
    );
  }

  Widget _disabledBanner() => Container(
    margin: const EdgeInsets.only(bottom: 16),
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(
      color: AppTheme.warning.withAlpha(15),
      borderRadius: BorderRadius.circular(14),
      border: Border.all(color: AppTheme.warning.withAlpha(50)),
    ),
    child: Row(children: [
      Container(
        width: 36,
        height: 36,
        decoration: BoxDecoration(gradient: AppTheme.orangeGradient, borderRadius: BorderRadius.circular(10)),
        child: const Icon(Icons.lock_outline_rounded, color: Colors.white, size: 18),
      ),
      const SizedBox(width: 12),
      const Expanded(child: Text('Withdrawals are not yet enabled for your account. Contact support to get started.', style: TextStyle(fontSize: 13, color: AppTheme.textPrimary, fontWeight: FontWeight.w500))),
    ]),
  );

  Widget _addressCard() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
        boxShadow: AppTheme.cardShadow,
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(gradient: AppTheme.primaryGradient, borderRadius: BorderRadius.circular(10)),
            child: const Icon(Icons.qr_code_rounded, color: Colors.white, size: 20),
          ),
          const SizedBox(width: 12),
          const Text('USDT Payment Address', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppTheme.textPrimary)),
        ]),
        const SizedBox(height: 16),
        ValueListenableBuilder(
          valueListenable: _networkCtrl,
          builder: (_, v, __) => Row(children: [
            Expanded(child: _networkBtn('TRC20', 'TRON Network', 'trc20', v)),
            const SizedBox(width: 10),
            Expanded(child: _networkBtn('BEP20', 'BSC Network', 'bep20', v)),
          ]),
        ),
        const SizedBox(height: 14),
        TextField(
          controller: _addressCtrl,
          decoration: const InputDecoration(
            labelText: 'USDT Wallet Address',
            hintText: 'Paste your wallet address here',
            prefixIcon: Icon(Icons.wallet, size: 20),
          ),
          style: const TextStyle(fontFamily: 'monospace', fontSize: 13),
        ),
        const SizedBox(height: 14),
        SizedBox(
          width: double.infinity,
          child: ElevatedButton.icon(
            onPressed: _saveAddress,
            icon: const Icon(Icons.save_rounded, size: 18),
            label: const Text('Save Address'),
          ),
        ),
      ]),
    );
  }

  Widget _networkBtn(String title, String subtitle, String value, String current) {
    final selected = current == value;
    return GestureDetector(
      onTap: () => _networkCtrl.value = value,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 14),
        decoration: BoxDecoration(
          gradient: selected ? AppTheme.primaryGradient : null,
          color: selected ? null : const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: selected ? Colors.transparent : AppTheme.border, width: 1),
        ),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(title, style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: selected ? Colors.white : AppTheme.textPrimary)),
          Text(subtitle, style: TextStyle(fontSize: 11, color: selected ? Colors.white70 : AppTheme.textSecondary)),
        ]),
      ),
    );
  }

  Widget _withdrawCard(bool isWeekend, bool hasPending, double balance, double threshold) {
    final canWithdraw = isWeekend && !hasPending && balance >= threshold;
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: canWithdraw ? AppTheme.primary : AppTheme.border),
        boxShadow: AppTheme.cardShadow,
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              gradient: isWeekend ? AppTheme.primaryGradient : AppTheme.orangeGradient,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(isWeekend ? Icons.check_circle_rounded : Icons.schedule_rounded, color: Colors.white, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(
              isWeekend ? 'Withdrawals Open' : 'Withdrawals Closed',
              style: TextStyle(fontWeight: FontWeight.w700, color: isWeekend ? AppTheme.primary : AppTheme.warning, fontSize: 14),
            ),
            Text(
              isWeekend ? 'Available this weekend' : 'Opens Saturday & Sunday',
              style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary),
            ),
          ])),
        ]),
        const SizedBox(height: 14),
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(10)),
          child: const Text('Your full balance is submitted as one withdrawal. Payments processed before end of Sunday (USA Eastern Time).', style: TextStyle(fontSize: 12, color: AppTheme.primaryDark, height: 1.5)),
        ),
        if (hasPending) ...[
          const SizedBox(height: 14),
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: AppTheme.warning.withAlpha(15), borderRadius: BorderRadius.circular(10)),
            child: const Row(children: [
              Icon(Icons.hourglass_bottom_rounded, color: AppTheme.warning, size: 18),
              SizedBox(width: 8),
              Text('Withdrawal pending — being processed', style: TextStyle(color: AppTheme.warning, fontSize: 13, fontWeight: FontWeight.w600)),
            ]),
          ),
        ] else if (!isWeekend) ...[
          const SizedBox(height: 12),
          const Text('Come back on Saturday or Sunday to request a withdrawal.', style: TextStyle(color: AppTheme.textSecondary, fontSize: 13)),
        ] else if (balance < threshold) ...[
          const SizedBox(height: 12),
          Text('Balance \$${balance.toStringAsFixed(2)} is below the minimum of \$${threshold.toStringAsFixed(2)}.', style: const TextStyle(color: AppTheme.textSecondary, fontSize: 13)),
        ] else ...[
          const SizedBox(height: 16),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: _requestWithdrawal,
              icon: const Icon(Icons.send_rounded, size: 18),
              label: const Text('Request Withdrawal'),
            ),
          ),
        ],
      ]),
    );
  }

  Widget _historyCard(List<Map<String, dynamic>> withdrawals) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
        boxShadow: AppTheme.cardShadow,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.fromLTRB(18, 16, 18, 12),
            child: Text('Withdrawal History', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
          ),
          const Divider(height: 1),
          if (withdrawals.isEmpty)
            Padding(
              padding: const EdgeInsets.all(32),
              child: Center(
                child: Column(children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(16)),
                    child: const Icon(Icons.history_rounded, color: AppTheme.primary, size: 28),
                  ),
                  const SizedBox(height: 10),
                  const Text('No withdrawals yet', style: TextStyle(color: AppTheme.textSecondary, fontSize: 14, fontWeight: FontWeight.w500)),
                ]),
              ),
            )
          else
            ...withdrawals.map(_withdrawalRow),
        ],
      ),
    );
  }

  Widget _withdrawalRow(Map<String, dynamic> w) {
    final status = w['status']?.toString() ?? '';
    Color statusColor;
    IconData statusIcon;
    switch (status) {
      case 'paid':
        statusColor = AppTheme.primary;
        statusIcon = Icons.check_circle_rounded;
        break;
      case 'pending':
        statusColor = AppTheme.warning;
        statusIcon = Icons.hourglass_bottom_rounded;
        break;
      default:
        statusColor = AppTheme.danger;
        statusIcon = Icons.cancel_rounded;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
      decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: AppTheme.border, width: 0.5))),
      child: Row(children: [
        Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(color: statusColor.withAlpha(15), borderRadius: BorderRadius.circular(10)),
          child: Icon(statusIcon, color: statusColor, size: 20),
        ),
        const SizedBox(width: 12),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text('\$${_toDouble(w['amount']).toStringAsFixed(2)}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16, letterSpacing: -0.3)),
          Text(_networkLabel(w['network']?.toString() ?? w['method']?.toString() ?? ''), style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
          if (status == 'paid' && w['receipt_hash'] != null)
            Text('Tx: ${w['receipt_hash'].toString().substring(0, 12)}...', style: const TextStyle(fontSize: 11, color: AppTheme.primary, fontFamily: 'monospace')),
          if (status == 'rejected' && w['admin_note'] != null)
            Text(w['admin_note'].toString(), style: const TextStyle(fontSize: 11, color: AppTheme.danger)),
        ])),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
          decoration: BoxDecoration(color: statusColor.withAlpha(15), borderRadius: BorderRadius.circular(20)),
          child: Text(
            status.isNotEmpty ? status[0].toUpperCase() + status.substring(1) : '',
            style: TextStyle(color: statusColor, fontSize: 12, fontWeight: FontWeight.w700),
          ),
        ),
      ]),
    );
  }

  String _networkLabel(String n) {
    if (n.contains('trc20')) return 'USDT (TRC20 / Tron)';
    if (n.contains('bep20')) return 'USDT (BEP20 / BSC)';
    return n.toUpperCase();
  }

  double _toDouble(dynamic v) => v == null ? 0.0 : (num.tryParse(v.toString()) ?? 0).toDouble();
}
