import 'dart:io';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../providers/auth_provider.dart';
import '../services/api_service.dart';
import '../widgets/loading_widget.dart';
import 'login_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> with AutomaticKeepAliveClientMixin {
  Map<String, dynamic>? _user;
  bool _loading = true;

  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  final _websiteCtrl = TextEditingController();
  final _curPassCtrl = TextEditingController();
  final _newPassCtrl = TextEditingController();
  final _confirmPassCtrl = TextEditingController();

  @override
  bool get wantKeepAlive => true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _nameCtrl.dispose(); _emailCtrl.dispose(); _phoneCtrl.dispose();
    _websiteCtrl.dispose(); _curPassCtrl.dispose(); _newPassCtrl.dispose(); _confirmPassCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    final res = await ApiService.get('/profile');
    if (!mounted) return;
    if (res.ok) {
      setState(() { _user = Map<String, dynamic>.from(res.data); _loading = false; });
      _nameCtrl.text = _user!['name'] ?? '';
      _emailCtrl.text = _user!['email'] ?? '';
      _phoneCtrl.text = _user!['phone'] ?? '';
      _websiteCtrl.text = _user!['website'] ?? '';
    } else {
      setState(() => _loading = false);
    }
  }

  Future<void> _updateProfile() async {
    final res = await ApiService.put('/profile', {
      'name': _nameCtrl.text.trim(),
      'email': _emailCtrl.text.trim(),
      'phone': _phoneCtrl.text.trim(),
      'website': _websiteCtrl.text.trim(),
    });
    if (!mounted) return;
    _showSnack(res.message, res.ok);
    if (res.ok) setState(() => _user = Map<String, dynamic>.from(res.data['user']));
  }

  Future<void> _changePassword() async {
    if (_newPassCtrl.text != _confirmPassCtrl.text) {
      _showSnack('Passwords do not match.', false);
      return;
    }
    final res = await ApiService.put('/profile/password', {
      'current_password': _curPassCtrl.text,
      'password': _newPassCtrl.text,
      'password_confirmation': _confirmPassCtrl.text,
    });
    if (!mounted) return;
    _showSnack(res.message, res.ok);
    if (res.ok) { _curPassCtrl.clear(); _newPassCtrl.clear(); _confirmPassCtrl.clear(); }
  }

  Future<void> _pickAvatar() async {
    final picker = ImagePicker();
    final file = await picker.pickImage(source: ImageSource.gallery, imageQuality: 80, maxWidth: 400);
    if (file == null || !mounted) return;
    final res = await ApiService.postMultipart('/profile/avatar', {}, file.path, 'avatar');
    if (!mounted) return;
    _showSnack(res.message, res.ok);
    if (res.ok) setState(() => _user!['avatar_url'] = res.data['avatar_url']);
  }

  Future<void> _logout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Logout'),
        content: const Text('Are you sure you want to logout?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
          ElevatedButton(onPressed: () => Navigator.pop(context, true), child: const Text('Logout')),
        ],
      ),
    );
    if (confirm != true || !mounted) return;
    await context.read<AuthProvider>().logout();
    if (!mounted) return;
    Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (_) => const LoginScreen()), (_) => false);
  }

  void _showSnack(String msg, bool ok) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: ok ? AppTheme.primary : AppTheme.danger));
  }

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Scaffold(
      appBar: AppBar(title: const Text('My Profile'), actions: [
        TextButton.icon(onPressed: _logout, icon: const Icon(Icons.logout, color: AppTheme.danger, size: 18), label: const Text('Logout', style: TextStyle(color: AppTheme.danger))),
      ]),
      body: _loading ? const LoadingWidget() : _buildBody(),
    );
  }

  Widget _buildBody() {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        // Avatar
        Center(
          child: Stack(children: [
            GestureDetector(
              onTap: _pickAvatar,
              child: CircleAvatar(
                radius: 46,
                backgroundColor: AppTheme.primaryLight,
                child: _user!['avatar_url'] != null
                    ? ClipOval(child: CachedNetworkImage(imageUrl: _user!['avatar_url'], width: 92, height: 92, fit: BoxFit.cover, placeholder: (_, __) => const CircularProgressIndicator(), errorWidget: (_, __, ___) => _initials()))
                    : _initials(),
              ),
            ),
            Positioned(bottom: 0, right: 0,
              child: GestureDetector(
                onTap: _pickAvatar,
                child: Container(
                  padding: const EdgeInsets.all(6),
                  decoration: const BoxDecoration(color: AppTheme.primary, shape: BoxShape.circle),
                  child: const Icon(Icons.camera_alt, color: Colors.white, size: 16),
                ),
              ),
            ),
          ]),
        ),
        const SizedBox(height: 6),
        Center(child: Text(_user!['name'] ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700))),
        Center(child: Text(_user!['email'] ?? '', style: const TextStyle(fontSize: 13, color: AppTheme.textSecondary))),
        Center(child: Container(
          margin: const EdgeInsets.only(top: 6),
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
          decoration: BoxDecoration(
            color: _user!['status'] == 'active' ? AppTheme.primaryLight : const Color(0xFFFEF3C7),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Text(
            (_user!['status'] ?? '').toString().toUpperCase(),
            style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: _user!['status'] == 'active' ? AppTheme.primary : AppTheme.warning),
          ),
        )),
        const SizedBox(height: 24),

        // Profile info form
        _formCard('Account Information', [
          _field('Full Name', _nameCtrl, Icons.person_outline),
          _field('Email', _emailCtrl, Icons.email_outlined, type: TextInputType.emailAddress),
          _field('Phone', _phoneCtrl, Icons.phone_outlined, type: TextInputType.phone),
          _field('Website', _websiteCtrl, Icons.language_outlined, type: TextInputType.url),
          const SizedBox(height: 8),
          SizedBox(width: double.infinity, child: ElevatedButton(onPressed: _updateProfile, child: const Text('Save Changes'))),
        ]),
        const SizedBox(height: 16),

        // Password
        _formCard('Change Password', [
          _field('Current Password', _curPassCtrl, Icons.lock_outline, obscure: true),
          _field('New Password', _newPassCtrl, Icons.lock_outline, obscure: true),
          _field('Confirm New Password', _confirmPassCtrl, Icons.lock_outline, obscure: true),
          const SizedBox(height: 8),
          SizedBox(width: double.infinity, child: ElevatedButton(onPressed: _changePassword, child: const Text('Update Password'))),
        ]),
        const SizedBox(height: 16),

        // Info
        Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppTheme.border)),
          child: Column(children: [
            _infoRow('Member Since', _user!['created_at'] ?? '—'),
            _infoRow('Account Status', (_user!['status'] ?? '').toString().toUpperCase()),
            _infoRow('App Version', '1.0.0'),
          ]),
        ),
        const SizedBox(height: 80),
      ],
    );
  }

  Widget _initials() => Text(
    (_user!['name'] ?? 'U').substring(0, 1).toUpperCase(),
    style: const TextStyle(fontSize: 32, fontWeight: FontWeight.w800, color: AppTheme.primary),
  );

  Widget _formCard(String title, List<Widget> children) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppTheme.border)),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(title, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
        const SizedBox(height: 14),
        ...children.map((w) => Padding(padding: const EdgeInsets.only(bottom: 12), child: w)),
      ]),
    );
  }

  Widget _field(String label, TextEditingController ctrl, IconData icon, {TextInputType? type, bool obscure = false}) {
    return TextField(
      controller: ctrl,
      keyboardType: type,
      obscureText: obscure,
      decoration: InputDecoration(labelText: label, prefixIcon: Icon(icon, size: 20)),
    );
  }

  Widget _infoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(children: [
        Expanded(child: Text(label, style: const TextStyle(color: AppTheme.textSecondary, fontSize: 13))),
        Text(value, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
      ]),
    );
  }
}
