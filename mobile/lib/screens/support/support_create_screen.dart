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

  @override
  void dispose() {
    _subjectCtrl.dispose();
    _messageCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (_subjectCtrl.text.trim().isEmpty || _messageCtrl.text.trim().isEmpty) return;
    setState(() => _loading = true);
    final res = await ApiService.post('/support', {
      'subject': _subjectCtrl.text.trim(),
      'message': _messageCtrl.text.trim(),
    });
    if (!mounted) return;
    setState(() => _loading = false);
    if (res.ok) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Ticket submitted.'), backgroundColor: AppTheme.primary));
    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res.message), backgroundColor: AppTheme.danger));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('New Support Ticket')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(children: [
          TextField(controller: _subjectCtrl, decoration: const InputDecoration(labelText: 'Subject')),
          const SizedBox(height: 14),
          TextField(controller: _messageCtrl, decoration: const InputDecoration(labelText: 'Describe your issue', alignLabelWithHint: true), maxLines: 6),
          const SizedBox(height: 20),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: _loading ? null : _submit,
              child: _loading ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : const Text('Submit Ticket'),
            ),
          ),
        ]),
      ),
    );
  }
}
