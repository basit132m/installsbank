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

  bool _obscureCur = true;
  bool _obscureNew = true;
  bool _obscureConfirm = true;

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
      setState(() { _user = Map<String, dynamic>.from(res.data as Map); _loading = false; });
      _nameCtrl.text = _user!['name']?.toString() ?? '';
      _emailCtrl.text = _user!['email']?.toString() ?? '';
      _phoneCtrl.text = _user!['phone']?.toString() ?? '';
      _websiteCtrl.text = _user!['website']?.toString() ?? '';
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
    if (res.ok) setState(() => _user = Map<String, dynamic>.from(res.data['user'] as Map));
  }

  Future<void> _changePassword() async {
    if (_newPassCtrl.text != _confirmPassCtrl.text) {
      _showSnack('Passwords do not match.', false);
      return;
    }
    if (_newPassCtrl.text.length < 8) {
      _showSnack('Password must be at least 8 characters.', false);
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
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text('Logout', style: TextStyle(fontWeight: FontWeight.w800)),
        content: const Text('Are you sure you want to logout?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.danger),
            child: const Text('Logout'),
          ),
        ],
      ),
    );
    if (confirm != true || !mounted) return;
    await context.read<AuthProvider>().logout();
    if (!mounted) return;
    Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (_) => const LoginScreen()), (_) => false);
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
        title: const Text('My Profile'),
        centerTitle: false,
        actions: [
          TextButton.icon(
            onPressed: _logout,
            icon: const Icon(Icons.logout_rounded, color: AppTheme.danger, size: 18),
            label: const Text('Logout', style: TextStyle(color: AppTheme.danger, fontWeight: FontWeight.w600)),
          ),
        ],
      ),
      body: _loading ? const LoadingWidget() : _buildBody(),
    );
  }

  Widget _buildBody() {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        // Profile header card
        Container(
          decoration: BoxDecoration(
            gradient: AppTheme.primaryGradient,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Stack(children: [
            // Background decoration
            Positioned(top: -20, right: -20, child: Container(
              width: 120,
              height: 120,
              decoration: BoxDecoration(
                color: Colors.white.withAlpha(10),
                shape: BoxShape.circle,
              ),
            )),
            Positioned(bottom: -30, right: 40, child: Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: Colors.white.withAlpha(8),
                shape: BoxShape.circle,
              ),
            )),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Row(children: [
                GestureDetector(
                  onTap: _pickAvatar,
                  child: Stack(children: [
                    CircleAvatar(
                      radius: 38,
                      backgroundColor: Colors.white.withAlpha(30),
                      child: _user!['avatar_url'] != null
                          ? ClipOval(child: CachedNetworkImage(
                              imageUrl: _user!['avatar_url'].toString(),
                              width: 76,
                              height: 76,
                              fit: BoxFit.cover,
                              placeholder: (_, __) => const CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                              errorWidget: (_, __, ___) => _initials(),
                            ))
                          : _initials(),
                    ),
                    Positioned(bottom: 0, right: 0, child: Container(
                      width: 26,
                      height: 26,
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(13)),
                      child: const Icon(Icons.camera_alt_rounded, color: AppTheme.primary, size: 14),
                    )),
                  ]),
                ),
                const SizedBox(width: 16),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(_user!['name']?.toString() ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Colors.white, letterSpacing: -0.3)),
                  const SizedBox(height: 2),
                  Text(_user!['email']?.toString() ?? '', style: const TextStyle(fontSize: 13, color: Colors.white70)),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: _user!['status'] == 'active' ? Colors.white.withAlpha(30) : Colors.orange.withAlpha(60),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: Colors.white.withAlpha(50)),
                    ),
                    child: Text(
                      (_user!['status']?.toString() ?? '').toUpperCase(),
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: Colors.white),
                    ),
                  ),
                ])),
              ]),
            ),
          ]),
        ),
        const SizedBox(height: 20),

        // Account info form
        _formCard(
          'Account Information',
          Icons.person_rounded,
          [
            _field('Full Name', _nameCtrl, Icons.person_outline_rounded),
            _field('Email Address', _emailCtrl, Icons.email_outlined, type: TextInputType.emailAddress),
            _field('Phone Number', _phoneCtrl, Icons.phone_outlined, type: TextInputType.phone),
            _field('Website', _websiteCtrl, Icons.language_outlined, type: TextInputType.url),
          ],
          ElevatedButton.icon(
            onPressed: _updateProfile,
            icon: const Icon(Icons.save_rounded, size: 18),
            label: const Text('Save Changes'),
            style: ElevatedButton.styleFrom(minimumSize: const Size(double.infinity, 50)),
          ),
        ),
        const SizedBox(height: 16),

        // Password change
        _formCard(
          'Change Password',
          Icons.lock_rounded,
          [
            _passField('Current Password', _curPassCtrl, _obscureCur, () => setState(() => _obscureCur = !_obscureCur)),
            _passField('New Password', _newPassCtrl, _obscureNew, () => setState(() => _obscureNew = !_obscureNew)),
            _passField('Confirm New Password', _confirmPassCtrl, _obscureConfirm, () => setState(() => _obscureConfirm = !_obscureConfirm)),
          ],
          ElevatedButton.icon(
            onPressed: _changePassword,
            icon: const Icon(Icons.lock_open_rounded, size: 18),
            label: const Text('Update Password'),
            style: ElevatedButton.styleFrom(minimumSize: const Size(double.infinity, 50)),
          ),
        ),
        const SizedBox(height: 16),

        // Account info
        Container(
          padding: const EdgeInsets.all(18),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppTheme.border), boxShadow: AppTheme.cardShadow),
          child: Column(children: [
            _infoRow(Icons.calendar_today_rounded, 'Member Since', _user!['created_at']?.toString() ?? '—'),
            const Divider(height: 20),
            _infoRow(Icons.verified_user_rounded, 'Account Status', (_user!['status']?.toString() ?? '').toUpperCase()),
            const Divider(height: 20),
            _infoRow(Icons.phone_android_rounded, 'App Version', '1.0.0'),
          ]),
        ),
        const SizedBox(height: 80),
      ],
    );
  }

  Widget _initials() => Text(
    (_user!['name']?.toString() ?? 'U').substring(0, 1).toUpperCase(),
    style: const TextStyle(fontSize: 28, fontWeight: FontWeight.w800, color: Colors.white),
  );

  Widget _formCard(String title, IconData icon, List<Widget> fields, Widget button) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppTheme.border), boxShadow: AppTheme.cardShadow),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(gradient: AppTheme.primaryGradient, borderRadius: BorderRadius.circular(10)),
            child: Icon(icon, color: Colors.white, size: 18),
          ),
          const SizedBox(width: 12),
          Text(title, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppTheme.textPrimary)),
        ]),
        const SizedBox(height: 16),
        ...fields.map((w) => Padding(padding: const EdgeInsets.only(bottom: 12), child: w)),
        const SizedBox(height: 4),
        button,
      ]),
    );
  }

  Widget _field(String label, TextEditingController ctrl, IconData icon, {TextInputType? type}) {
    return TextField(
      controller: ctrl,
      keyboardType: type,
      decoration: InputDecoration(labelText: label, prefixIcon: Icon(icon, size: 20)),
    );
  }

  Widget _passField(String label, TextEditingController ctrl, bool obscure, VoidCallback toggle) {
    return TextField(
      controller: ctrl,
      obscureText: obscure,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: const Icon(Icons.lock_outline_rounded, size: 20),
        suffixIcon: IconButton(
          icon: Icon(obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined, size: 20, color: AppTheme.textSecondary),
          onPressed: toggle,
        ),
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value) {
    return Row(children: [
      Container(
        width: 32,
        height: 32,
        decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(8)),
        child: Icon(icon, size: 16, color: AppTheme.primary),
      ),
      const SizedBox(width: 12),
      Expanded(child: Text(label, style: const TextStyle(color: AppTheme.textSecondary, fontSize: 13))),
      Text(value, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: AppTheme.textPrimary)),
    ]);
  }
}
