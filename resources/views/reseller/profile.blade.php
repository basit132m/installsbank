@extends('layouts.reseller')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:900px;" class="profileGrid">

    <div class="card">
        <div class="card-title mb-4">Account Details</div>
        <form method="POST" action="{{ route('reseller.profile.update') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled style="background:#f9fafb;color:#9ca3af;">
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Email cannot be changed. Contact support if needed.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+92 ...">
            </div>
            <div class="form-group">
                <label class="form-label">Telegram</label>
                <input type="text" name="telegram" class="form-control" value="{{ old('telegram', $user->telegram) }}" placeholder="@username">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <div class="card">
        <div class="card-title mb-4">Change Password</div>
        <form method="POST" action="{{ route('reseller.profile.password') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>

</div>
@endsection

@push('styles')
<style>@media(max-width:800px){ .profileGrid { grid-template-columns:1fr !important; } }</style>
@endpush
