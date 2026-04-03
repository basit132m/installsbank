@extends('layouts.admin')
@section('title', 'Create Tracking Link')
@section('page-title', 'Create Tracking Link')

@section('content')
<div style="max-width:600px;">
    <div class="card">
        <div class="card-title mb-1">Create New Tracking Link</div>
        <p class="text-muted text-sm mb-4">A unique tracking code will be generated automatically.</p>
        <form method="POST" action="{{ route('admin.tracking.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Publisher</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Select publisher...</option>
                    @foreach($publishers as $pub)
                        <option value="{{ $pub->id }}" {{ old('user_id') == $pub->id ? 'selected' : '' }}>{{ $pub->name }} — {{ $pub->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tracking Domain</label>
                <select name="tracking_domain_id" class="form-control form-select">
                    <option value="">Default (installsbank.com)</option>
                    @foreach($domains as $d)
                        <option value="{{ $d->id }}" {{ old('tracking_domain_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->domain }}{{ $d->label ? ' — ' . $d->label : '' }}
                        </option>
                    @endforeach
                </select>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">
                    Select a custom domain for this link's tracking URL. <a href="{{ route('admin.tracking-domains.index') }}" style="color:var(--primary);">Manage domains →</a>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Link Name (Optional)</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Main Campaign">
            </div>

            <!-- Default / Fallback URL -->
            <div class="form-group">
                <label class="form-label">Default Destination URL <span style="color:#ef4444;">*</span></label>
                <input type="url" name="original_url" class="form-control" value="{{ old('original_url') }}" placeholder="https://example.com/download" required>
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
                    <input type="url" name="url_windows" class="form-control" value="{{ old('url_windows') }}" placeholder="https://example.com/download/windows">
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                        <span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">ANDROID</span>
                        Android URL
                    </label>
                    <input type="url" name="url_android" class="form-control" value="{{ old('url_android') }}" placeholder="https://play.google.com/store/...">
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                        <span style="background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">MAC / iOS</span>
                        Mac / iOS URL
                    </label>
                    <input type="url" name="url_mac" class="form-control" value="{{ old('url_mac') }}" placeholder="https://apps.apple.com/...">
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                        <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">OTHER</span>
                        Other Devices URL
                    </label>
                    <input type="url" name="url_other" class="form-control" value="{{ old('url_other') }}" placeholder="https://example.com/download">
                    <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Linux, unknown OS, bots, etc. If blank, default URL is used.</div>
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Create Link</button>
                <a href="{{ route('admin.tracking.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
