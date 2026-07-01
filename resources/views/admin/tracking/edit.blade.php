@extends('layouts.admin')
@section('title', 'Edit Tracking Link')
@section('page-title', 'Edit Tracking Link')

@section('content')
@php
    $slots      = $trackingLink->windows_schedules ?? [];
    $scheduleOn = (bool) $trackingLink->windows_schedule_enabled;
    $serverPk       = now(\App\Models\TrackingLink::SCHEDULE_TIMEZONE);
    $serverWinUrl   = $trackingLink->resolveWindowsUrl();
    $slotIsWinning  = $serverWinUrl && $serverWinUrl !== $trackingLink->url_windows;
@endphp

<style>
    .tl-wrap { max-width: 940px; }
    .tl-section { border:1px solid #e5e7eb; border-radius:14px; padding:20px; margin-bottom:18px; background:#fff; }
    .tl-sec-head { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
    .tl-sec-ico { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .tl-sec-title { font-size:14px; font-weight:800; color:#111827; }
    .tl-sec-sub { font-size:12px; color:#9ca3af; margin-top:1px; }
    .tl-label { display:block; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.03em; margin-bottom:5px; }
    .tl-chip { padding:2px 8px; border-radius:6px; font-size:11px; font-weight:700; }
    .tl-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .slot-card { border:1.5px solid #e5e7eb; border-radius:12px; padding:14px; margin-bottom:12px; background:#fbfbfd; transition:border-color .15s, background .15s; }
    .slot-card.is-active { border-color:#6ee7b7; background:#f0fdf9; }
    .slot-card.is-disabled { opacity:.62; }
    @media(max-width:640px){ .tl-grid2 { grid-template-columns:1fr; } }
</style>

<div class="tl-wrap">

    {{-- ══════════ HEADER ══════════ --}}
    <div class="tl-section" style="background:linear-gradient(135deg,#0f172a,#1e293b); border:none;">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div style="width:44px;height:44px;border-radius:11px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#38bdf8" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:16px;font-weight:800;color:#fff;">{{ $trackingLink->name ?: 'Unnamed Link' }}</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">
                    Code <code style="background:rgba(255,255,255,.12);color:#e2e8f0;padding:2px 7px;border-radius:5px;">{{ $trackingLink->unique_code }}</code>
                    &nbsp;·&nbsp; Publisher <strong style="color:#e2e8f0;">{{ $trackingLink->user->name }}</strong>
                </div>
            </div>
            <a href="{{ route('admin.tracking.windows-history', $trackingLink) }}"
               style="background:rgba(255,255,255,.1);color:#e2e8f0;border:1px solid rgba(255,255,255,.15);border-radius:9px;padding:8px 14px;font-size:12px;font-weight:700;text-decoration:none;">
                📊 30-Day History
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.tracking.update', $trackingLink) }}">
        @csrf
        @method('PUT')

        {{-- ══════════ BASICS ══════════ --}}
        <div class="tl-section">
            <div class="tl-sec-head">
                <div class="tl-sec-ico" style="background:#eff6ff;"><svg width="17" height="17" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                <div><div class="tl-sec-title">Link Settings</div><div class="tl-sec-sub">Domain, name and the default destination</div></div>
            </div>

            <div class="tl-grid2" style="margin-bottom:14px;">
                <div>
                    <label class="tl-label">Tracking Domain</label>
                    <select name="tracking_domain_id" class="form-control form-select">
                        <option value="">Default (installsbank.com)</option>
                        @foreach($domains as $d)
                            <option value="{{ $d->id }}" {{ old('tracking_domain_id', $trackingLink->tracking_domain_id) == $d->id ? 'selected' : '' }}>
                                {{ $d->domain }}{{ $d->label ? ' — ' . $d->label : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="tl-label">Link Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $trackingLink->name) }}" placeholder="e.g. Main Campaign">
                </div>
            </div>

            <div>
                <label class="tl-label">Default Destination URL <span style="color:#ef4444;">*</span></label>
                <input type="url" name="original_url" class="form-control" value="{{ old('original_url', $trackingLink->original_url) }}" required placeholder="https://example.com/download">
                <div style="font-size:12px;color:#9ca3af;margin-top:5px;">Used when no device-specific URL is set, or for devices not listed below. <a href="{{ route('admin.tracking-domains.index') }}" style="color:var(--primary);">Manage domains →</a></div>
            </div>
        </div>

        {{-- ══════════ DEVICE URLS ══════════ --}}
        <div class="tl-section">
            <div class="tl-sec-head">
                <div class="tl-sec-ico" style="background:#f5f3ff;"><svg width="17" height="17" fill="none" stroke="#7c3aed" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                <div><div class="tl-sec-title">Device-Specific URLs</div><div class="tl-sec-sub">Optional — overrides the default for that device type</div></div>
            </div>

            <div class="tl-grid2">
                <div>
                    <label class="tl-label"><span class="tl-chip" style="background:#dbeafe;color:#1e40af;">WINDOWS</span></label>
                    <input type="url" name="url_windows" class="form-control" value="{{ old('url_windows', $trackingLink->url_windows) }}" placeholder="https://example.com/windows">
                </div>
                <div>
                    <label class="tl-label"><span class="tl-chip" style="background:#d1fae5;color:#065f46;">ANDROID</span></label>
                    <input type="url" name="url_android" class="form-control" value="{{ old('url_android', $trackingLink->url_android) }}" placeholder="https://play.google.com/...">
                </div>
                <div>
                    <label class="tl-label"><span class="tl-chip" style="background:#f3f4f6;color:#374151;">MAC / iOS</span></label>
                    <input type="url" name="url_mac" class="form-control" value="{{ old('url_mac', $trackingLink->url_mac) }}" placeholder="https://apps.apple.com/...">
                </div>
                <div>
                    <label class="tl-label"><span class="tl-chip" style="background:#fef3c7;color:#92400e;">OTHER</span></label>
                    <input type="url" name="url_other" class="form-control" value="{{ old('url_other', $trackingLink->url_other) }}" placeholder="https://example.com/download">
                </div>
            </div>
            <div style="font-size:12px;color:#9ca3af;margin-top:8px;">"Other" covers Linux, unknown OS, bots, etc. If blank, the default URL is used.</div>
        </div>

        {{-- ══════════ AUTO WINDOWS REDIRECT TIMER ══════════ --}}
        <div class="tl-section" style="{{ $scheduleOn ? 'border-color:#93c5fd;box-shadow:0 0 0 3px rgba(59,130,246,.06);' : '' }}">
            <div class="tl-sec-head" style="margin-bottom:12px;">
                <div class="tl-sec-ico" style="background:#eff6ff;"><svg width="17" height="17" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div style="flex:1;">
                    <div class="tl-sec-title">Auto Windows Redirect Timer <span class="tl-chip" style="background:#dbeafe;color:#1e40af;margin-left:4px;">PAKISTAN TIME</span></div>
                    <div class="tl-sec-sub">Rotate the Windows destination automatically by time of day</div>
                </div>
                <label class="toggle" title="Master on/off for all timers">
                    <input type="checkbox" name="windows_schedule_enabled" value="1" {{ $scheduleOn ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div style="font-size:12px;color:#6b7280;margin-bottom:12px;">
                While a slot is active, <strong>Windows visitors</strong> go to that slot's URL. Outside all slots (or master off) the normal Windows URL applies.
                Your browser's Pakistan time: <strong id="pkClock" style="color:#1e40af;">--:--:--</strong>
            </div>

            {{-- Live countdown for the active window --}}
            <div id="activeCountdown" style="display:none;align-items:center;gap:10px;background:#ecfdf5;border:1.5px solid #6ee7b7;border-radius:9px;padding:10px 14px;margin-bottom:12px;">
                <svg width="18" height="18" fill="none" stroke="#059669" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <div style="font-size:12px;color:#065f46;font-weight:600;"><span id="countdownLabel">Timer 1</span> is active — redirecting Windows visitors now</div>
                    <div style="font-size:13px;color:#047857;">Time remaining: <strong id="countdownValue" style="font-size:15px;font-family:monospace;">--</strong></div>
                </div>
            </div>

            {{-- SERVER CHECK — reflects saved data + server clock --}}
            <div style="background:#0f172a;border-radius:10px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:#e2e8f0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                    <span style="background:#1e293b;color:#38bdf8;padding:2px 8px;border-radius:5px;font-weight:700;font-size:11px;">SERVER CHECK</span>
                    <span style="color:#94a3b8;">What a real visitor gets right now (saved data &amp; server clock)</span>
                </div>
                <div style="margin-bottom:3px;">Server Pakistan time: <strong style="color:#facc15;">{{ $serverPk->format('H:i:s') }}</strong> ({{ $serverPk->format('D, M d') }})</div>
                <div style="margin-bottom:3px;">Master timer: <strong style="color:{{ $scheduleOn ? '#4ade80' : '#f87171' }};">{{ $scheduleOn ? 'ENABLED' : 'DISABLED' }}</strong> · {{ count($slots) }} saved slot(s)</div>
                <div style="word-break:break-all;">Windows visitor →
                    <strong style="color:{{ $slotIsWinning ? '#4ade80' : '#fbbf24' }};">{{ $serverWinUrl ?: '(default URL)' }}</strong>
                    @if($slotIsWinning)<span style="color:#4ade80;"> ← timer slot active ✓</span>
                    @else<span style="color:#94a3b8;"> ← default Windows URL (no slot active)</span>@endif
                </div>
            </div>

            {{-- SLOT CARDS --}}
            @for($i = 0; $i < 3; $i++)
            @php
                $slot        = $slots[$i] ?? null;
                $slotEnabled = old('schedule_enabled.'.$i, ($slot['enabled'] ?? true)) ? true : false;
                $cur         = $slotCurrentCounts[$i] ?? ['count'=>0,'active'=>false];
                $cap         = $slot['cap'] ?? null;
                $capReached  = $cap && $cur['count'] >= $cap;
                $capPct      = $cap ? min(100, round($cur['count'] / $cap * 100)) : 0;
            @endphp
            <div class="slot-card {{ $cur['active'] && $slotEnabled ? 'is-active' : '' }} {{ !$slotEnabled ? 'is-disabled' : '' }}" id="slotRow{{ $i }}" data-slot-card="{{ $i }}">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
                    <span style="background:#ede9fe;color:#7c3aed;padding:3px 9px;border-radius:6px;font-size:11px;font-weight:800;">TIMER {{ $i + 1 }}</span>
                    <span id="slotStatus{{ $i }}" style="font-size:11px;font-weight:700;"></span>
                    <div style="margin-left:auto;display:flex;align-items:center;gap:7px;">
                        <span style="font-size:11px;color:#6b7280;font-weight:600;">Slot</span>
                        <input type="hidden" name="schedule_enabled[{{ $i }}]" value="0">
                        <label class="toggle" title="Enable/disable this slot only">
                            <input type="checkbox" class="slot-enabled" data-slot="{{ $i }}" name="schedule_enabled[{{ $i }}]" value="1" {{ $slotEnabled ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:10px;">
                    <div>
                        <label class="tl-label">From (PKT)</label>
                        <input type="time" name="schedule_start[{{ $i }}]" class="form-control slot-start" data-slot="{{ $i }}"
                               value="{{ old('schedule_start.' . $i, $slot['start'] ?? '') }}">
                    </div>
                    <div>
                        <label class="tl-label">To (PKT)</label>
                        <input type="time" name="schedule_end[{{ $i }}]" class="form-control slot-end" data-slot="{{ $i }}"
                               value="{{ old('schedule_end.' . $i, $slot['end'] ?? '') }}">
                    </div>
                    <div>
                        <label class="tl-label">Click Cap</label>
                        <input type="number" name="schedule_cap[{{ $i }}]" class="form-control" min="1" step="1"
                               value="{{ old('schedule_cap.' . $i, $slot['cap'] ?? '') }}" placeholder="∞ unlimited">
                    </div>
                </div>

                <div style="margin-bottom:10px;">
                    <label class="tl-label">Windows Redirect URL for this window</label>
                    <input type="url" name="schedule_url[{{ $i }}]" class="form-control"
                           value="{{ old('schedule_url.' . $i, $slot['url'] ?? '') }}"
                           placeholder="https://example.com/windows-offer-{{ $i + 1 }}">
                </div>

                <div style="margin-bottom:10px;">
                    <label class="tl-label">Note <span style="font-weight:500;text-transform:none;color:#9ca3af;">— which advertiser / campaign / ID</span></label>
                    <input type="text" name="schedule_note[{{ $i }}]" class="form-control" maxlength="150"
                           value="{{ old('schedule_note.' . $i, $slot['note'] ?? '') }}"
                           placeholder="e.g. Advertiser #12 — fastt.gg night campaign">
                </div>

                {{-- Live footer: this-run count + cap progress --}}
                @if($slot && !empty($slot['url']))
                <div style="display:flex;align-items:center;gap:12px;padding-top:10px;border-top:1px dashed #e5e7eb;flex-wrap:wrap;">
                    <div style="display:flex;align-items:baseline;gap:6px;">
                        <span style="font-size:20px;font-weight:900;color:{{ $cur['active'] && $slotEnabled ? '#059669' : '#6b7280' }};">{{ number_format($cur['count']) }}</span>
                        <span style="font-size:11px;color:#9ca3af;">clicks {{ $cur['active'] ? 'this run' : 'last run' }}</span>
                    </div>
                    @if($cap)
                        <div style="flex:1;min-width:140px;">
                            <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:3px;">
                                <span style="color:#6b7280;font-weight:600;">Cap {{ number_format($cur['count']) }} / {{ number_format($cap) }}</span>
                                @if($capReached)<span style="color:#ef4444;font-weight:700;">CAP REACHED → default</span>@endif
                            </div>
                            <div style="height:6px;background:#f3f4f6;border-radius:4px;overflow:hidden;">
                                <div style="height:100%;width:{{ $capPct }}%;background:{{ $capReached ? '#ef4444' : '#01BF63' }};border-radius:4px;"></div>
                            </div>
                        </div>
                    @else
                        <span style="font-size:11px;color:#9ca3af;">No cap — runs the whole window</span>
                    @endif
                </div>
                @endif
            </div>
            @endfor

            <div style="font-size:12px;color:#9ca3af;">
                Leave a timer empty to skip it. Windows may cross midnight (22:00 → 04:00). On overlap the lower-numbered timer wins.
                A <strong>click cap</strong> stops that slot once it hits the limit within its window — traffic then falls to the next slot or the default URL, and the cap resets when the window restarts.
            </div>
        </div>

        {{-- ══════════ PERFORMANCE (timeframe totals) ══════════ --}}
        <div class="tl-section">
            <div class="tl-sec-head" style="margin-bottom:12px;">
                <div class="tl-sec-ico" style="background:#f0fdf4;"><svg width="17" height="17" fill="none" stroke="#059669" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                <div style="flex:1;"><div class="tl-sec-title">Windows Redirect Performance</div><div class="tl-sec-sub">{{ $statsLabel }} · {{ number_format($windowsTotalClicks) }} total Windows clicks</div></div>
                <div id="winstats" style="display:flex;gap:4px;flex-wrap:wrap;">
                    @foreach(['today'=>'Today','7d'=>'7 Days','30d'=>'30 Days','all'=>'All'] as $val => $lbl)
                    <a href="{{ route('admin.tracking.edit', ['trackingLink' => $trackingLink, 'stats' => $val]) }}#winstats"
                       style="padding:4px 11px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;{{ $period === $val ? 'background:#01BF63;color:#fff;' : 'background:#f3f4f6;color:#6b7280;' }}">{{ $lbl }}</a>
                    @endforeach
                </div>
            </div>

            @foreach($slots as $i => $slot)
                @php
                    $stat    = $windowsRedirectStats->get($slot['url'] ?? '__none__');
                    $total   = (int) ($stat->total ?? 0);
                    $counted = (int) ($stat->counted ?? 0);
                @endphp
                <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #f3f4f6;border-radius:9px;margin-bottom:7px;background:#fafafa;">
                    <span style="background:#ede9fe;color:#7c3aed;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;flex-shrink:0;">TIMER {{ $i + 1 }}</span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;color:#374151;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $slot['url'] ?? '—' }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $slot['start'] ?? '?' }}–{{ $slot['end'] ?? '?' }} PKT{{ !empty($slot['note']) ? ' · '.$slot['note'] : '' }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-size:17px;font-weight:900;color:#374151;line-height:1;">{{ number_format($total) }}</div>
                        <div style="font-size:10px;color:#9ca3af;">{{ number_format($counted) }} counted</div>
                    </div>
                </div>
            @endforeach

            @php
                $defStat  = $windowsRedirectStats->get($trackingLink->url_windows ?: '__none__');
                $defTotal = (int) ($defStat->total ?? 0);
                $defCount = (int) ($defStat->counted ?? 0);
            @endphp
            @if($trackingLink->url_windows)
            <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #f3f4f6;border-radius:9px;background:#fff;">
                <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;flex-shrink:0;">DEFAULT</span>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:12px;color:#374151;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $trackingLink->url_windows }}</div>
                    <div style="font-size:11px;color:#9ca3af;">Normal Windows URL — used when no slot is active</div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:17px;font-weight:900;color:#374151;line-height:1;">{{ number_format($defTotal) }}</div>
                    <div style="font-size:10px;color:#9ca3af;">{{ number_format($defCount) }} counted</div>
                </div>
            </div>
            @endif

            <div style="font-size:11px;color:#9ca3af;margin-top:10px;">
                Matched by exact destination URL. Change a slot's URL and past clicks stay attributed to the old one.
                <a href="{{ route('admin.tracking.windows-history', $trackingLink) }}" style="color:#3b82f6;font-weight:600;text-decoration:none;">View 30-day history →</a>
            </div>
        </div>

        {{-- ══════════ ACTIONS ══════════ --}}
        <div style="display:flex;gap:10px;align-items:center;position:sticky;bottom:0;background:linear-gradient(180deg,rgba(249,250,251,0),#f9fafb 40%);padding:14px 0;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.tracking.index') }}" class="btn btn-ghost">Cancel</a>
            <span style="margin-left:auto;font-size:12px;color:#9ca3af;">Changes take effect immediately.</span>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function pkNow() {
    return new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Karachi' }));
}
function secondsUntilEnd(now, start, end) {
    const [eh, em] = end.split(':').map(Number);
    const endDate = new Date(now);
    endDate.setHours(eh, em, 0, 0);
    if (endDate <= now) endDate.setDate(endDate.getDate() + 1);
    return Math.max(0, Math.floor((endDate - now) / 1000));
}
function secondsUntilStart(now, start) {
    const [sh, sm] = start.split(':').map(Number);
    const startDate = new Date(now);
    startDate.setHours(sh, sm, 0, 0);
    if (startDate <= now) startDate.setDate(startDate.getDate() + 1);
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
    const masterOn = document.querySelector('input[name="windows_schedule_enabled"]').checked;
    let claimed = false, activeIdx = -1, activeEnd = null;

    for (let i = 0; i < 3; i++) {
        const start   = document.querySelector('.slot-start[data-slot="' + i + '"]')?.value;
        const end     = document.querySelector('.slot-end[data-slot="' + i + '"]')?.value;
        const enabled = document.querySelector('.slot-enabled[data-slot="' + i + '"]')?.checked;
        const badge   = document.getElementById('slotStatus' + i);
        const card    = document.querySelector('[data-slot-card="' + i + '"]');
        if (!badge) continue;

        if (card) card.classList.toggle('is-disabled', !enabled);

        if (!start || !end || start === end) { badge.textContent = ''; if (card) card.classList.remove('is-active'); continue; }

        if (!enabled) {
            badge.textContent = '○ slot disabled';
            badge.style.color = '#9ca3af';
            if (card) card.classList.remove('is-active');
            continue;
        }

        const inWindow = start < end
            ? (nowHM >= start && nowHM < end)
            : (nowHM >= start || nowHM < end);

        if (inWindow && !claimed) {
            claimed = true; activeIdx = i; activeEnd = end;
            badge.textContent = masterOn ? '● ACTIVE NOW' : '● would be active (master off)';
            badge.style.color = masterOn ? '#059669' : '#d97706';
            if (card) card.classList.toggle('is-active', masterOn);
        } else if (inWindow) {
            badge.textContent = '○ overlapped by Timer ' + (activeIdx + 1);
            badge.style.color = '#9ca3af';
            if (card) card.classList.remove('is-active');
        } else {
            badge.textContent = '○ starts in ' + fmtDuration(secondsUntilStart(now, start));
            badge.style.color = '#d97706';
            if (card) card.classList.remove('is-active');
        }
    }

    const banner = document.getElementById('activeCountdown');
    if (banner) {
        if (claimed && masterOn && activeEnd) {
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
document.querySelectorAll('.slot-start, .slot-end, .slot-enabled, input[name="windows_schedule_enabled"]').forEach(el => {
    el.addEventListener('change', updatePkClock);
});
</script>
@endpush
