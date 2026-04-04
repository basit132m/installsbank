@extends('layouts.publisher')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')

<div style="max-width:700px;">

<!-- Avatar Card -->
<div class="card mb-6">
    <div class="card-title mb-4">Profile Picture</div>
    <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div style="position:relative;">
            @if($user->avatar)
                <img src="/avatars/{{ $user->avatar }}" alt="Avatar"
                     style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;">
            @else
                <div style="width:88px;height:88px;border-radius:50%;background:#e6faf2;border:3px solid #e5e7eb;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;color:#01BF63;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div style="flex:1;">
            <form method="POST" action="{{ route('publisher.profile.avatar') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <label style="cursor:pointer;">
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none;" onchange="this.form.submit()">
                        <span class="btn btn-ghost" onclick="document.getElementById('avatarInput').click()">
                            Upload New Photo
                        </span>
                    </label>
                    @if($user->avatar)
                    <form method="POST" action="{{ route('publisher.profile.avatar.remove') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-ghost" style="color:#ef4444;border-color:#fca5a5;" onclick="return confirm('Remove avatar?')">Remove</button>
                    </form>
                    @endif
                </div>
                <div style="font-size:12px;color:#9ca3af;margin-top:8px;">JPG, PNG, WebP or GIF. Max 2MB.</div>
            </form>
        </div>
    </div>
</div>

<!-- Profile Info Card -->
<div class="card mb-6">
    <div class="card-title mb-4">Account Information</div>
    <form method="POST" action="{{ route('publisher.profile.update') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+1 234 567 8900">
            </div>
            <div class="form-group">
                <label class="form-label">Website</label>
                <input type="url" name="website" class="form-control" value="{{ old('website', $user->website) }}" placeholder="https://yoursite.com">
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <div style="font-size:13px;color:#9ca3af;">
                Member since {{ $user->created_at->format('M Y') }}
                · <span style="text-transform:capitalize;">{{ $user->status }}</span>
            </div>
        </div>
    </form>
</div>

<!-- Change Password Card -->
<div class="card" id="password">
    <div class="card-title mb-4">Change Password</div>
    <form method="POST" action="{{ route('publisher.profile.password') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
            @error('current_password')
                <div style="font-size:12px;color:#ef4444;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required autocomplete="new-password" minlength="8">
        </div>
        <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>

</div>
@endsection
