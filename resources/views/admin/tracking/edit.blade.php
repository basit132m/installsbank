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
                <label class="form-label">Tracking Domain</label>
                <select name="tracking_domain_id" class="form-control form-select">
                    <option value="">Default (installsbank.com)</option>
                    @foreach($domains as $d)
                        <option value="{{ $d->id }}" {{ old('tracking_domain_id', $trackingLink->tracking_domain_id) == $d->id ? 'selected' : '' }}>
                            {{ $d->domain }}{{ $d->label ? ' — ' . $d->label : '' }}
                        </option>
                    @endforeach
                </select>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">
                    <a href="{{ route('admin.tracking-domains.index') }}" style="color:var(--primary);">Manage domains →</a>
                </div>
            </div>
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

            <!-- Auto Windows Redirect Timer (Pakistan Time) -->
            @php
                $slots = $trackingLink->windows_schedules ?? [];
                $scheduleOn = (bool) $trackingLink->windows_schedule_enabled;
            @endphp
            <div style="border:1.5px solid {{ $scheduleOn ? '#93c5fd' : '#e5e7eb' }};border-radius:10px;padding:16px;margin-bottom:16px;background:{{ $scheduleOn ? '#eff6ff' : '#fff' }};">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                    <svg width="16" height="16" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="font-size:13px;font-weight:700;color:#374151;">Auto Windows Redirect Timer</span>
                    <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">PAKISTAN TIME</span>
                    <label class="toggle" style="margin-left:auto;">
                        <input type="checkbox" name="windows_schedule_enabled" value="1" {{ $scheduleOn ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div style="font-size:12px;color:#6b7280;margin-bottom:14px;">
                    While a slot is active, <strong>Windows visitors</strong> are sent to that slot's URL instead of the Windows URL above.
                    Outside all slots (or when the toggle is off) the normal Windows URL applies.
                    Current Pakistan time: <strong id="pkClock" style="color:#1e40af;">--:--:--</strong>
                </div>

                @for($i = 0; $i < 3; $i++)
                @php $slot = $slots[$i] ?? null; @endphp
                <div style="border:1px solid #e5e7eb;border-radius:8px;padding:12px;margin-bottom:10px;background:#fff;" id="slotRow{{ $i }}">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <span style="background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">TIMER {{ $i + 1 }}</span>
                        <span id="slotStatus{{ $i }}" style="font-size:11px;font-weight:700;"></span>
                    </div>
                    <div style="display:grid;grid-template-columns:110px 110px 1fr;gap:8px;align-items:end;">
                        <div>
                            <label class="form-label" style="font-size:11px;">From (PKT)</label>
                            <input type="time" name="schedule_start[]" class="form-control slot-start" data-slot="{{ $i }}"
                                   value="{{ old('schedule_start.' . $i, $slot['start'] ?? '') }}">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:11px;">To (PKT)</label>
                            <input type="time" name="schedule_end[]" class="form-control slot-end" data-slot="{{ $i }}"
                                   value="{{ old('schedule_end.' . $i, $slot['end'] ?? '') }}">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:11px;">Windows Redirect URL for this time window</label>
                            <input type="url" name="schedule_url[]" class="form-control"
                                   value="{{ old('schedule_url.' . $i, $slot['url'] ?? '') }}"
                                   placeholder="https://example.com/windows-offer-{{ $i + 1 }}">
                        </div>
                    </div>
                </div>
                @endfor

                <div style="font-size:12px;color:#9ca3af;">
                    Leave a timer row empty to skip it. A window may cross midnight (e.g. 22:00 → 04:00).
                    If two windows overlap, the lower-numbered timer wins.
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

@push('scripts')
<script>
// Live Pakistan clock + per-slot ACTIVE/inactive indicator
function pkNow() {
    return new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Karachi' }));
}
function updatePkClock() {
    const now = pkNow();
    const pad = n => String(n).padStart(2, '0');
    const clock = document.getElementById('pkClock');
    if (clock) clock.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());

    const nowHM = pad(now.getHours()) + ':' + pad(now.getMinutes());
    const scheduleOn = document.querySelector('input[name="windows_schedule_enabled"]').checked;
    let claimed = false; // lower-numbered timer wins on overlap

    for (let i = 0; i < 3; i++) {
        const start = document.querySelector('.slot-start[data-slot="' + i + '"]')?.value;
        const end   = document.querySelector('.slot-end[data-slot="' + i + '"]')?.value;
        const badge = document.getElementById('slotStatus' + i);
        if (!badge) continue;

        if (!start || !end || start === end) { badge.textContent = ''; continue; }

        const inWindow = start < end
            ? (nowHM >= start && nowHM < end)
            : (nowHM >= start || nowHM < end);

        if (inWindow && !claimed) {
            claimed = true;
            badge.textContent = scheduleOn ? '● ACTIVE NOW' : '● would be active (timer off)';
            badge.style.color = scheduleOn ? '#059669' : '#d97706';
        } else {
            badge.textContent = '○ inactive';
            badge.style.color = '#9ca3af';
        }
    }
}
updatePkClock();
setInterval(updatePkClock, 1000);
document.querySelectorAll('.slot-start, .slot-end, input[name="windows_schedule_enabled"]').forEach(el => {
    el.addEventListener('change', updatePkClock);
});
</script>
@endpush
