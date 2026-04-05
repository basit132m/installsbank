@extends('layouts.advertiser')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')

<div style="max-width:720px;">

    {{-- Communication notice --}}
    <div class="alert alert-info" style="display:flex;align-items:flex-start;gap:12px;margin-bottom:24px;">
        <svg width="20" height="20" fill="none" stroke="#1e40af" viewBox="0 0 24 24" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <div style="font-weight:700;margin-bottom:3px;">Contact Channels Required</div>
            <div style="font-size:13px;line-height:1.6;">
                <strong>Telegram and WhatsApp are required</strong> for account communication and campaign management.
                Our team uses these channels to discuss campaign setup, billing, traffic quality, and important updates.
            </div>
        </div>
    </div>

    {{-- Profile Info Card --}}
    <div class="card mb-6">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f3f4f6;">
            <div style="width:40px;height:40px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <div class="card-title" style="margin-bottom:2px;">Profile Information</div>
                <div style="font-size:13px;color:#6b7280;">Update your account details and contact information</div>
            </div>
        </div>

        <form method="POST" action="{{ route('advertiser.profile.update') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Full Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $user->name) }}"
                       placeholder="John Doe" required autocomplete="name">
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control"
                       value="{{ $user->email }}" disabled
                       style="background:#f9fafb;color:#6b7280;cursor:not-allowed;">
                <div class="form-hint">Email address cannot be changed. Contact support if needed.</div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Company Name <span style="color:#9ca3af;font-weight:400;font-size:11px;">(optional)</span></label>
                    <input type="text" name="company_name" class="form-control"
                           value="{{ old('company_name', $profile->company_name ?? '') }}"
                           placeholder="Acme Inc.">
                </div>
                <div class="form-group">
                    <label class="form-label">Website <span style="color:#9ca3af;font-weight:400;font-size:11px;">(optional)</span></label>
                    <input type="url" name="website" class="form-control"
                           value="{{ old('website', $profile->website ?? $user->website ?? '') }}"
                           placeholder="https://yourapp.com">
                </div>
            </div>

            {{-- Contact fields --}}
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:16px 18px;margin-bottom:18px;">
                <div style="font-size:12px;font-weight:700;color:#1e40af;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                    <svg width="13" height="13" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Communication Channels — Required
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">
                            Telegram Username <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="telegram" class="form-control"
                               value="{{ old('telegram', $profile->telegram ?? $user->telegram ?? '') }}"
                               placeholder="@username" required>
                        <div class="form-hint" style="color:#3b82f6;font-weight:500;display:flex;align-items:center;gap:4px;margin-top:5px;">
                            <svg width="11" height="11" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Required for account management
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">
                            WhatsApp Number <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="whatsapp" class="form-control"
                               value="{{ old('whatsapp', $profile->whatsapp ?? '') }}"
                               placeholder="+1 234 567 8900" required>
                        <div class="form-hint" style="color:#3b82f6;font-weight:500;display:flex;align-items:center;gap:4px;margin-top:5px;">
                            <svg width="11" height="11" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Required for account management
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number <span style="color:#9ca3af;font-weight:400;font-size:11px;">(optional)</span></label>
                <input type="text" name="phone" class="form-control"
                       value="{{ old('phone', $user->phone ?? '') }}"
                       placeholder="+1 234 567 8900" autocomplete="tel">
            </div>

            <div style="display:flex;align-items:center;gap:16px;margin-top:4px;">
                <button type="submit" class="btn btn-blue">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
                <div class="text-sm text-muted">
                    Member since {{ $user->created_at->format('M Y') }}
                </div>
            </div>
        </form>
    </div>

    {{-- Change Password Card --}}
    <div class="card" id="password">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f3f4f6;">
            <div style="width:40px;height:40px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <div class="card-title" style="margin-bottom:2px;">Change Password</div>
                <div style="font-size:13px;color:#6b7280;">Use a strong password with at least 8 characters</div>
            </div>
        </div>

        <form method="POST" action="{{ route('advertiser.profile.password') }}" style="max-width:480px;">
            @csrf

            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control"
                       placeholder="Your current password" required autocomplete="current-password">
                @error('current_password')
                    <div style="font-size:12px;color:#ef4444;margin-top:4px;display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" fill="none" stroke="#ef4444" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min 8 characters" required autocomplete="new-password" minlength="8">
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control"
                       placeholder="Repeat new password" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-blue">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Update Password
            </button>
        </form>
    </div>

</div>
@endsection
