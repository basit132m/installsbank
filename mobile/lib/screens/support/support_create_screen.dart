import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../services/api_service.dart';

class SupportCreateScreen extends StatefulWidget {
  const SupportCreateScreen({super.key});

  @override
  State<SupportCreateScreen> createState() => _SupportCreateScreenState();
}

class _SupportCreateScreenState extends State<SupportCreateScreen> {
  final _subjectCtrl = TextEditingController();
  final _messageCtrl = TextEditingController();
  bool _loading = false;
  String? _selectedCategory;

  final List<String> _categories = [
    'Account & Billing',
    'Technical Issue',
    'Payment Problem',
    'Contract Query',
    'Other',
  ];

  @override
  void dispose() {
    _subjectCtrl.dispose();
    _messageCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (_subjectCtrl.text.trim().isEmpty || _messageCtrl.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please fill in all fields'),
          backgroundColor: AppTheme.danger,
          behavior: SnackBarBehavior.floating,
        ),
      );
      return;
    }
    setState(() => _loading = true);
    final res = await ApiService.post('/support', {
      'subject': _subjectCtrl.text.trim(),
      'message': _messageCtrl.text.trim(),
    });
    if (!mounted) return;
    setState(() => _loading = false);
    if (res.ok) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
        content: Text('Ticket submitted successfully!'),
        backgroundColor: AppTheme.primary,
        behavior: SnackBarBehavior.floating,
      ));
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
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(title: const Text('New Support Ticket')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(children: [
          // Header info
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(gradient: AppTheme.primaryGradient, borderRadius: BorderRadius.circular(16)),
            child: const Row(children: [
              Icon(Icons.support_agent_rounded, color: Colors.white, size: 28),
              SizedBox(width: 12),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text('Contact Support', style: TextStyle(color: Colors.white, fontSize: 15, fontWeight: FontWeight.w800)),
                SizedBox(height: 2),
                Text('We typically respond within 24 hours', style: TextStyle(color: Colors.white70, fontSize: 12)),
              ])),
            ]),
          ),
          const SizedBox(height: 20),

          // Category
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppTheme.border), boxShadow: AppTheme.cardShadow),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('Category', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppTheme.textSecondary, letterSpacing: 0.3)),
              const SizedBox(height: 12),
              Wrap(spacing: 8, runSpacing: 8, children: _categories.map((cat) {
                final selected = _selectedCategory == cat;
                return GestureDetector(
                  onTap: () {
                    setState(() => _selectedCategory = cat);
                    if (_subjectCtrl.text.isEmpty) _subjectCtrl.text = cat;
                  },
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                    decoration: BoxDecoration(
                      gradient: selected ? AppTheme.primaryGradient : null,
                      color: selected ? null : const Color(0xFFF8FAFC),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: selected ? Colors.transparent : AppTheme.border),
                    ),
                    child: Text(cat, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: selected ? Colors.white : AppTheme.textSecondary)),
                  ),
                );
              }).toList()),
            ]),
          ),
          const SizedBox(height: 14),

          // Form
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppTheme.border), boxShadow: AppTheme.cardShadow),
            child: Column(children: [
              TextField(
                controller: _subjectCtrl,
                decoration: const InputDecoration(
                  labelText: 'Subject',
                  prefixIcon: Icon(Icons.title_rounded, size: 20),
                ),
              ),
              const SizedBox(height: 14),
              TextField(
                controller: _messageCtrl,
                decoration: const InputDecoration(
                  labelText: 'Describe your issue',
                  alignLabelWithHint: true,
                  prefixIcon: Padding(padding: EdgeInsets.only(bottom: 80), child: Icon(Icons.message_outlined, size: 20)),
                ),
                maxLines: 6,
                minLines: 4,
              ),
            ]),
          ),
          const SizedBox(height: 20),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: _loading ? null : _submit,
              icon: _loading
                  ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                  : const Icon(Icons.send_rounded, size: 18),
              label: Text(_loading ? 'Submitting...' : 'Submit Ticket'),
            ),
          ),
          const SizedBox(height: 40),
        ]),
      ),
    );
  }
}
