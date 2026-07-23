@extends('layouts.admin')
@section('title', $account->exists ? 'Edit Dashboard' : 'New Dashboard')
@section('page-title', $account->exists ? 'Edit Dashboard Account' : 'New Dashboard Account')

@section('content')
<div style="max-width:720px;">
    <a href="{{ route('admin.portal-accounts.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom:16px;">← Dashboard Accounts</a>

    <form method="POST" action="{{ $account->exists ? route('admin.portal-accounts.update', $account) : route('admin.portal-accounts.store') }}">
        @csrf
        @if($account->exists) @method('PUT') @endif

        {{-- Credentials --}}
        <div class="card" style="margin-bottom:18px;">
            <div class="card-title mb-4">Login Credentials</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $account->username) }}" required placeholder="publisher_01">
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Letters, numbers, dashes and underscores.</div>
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Password {{ $account->exists ? '(leave blank to keep)' : '*' }}</label>
                    <input type="text" name="password" class="form-control" {{ $account->exists ? '' : 'required' }} placeholder="{{ $account->exists ? '••••••••' : 'Min 6 characters' }}">
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Shown as plain text so you can copy it to share with the publisher.</div>
                </div>
            </div>
            <div class="form-group" style="margin:16px 0 0;">
                <label class="form-label">Dashboard Title (optional)</label>
                <input type="text" name="display_title" class="form-control" value="{{ old('display_title', $account->display_title) }}" placeholder="e.g. Traffic Analytics">
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Neutral heading shown at the top of the dashboard. No brand name is displayed.</div>
            </div>
        </div>

        {{-- Source --}}
        <div class="card" style="margin-bottom:18px;">
            <div class="card-title mb-4">Data Source</div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Assigned Tracking Code</label>
                <select name="tracking_link_id" class="form-control form-select">
                    <option value="">— None —</option>
                    @foreach($links as $l)
                        <option value="{{ $l['id'] }}" {{ old('tracking_link_id', $account->tracking_link_id) == $l['id'] ? 'selected' : '' }}>{{ $l['label'] }}</option>
                    @endforeach
                </select>
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">The dashboard shows Windows clicks from this tracking link. Its code is shown to the publisher as their Tracking ID.</div>
            </div>
        </div>

        {{-- Controlled display --}}
        <div class="card" style="margin-bottom:18px;">
            <div class="card-title mb-1">Displayed Clicks Control</div>
            <div style="font-size:12px;color:#9ca3af;margin-bottom:16px;">These only affect this dashboard — never the real panel. Daily shown clicks = real Windows clicks ÷ divider, then clamped into [Min, Max]. <strong style="color:#059669;">Changes apply going forward only</strong> — today's already-shown clicks stay frozen, so the publisher never sees numbers drop.</div>

            <div class="toggle-wrap mb-4" style="display:flex;align-items:center;gap:10px;">
                <label class="toggle"><input type="checkbox" name="divider_enabled" value="1" {{ old('divider_enabled', $account->divider_enabled) ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                <span style="font-size:13px;font-weight:500;">Enable divider</span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Divider Value</label>
                    <input type="number" name="divider_value" class="form-control" value="{{ old('divider_value', $account->divider_value ?? 1) }}" min="1" step="0.1">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Min Clicks / Day</label>
                    <input type="number" name="min_clicks" class="form-control" value="{{ old('min_clicks', $account->min_clicks) }}" min="0" placeholder="no floor">
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Pads up if actual is lower.</div>
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Max Clicks / Day</label>
                    <input type="number" name="max_clicks" class="form-control" value="{{ old('max_clicks', $account->max_clicks) }}" min="0" placeholder="no cap">
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Caps if actual is higher.</div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:18px;">
            <div class="toggle-wrap" style="display:flex;align-items:center;gap:10px;">
                <label class="toggle"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->exists ? $account->is_active : true) ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                <span style="font-size:13px;font-weight:500;">Account active (can log in)</span>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary">{{ $account->exists ? 'Save Changes' : 'Create Dashboard' }}</button>
            <a href="{{ route('admin.portal-accounts.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>
@endsection
