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
                <div style="font-size:12px;color:#6b7280;margin-bottom:10px;">
                    While a slot is active, <strong>Windows visitors</strong> are sent to that slot's URL instead of the Windows URL above.
                    Outside all slots (or when the toggle is off) the normal Windows URL applies.
                    Current Pakistan time (your browser): <strong id="pkClock" style="color:#1e40af;">--:--:--</strong>
                </div>

                {{-- Live countdown: shows time remaining in the currently-active window --}}
                <div id="activeCountdown" style="display:none;align-items:center;gap:10px;background:#ecfdf5;border:1.5px solid #6ee7b7;border-radius:8px;padding:10px 14px;margin-bottom:14px;">
                    <svg width="18" height="18" fill="none" stroke="#059669" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <div style="font-size:12px;color:#065f46;font-weight:600;"><span id="countdownLabel">Timer 1</span> is active — redirecting Windows visitors now</div>
                        <div style="font-size:13px;color:#047857;">Time remaining in this period: <strong id="countdownValue" style="font-size:15px;font-family:monospace;">--</strong></div>
                    </div>
                </div>

                {{-- SERVER-SIDE TRUTH: this reflects SAVED data + the server clock, which is what
                     actually decides the redirect. If this disagrees with the badges above, the
                     schedule isn't saved yet, or the server clock differs from your PC. --}}
                @php
                    $serverPk       = now(\App\Models\TrackingLink::SCHEDULE_TIMEZONE);
                    $serverWinUrl   = $trackingLink->resolveWindowsUrl();
                    $slotIsWinning  = $serverWinUrl && $serverWinUrl !== $trackingLink->url_windows;
                @endphp
                <div style="background:#0f172a;border-radius:8px;padding:12px 14px;margin-bottom:14px;font-size:12px;color:#e2e8f0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                        <span style="background:#1e293b;color:#38bdf8;padding:2px 8px;border-radius:5px;font-weight:700;font-size:11px;">SERVER CHECK</span>
                        <span style="color:#94a3b8;">Reflects <strong style="color:#e2e8f0;">saved</strong> data &amp; the server's clock — this is what a real visitor gets.</span>
                    </div>
                    <div style="margin-bottom:3px;">Server Pakistan time: <strong style="color:#facc15;">{{ $serverPk->format('H:i:s') }}</strong> ({{ $serverPk->format('D, M d') }})</div>
                    <div style="margin-bottom:3px;">Timer saved as: <strong style="color:{{ $scheduleOn ? '#4ade80' : '#f87171' }};">{{ $scheduleOn ? 'ENABLED' : 'DISABLED' }}</strong> · {{ count($slots) }} saved slot(s)</div>
                    <div style="word-break:break-all;">A Windows visitor right now gets:
                        <strong style="color:{{ $slotIsWinning ? '#4ade80' : '#fbbf24' }};">{{ $serverWinUrl ?: '(default URL)' }}</strong>
                        @if($slotIsWinning)<span style="color:#4ade80;">← timer slot is active ✓</span>
                        @else<span style="color:#94a3b8;">← falling back to normal Windows URL (no slot active)</span>@endif
                    </div>
                </div>

                @for($i = 0; $i < 3; $i++)
                @php $slot = $slots[$i] ?? null; @endphp
                <div style="border:1px solid #e5e7eb;border-radius:8px;padding:12px;margin-bottom:10px;background:#fff;" id="slotRow{{ $i }}">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <span style="background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;">TIMER {{ $i + 1 }}</span>
                        @if(!empty($slot['note']))
                        <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;">{{ $slot['note'] }}</span>
                        @endif
                        <span id="slotStatus{{ $i }}" style="font-size:11px;font-weight:700;"></span>
                    </div>
                    <div style="display:grid;grid-template-columns:110px 110px 1fr;gap:8px;align-items:end;margin-bottom:8px;">
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
                    <div>
                        <label class="form-label" style="font-size:11px;">Note <span style="font-weight:400;color:#9ca3af;">(optional — remember which advertiser / campaign / ID this URL is for)</span></label>
                        <input type="text" name="schedule_note[]" class="form-control" maxlength="150"
                               value="{{ old('schedule_note.' . $i, $slot['note'] ?? '') }}"
                               placeholder="e.g. Advertiser #12 — fastt.gg night campaign">
                    </div>
                </div>
                @endfor

                <div style="font-size:12px;color:#9ca3af;">
                    Leave a timer row empty to skip it. A window may cross midnight (e.g. 22:00 → 04:00).
                    If two windows overlap, the lower-numbered timer wins.
                </div>
            </div>

            {{-- Windows redirect performance — how many Windows clicks each destination received --}}
            <div style="border:1.5px solid #e5e7eb;border-radius:10px;padding:16px;margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                    <svg width="16" height="16" fill="none" stroke="#059669" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span style="font-size:13px;font-weight:700;color:#374151;">Windows Redirect Performance</span>
                    <div style="margin-left:auto;display:flex;gap:4px;flex-wrap:wrap;">
                        @foreach(['today'=>'Today','7d'=>'7 Days','30d'=>'30 Days','all'=>'All'] as $val => $lbl)
                        <a href="{{ route('admin.tracking.edit', ['trackingLink' => $trackingLink, 'stats' => $val]) }}#winstats"
                           style="padding:3px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;{{ $period === $val ? 'background:#01BF63;color:#fff;' : 'background:#f3f4f6;color:#6b7280;' }}">{{ $lbl }}</a>
                        @endforeach
                    </div>
                </div>
                <div id="winstats" style="font-size:12px;color:#6b7280;margin-bottom:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span>{{ $statsLabel }} · <strong>{{ number_format($windowsTotalClicks) }}</strong> total Windows click(s) on this link</span>
                    <a href="{{ route('admin.tracking.windows-history', $trackingLink) }}" style="color:#3b82f6;font-weight:600;text-decoration:none;">View 30-day history →</a>
                </div>
                <div style="font-size:11px;color:#9ca3af;margin-bottom:12px;">
                    <strong>This run</strong> counts only the current window occurrence — it resets to 0 each time a slot restarts. <strong>{{ $statsLabel }}</strong> is the cumulative total for the selected timeframe.
                </div>

                @php
                    // Sum how many Windows clicks landed on any configured timer-slot URL
                    $slotCodes = collect($slots)->pluck('url')->filter()->unique();
                @endphp

                {{-- Per timer slot --}}
                @foreach($slots as $i => $slot)
                    @php
                        $stat    = $windowsRedirectStats->get($slot['url'] ?? '__none__');
                        $total   = (int) ($stat->total ?? 0);
                        $counted = (int) ($stat->counted ?? 0);
                        $cur     = $slotCurrentCounts[$i] ?? ['count'=>0,'active'=>false];
                    @endphp
                    <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid {{ $cur['active'] ? '#6ee7b7' : '#f3f4f6' }};border-radius:8px;margin-bottom:8px;background:{{ $cur['active'] ? '#ecfdf5' : '#fafafa' }};">
                        <span style="background:#ede9fe;color:#7c3aed;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;flex-shrink:0;">TIMER {{ $i + 1 }}</span>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:12px;color:#374151;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $slot['url'] ?? '—' }}</div>
                            <div style="font-size:11px;color:#9ca3af;">{{ $slot['start'] ?? '?' }}–{{ $slot['end'] ?? '?' }} PKT{{ !empty($slot['note']) ? ' · '.$slot['note'] : '' }}</div>
                        </div>
                        {{-- Current activation (resets each time the window restarts) --}}
                        <div style="text-align:right;flex-shrink:0;padding-right:12px;border-right:1px solid #e5e7eb;">
                            <div style="font-size:18px;font-weight:900;color:{{ $cur['active'] ? '#059669' : '#6b7280' }};line-height:1;">{{ number_format($cur['count']) }}</div>
                            <div style="font-size:10px;color:{{ $cur['active'] ? '#059669' : '#9ca3af' }};">{{ $cur['active'] ? '● this run' : 'last run' }}</div>
                        </div>
                        {{-- Period total (from the timeframe buttons above) --}}
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-size:18px;font-weight:900;color:#374151;line-height:1;">{{ number_format($total) }}</div>
                            <div style="font-size:10px;color:#9ca3af;">{{ number_format($counted) }} counted · {{ $statsLabel }}</div>
                        </div>
                    </div>
                @endforeach

                {{-- Default Windows URL (when no slot active / timer off) --}}
                @php
                    $defStat  = $windowsRedirectStats->get($trackingLink->url_windows ?: '__none__');
                    $defTotal = (int) ($defStat->total ?? 0);
                    $defCount = (int) ($defStat->counted ?? 0);
                @endphp
                @if($trackingLink->url_windows)
                <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #f3f4f6;border-radius:8px;background:#fff;">
                    <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;flex-shrink:0;">DEFAULT</span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;color:#374151;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $trackingLink->url_windows }}</div>
                        <div style="font-size:11px;color:#9ca3af;">Normal Windows URL — used when no slot is active</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-size:18px;font-weight:900;color:#059669;line-height:1;">{{ number_format($defTotal) }}</div>
                        <div style="font-size:10px;color:#9ca3af;">{{ number_format($defCount) }} counted</div>
                    </div>
                </div>
                @endif

                <div style="font-size:11px;color:#9ca3af;margin-top:10px;">
                    Counts are matched by the exact destination URL a visitor was redirected to. If you change a slot's URL, past clicks stay attributed to the old URL.
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
// Seconds left until a window's end time, accounting for overnight (past-midnight) windows
function secondsUntilEnd(now, start, end) {
    const [eh, em] = end.split(':').map(Number);
    const endDate = new Date(now);
    endDate.setHours(eh, em, 0, 0);
    // If the end boundary is not in the future, it belongs to tomorrow (overnight window)
    if (endDate <= now) {
        endDate.setDate(endDate.getDate() + 1);
    }
    return Math.max(0, Math.floor((endDate - now) / 1000));
}

// Seconds until a window's next start (today if still ahead, otherwise tomorrow)
function secondsUntilStart(now, start) {
    const [sh, sm] = start.split(':').map(Number);
    const startDate = new Date(now);
    startDate.setHours(sh, sm, 0, 0);
    if (startDate <= now) {
        startDate.setDate(startDate.getDate() + 1);
    }
    return Math.max(0, Math.floor((startDate - now) / 1000));
}

function fmtDuration(totalSec) {
    const pad = n => String(n).padStart(2, '0');
    const h = Math.floor(totalSec / 3600);
    const m = Math.floor((totalSec % 3600) / 60);
    const s = totalSec % 60;
    return (h > 0 ? h + 'h ' : '') + pad(m) + 'm ' + pad(s) + 's';
}

function updatePkClock() {
    const now = pkNow();
    const pad = n => String(n).padStart(2, '0');
    const clock = document.getElementById('pkClock');
    if (clock) clock.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());

    const nowHM = pad(now.getHours()) + ':' + pad(now.getMinutes());
    const scheduleOn = document.querySelector('input[name="windows_schedule_enabled"]').checked;
    let claimed = false; // lower-numbered timer wins on overlap
    let activeIdx = -1, activeEnd = null;

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
            activeIdx = i; activeEnd = end;
            badge.textContent = scheduleOn ? '● ACTIVE NOW' : '● would be active (timer off)';
            badge.style.color = scheduleOn ? '#059669' : '#d97706';
        } else if (inWindow) {
            // In window but a lower-numbered slot already claimed it (overlap loser)
            badge.textContent = '○ overlapped by Timer ' + (activeIdx + 1);
            badge.style.color = '#9ca3af';
        } else {
            // Not active yet — show live countdown until it activates
            badge.textContent = '○ starts in ' + fmtDuration(secondsUntilStart(now, start));
            badge.style.color = '#d97706';
        }
    }

    // Countdown banner — only when a slot is active AND the timer is enabled
    const banner = document.getElementById('activeCountdown');
    if (banner) {
        if (claimed && scheduleOn && activeEnd) {
            const start = document.querySelector('.slot-start[data-slot="' + activeIdx + '"]')?.value;
            document.getElementById('countdownLabel').textContent = 'Timer ' + (activeIdx + 1);
            document.getElementById('countdownValue').textContent =
                fmtDuration(secondsUntilEnd(now, start, activeEnd)) + '  (ends ' + activeEnd + ' PKT)';
            banner.style.display = 'flex';
        } else {
            banner.style.display = 'none';
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
