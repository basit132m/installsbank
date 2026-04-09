@extends('layouts.admin')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#166534;font-weight:600;">
    ✓ {{ session('success') }}
</div>
@endif

<div style="display:grid;grid-template-columns:340px 1fr;gap:24px;align-items:start;">

    {{-- Left: Avatar card --}}
    <div class="card" style="text-align:center;">
        <div style="margin-bottom:20px;">
            @if($user->avatar)
                <img src="{{ asset('avatars/' . $user->avatar) }}" alt="Avatar"
                     style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;margin:0 auto 12px;display:block;">
            @else
                <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#01BF63,#00874a);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:36px;font-weight:800;color:white;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div style="font-size:17px;font-weight:800;color:#111827;">{{ $user->name }}</div>
            <div style="font-size:13px;color:#6b7280;margin-top:3px;text-transform:capitalize;">{{ $user->role }}</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ $user->email }}</div>
        </div>

        {{-- Upload avatar --}}
        <form method="POST" action="{{ route('admin.profile.avatar') }}" enctype="multipart/form-data">
            @csrf
            <label style="display:block;background:#f9fafb;border:2px dashed #d1d5db;border-radius:10px;padding:16px;cursor:pointer;transition:border-color .15s;"
                   onmouseover="this.style.borderColor='#01BF63'" onmouseout="this.style.borderColor='#d1d5db'">
                <input type="file" name="avatar" accept="image/*" style="display:none;" onchange="this.form.submit()">
                <svg width="24" height="24" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" style="margin:0 auto 6px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <div style="font-size:13px;color:#6b7280;">Click to upload photo</div>
                <div style="font-size:11px;color:#9ca3af;margin-top:3px;">JPG, PNG, WebP — max 2MB</div>
            </label>
        </form>

        @if($user->avatar)
        <form method="POST" action="{{ route('admin.profile.avatar.remove') }}" style="margin-top:10px;">
            @csrf
            <button type="submit" onclick="return confirm('Remove avatar?')"
                style="background:none;border:1px solid #fca5a5;color:#ef4444;border-radius:8px;padding:7px 16px;font-size:12px;font-weight:600;cursor:pointer;width:100%;">
                Remove Photo
            </button>
        </form>
        @endif

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6;text-align:left;">
            <div style="font-size:12px;color:#9ca3af;margin-bottom:8px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Account Info</div>
            <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">Member since: <strong style="color:#374151;">{{ $user->created_at->format('M d, Y') }}</strong></div>
            <div style="font-size:13px;color:#6b7280;">Last login: <strong style="color:#374151;">{{ $user->last_login_at?->diffForHumans() ?? 'N/A' }}</strong></div>
        </div>
    </div>

    {{-- Right: Forms --}}
    <div style="display:flex;flex-direction:column;gap:24px;">

        {{-- Personal Info --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:20px;">Personal Information</div>
            <form method="POST" action="{{ route('admin.profile.update') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">Phone <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+1 234 567 890">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="card" id="password">
            <div class="card-title" style="margin-bottom:20px;">Change Password</div>
            <form method="POST" action="{{ route('admin.profile.password') }}">
                @csrf
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    @error('current_password')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                        @error('password')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                    </div>
                </div>
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#78350f;">
                    Password must be at least 8 characters.
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>

    </div>
</div>

@endsection
