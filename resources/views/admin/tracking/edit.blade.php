@extends('layouts.admin')
@section('title', 'Edit Tracking Link')
@section('page-title', 'Edit Tracking Link')

@section('content')
<div style="max-width:600px;">
    <div class="card">
        <div class="card-title mb-1">Edit Tracking Link</div>
        <div style="font-size:13px;color:#6b7280;margin-bottom:20px;">
            Code: <code style="background:#f3f4f6;padding:2px 8px;border-radius:4px;font-size:13px;">{{ $trackingLink->unique_code }}</code>
            &nbsp;·&nbsp; Publisher: <strong>{{ $trackingLink->user->name }}</strong>
        </div>

        <form method="POST" action="{{ route('admin.tracking.update', $trackingLink) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Link Name (Optional)</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $trackingLink->name) }}" placeholder="e.g. Main Campaign">
            </div>

            <!-- Default / Fallback URL -->
            <div class="form-group">
                <label class="form-label">Default Destination URL <span style="color:#ef4444;">*</span></label>
                <input type="url" name="original_url" class="form-control" value="{{ old('original_url', $trackingLink->original_url) }}" required placeholder="https://example.com/download">
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Used when no device-specific URL is set, or for devices not listed below.</div>
            </div>

            <!-- Device-specific URLs -->
            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:16px;margin-bottom:16px;">
                <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Device-Specific URLs <span style="font-weight:400;color:#9ca3af;">(optional — overrides default for that device)</span>
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                        <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">WINDOWS</span>
                        Windows URL
                    </label>
                    <input type="url" name="url_windows" class="form-control" value="{{ old('url_windows', $trackingLink->url_windows) }}" placeholder="https://example.com/download/windows">
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                        <span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">ANDROID</span>
                        Android URL
                    </label>
                    <input type="url" name="url_android" class="form-control" value="{{ old('url_android', $trackingLink->url_android) }}" placeholder="https://play.google.com/store/...">
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                        <span style="background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">MAC / iOS</span>
                        Mac / iOS URL
                    </label>
                    <input type="url" name="url_mac" class="form-control" value="{{ old('url_mac', $trackingLink->url_mac) }}" placeholder="https://apps.apple.com/...">
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                        <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">OTHER</span>
                        Other Devices URL
                    </label>
                    <input type="url" name="url_other" class="form-control" value="{{ old('url_other', $trackingLink->url_other) }}" placeholder="https://example.com/download">
                    <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Linux, unknown OS, bots, etc. If blank, default URL is used.</div>
                </div>
            </div>

            <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:12px;margin-bottom:16px;">
                <div style="font-size:13px;font-weight:600;color:#92400e;margin-bottom:4px;">Note</div>
                <div style="font-size:13px;color:#92400e;">Changes take effect immediately. All active clicks will redirect to the new URLs.</div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.tracking.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
