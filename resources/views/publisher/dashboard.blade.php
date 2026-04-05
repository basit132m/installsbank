@extends('layouts.publisher')
@section('title', 'Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
@if(session('registered'))
<div style="background:#e6faf2;border:1px solid #a7f3d0;border-radius:12px;padding:20px 24px;margin-bottom:24px;">
    <div style="font-size:16px;font-weight:700;color:#065f46;margin-bottom:8px;">Account Created Successfully!</div>
    <p style="font-size:14px;color:#065f46;margin-bottom:0;">Welcome to Installs Bank. Your application has been submitted and is now under review.</p>
</div>
@endif

@if(auth()->user()->status === 'pending')
<div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:24px;margin-bottom:24px;">
    <div style="display:flex;gap:14px;align-items:flex-start;">
        <div style="width:44px;height:44px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="22" height="22" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div style="flex:1;">
            <div style="font-size:16px;font-weight:700;color:#92400e;margin-bottom:6px;">Account Pending Approval</div>
            <p style="font-size:14px;color:#78350f;line-height:1.6;margin-bottom:12px;">
                Your request is under review and will be sorted out ASAP. Please visit us again to see the status of your request.
            </p>
            <div style="background:#fffbeb;border-radius:8px;padding:14px;font-size:13px;color:#78350f;line-height:1.8;">
                <strong>What happens next:</strong>
                <ol style="padding-left:18px;margin-top:6px;">
                    <li>Our team verifies your website meets the <strong>500+ daily visitors</strong> requirement</li>
                    <li>If approved, you'll run a <strong>48-hour test period</strong> so we can evaluate traffic quality</li>
                    <li>A custom rate contract is then offered based on your test results</li>
                    <li>Once accepted, your ad code goes live and you start earning</li>
                </ol>
            </div>
            <div style="margin-top:12px;font-size:13px;color:#92400e;">
                <strong>Please come back and check this page in a few days</strong> to see your approval status.
                You can also reach us via <a href="{{ route('publisher.support.create') }}" style="color:#01BF63;font-weight:600;">Support</a> if you have questions.
            </div>
        </div>
    </div>
</div>
@endif

<!-- Announcements -->
@if($announcements->isNotEmpty())
@foreach($announcements as $ann)
@php
    $annStyles = [
        'info'    => ['bg'=>'#eff6ff','border'=>'#93c5fd','text'=>'#1e40af','icon_bg'=>'#dbeafe','icon_color'=>'#2563eb'],
        'warning' => ['bg'=>'#fffbeb','border'=>'#fcd34d','text'=>'#92400e','icon_bg'=>'#fef3c7','icon_color'=>'#d97706'],
        'danger'  => ['bg'=>'#fff1f2','border'=>'#fca5a5','text'=>'#991b1b','icon_bg'=>'#fee2e2','icon_color'=>'#ef4444'],
        'success' => ['bg'=>'#f0fdf4','border'=>'#86efac','text'=>'#166534','icon_bg'=>'#dcfce7','icon_color'=>'#16a34a'],
    ];
    $s = $annStyles[$ann->type] ?? $annStyles['info'];
@endphp
<div style="background:{{ $s['bg'] }};border:1.5px solid {{ $s['border'] }};border-radius:14px;padding:16px 20px;margin-bottom:14px;display:flex;gap:14px;align-items:flex-start;">
    <div style="width:36px;height:36px;background:{{ $s['icon_bg'] }};border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
        @if($ann->type === 'info')
            <svg width="18" height="18" fill="none" stroke="{{ $s['icon_color'] }}" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @elseif($ann->type === 'warning' || $ann->type === 'danger')
            <svg width="18" height="18" fill="none" stroke="{{ $s['icon_color'] }}" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        @else
            <svg width="18" height="18" fill="none" stroke="{{ $s['icon_color'] }}" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @endif
    </div>
    <div style="flex:1;font-size:14px;color:{{ $s['text'] }};line-height:1.7;">{!! nl2br(e($ann->message)) !!}</div>
</div>
@endforeach
@endif

<!-- App Download Banner -->
<div style="margin-bottom:24px;background:linear-gradient(135deg,#0f172a 0%,#064e35 60%,#01BF63 100%);border-radius:16px;padding:28px 32px;display:flex;align-items:center;gap:24px;flex-wrap:wrap;overflow:hidden;position:relative;">
    <div style="position:absolute;right:-30px;top:-30px;width:180px;height:180px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>
    <div style="position:absolute;right:80px;bottom:-50px;width:130px;height:130px;background:rgba(255,255,255,0.03);border-radius:50%;"></div>
    <div style="width:64px;height:64px;background:rgba(255,255,255,0.12);border-radius:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(255,255,255,0.15);">
        <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
    </div>
    <div style="flex:1;min-width:200px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <span style="font-size:18px;font-weight:800;color:white;">Installs Bank Android App</span>
            <span style="background:#01BF63;color:white;font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;">NEW</span>
        </div>
        <p style="font-size:14px;color:rgba(255,255,255,0.75);margin:0;line-height:1.6;">Monitor your earnings, view live click stats, and manage your account on the go — right from your Android device.</p>
        <div style="display:flex;gap:16px;margin-top:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,0.6);">
                <svg width="14" height="14" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Live click counter
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,0.6);">
                <svg width="14" height="14" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Earnings dashboard
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,0.6);">
                <svg width="14" height="14" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Withdrawal requests
            </div>
        </div>
    </div>
    <a href="https://www.dropbox.com/scl/fi/ao70vpaoeij74a5sjwowd/Installs-Bank.apk?rlkey=te90jvdgavwnmxztans140qbn&st=jzo9jg56&dl=1"
       style="display:inline-flex;align-items:center;gap:10px;background:white;color:#0f172a;padding:13px 22px;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;flex-shrink:0;transition:all 0.2s;box-shadow:0 4px 16px rgba(0,0,0,0.2);"
       onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.3)'"
       onmouseout="this.style.transform='';this.style.boxShadow='0 4px 16px rgba(0,0,0,0.2)'">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="#3DDC84"><path d="M17.523 15.341a.75.75 0 00-.61-.313H14.25V3a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75v12.028H7.087a.75.75 0 00-.537 1.275l4.913 5.04a.75.75 0 001.074 0l4.913-5.04a.75.75 0 00.073-.962z"/><path d="M19.5 21H4.5a.75.75 0 000 1.5h15a.75.75 0 000-1.5z"/></svg>
        Download APK
        <span style="background:#e6faf2;color:#065f46;font-size:11px;padding:2px 7px;border-radius:6px;font-weight:600;">Android</span>
    </a>
</div>

@if($pendingContract)
<div class="contract-box mb-6">
    <h3 style="margin-bottom:8px;">📋 New Contract Offer</h3>
    <p style="font-size:14px;color:#374151;margin-bottom:16px;">
        @if($pendingContract->type === 'per_click')
            You have been offered <strong>${{ number_format($pendingContract->rate, 4) }} per 1,000 unique clicks</strong>.
        @else
            You have been offered a <strong>fixed daily rate of ${{ number_format($pendingContract->rate, 4) }}/day</strong>.
        @endif
        @if($pendingContract->test_total_clicks)
            <br><span style="font-size:13px;color:#6b7280;">Based on your 48-hour test: {{ number_format($pendingContract->test_total_clicks) }} clicks</span>
        @endif
    </p>
    <div style="display:flex;gap:10px;">
        <form method="POST" action="{{ route('publisher.contract.accept', $pendingContract) }}">
            @csrf<button class="btn btn-primary">Accept Contract</button>
        </form>
        <form method="POST" action="{{ route('publisher.contract.reject', $pendingContract) }}">
            @csrf<button class="btn btn-ghost">Decline</button>
        </form>
    </div>
</div>
@endif

<!-- Live Click Counter -->
<div style="background:linear-gradient(135deg,#01BF63,#00a354);border-radius:16px;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 80% 50%,rgba(255,255,255,0.12),transparent 60%);pointer-events:none;"></div>

    <!-- Pulse icon -->
    <div style="position:relative;flex-shrink:0;">
        <div style="width:48px;height:48px;background:rgba(255,255,255,0.18);border-radius:12px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,0.3);">
            <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <span style="position:absolute;top:-4px;right:-4px;width:12px;height:12px;background:white;border-radius:50%;border:2px solid #01BF63;animation:livePulse 2s infinite;"></span>
    </div>

    <!-- Today clicks -->
    <div style="flex-shrink:0;">
        <div style="font-size:11px;color:rgba(255,255,255,0.7);font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:2px;">Clicks Today</div>
        <div style="display:flex;align-items:baseline;gap:6px;">
            <span id="liveClickCount" style="font-size:32px;font-weight:900;color:white;line-height:1;font-variant-numeric:tabular-nums;">—</span>
            <span style="font-size:12px;color:rgba(255,255,255,0.6);">total</span>
        </div>
    </div>

    <!-- Divider -->
    <div style="width:1px;height:40px;background:rgba(255,255,255,0.25);flex-shrink:0;"></div>

    <!-- Last hour -->
    <div style="flex-shrink:0;">
        <div style="font-size:11px;color:rgba(255,255,255,0.7);font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:2px;">Last Hour</div>
        <div style="display:flex;align-items:baseline;gap:6px;">
            <span id="liveLastHour" style="font-size:24px;font-weight:800;color:white;line-height:1;font-variant-numeric:tabular-nums;">—</span>
            <span style="font-size:12px;color:rgba(255,255,255,0.6);">clicks</span>
        </div>
    </div>

    <!-- Divider -->
    <div style="width:1px;height:40px;background:rgba(255,255,255,0.25);flex-shrink:0;"></div>

    <!-- Performance badge -->
    <div id="liveBadge" style="display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);padding:8px 14px;border-radius:20px;flex-shrink:0;">
        <span class="live-dot" style="background:white;"></span>
        <span id="badgeLabel" style="font-size:13px;font-weight:700;color:white;">Loading...</span>
    </div>

    <!-- Live indicator -->
    <div style="margin-left:auto;display:flex;align-items:center;gap:6px;flex-shrink:0;">
        <span style="width:8px;height:8px;background:white;border-radius:50%;animation:livePulse 2s infinite;display:block;"></span>
        <span style="font-size:11px;color:rgba(255,255,255,0.7);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Live</span>
    </div>
</div>
<style>
@keyframes livePulse {
    0%,100%{opacity:1;transform:scale(1);}
    50%{opacity:0.4;transform:scale(1.3);}
}
@media(max-width:700px){
    .country-grid { grid-template-columns: 1fr !important; }
    .charts-row { grid-template-columns: 1fr !important; }
}
</style>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6faf2;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['clicks_today']) }}</div>
        <div class="stat-label">Clicks Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;">
            <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['clicks_this_week']) }}</div>
        <div class="stat-label">This Week</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">
            <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['clicks_this_month']) }}</div>
        <div class="stat-label">This Month</div>
    </div>
    @if($stats['earnings_today'] !== null)
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6faf2;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        </div>
        <div class="stat-value">${{ number_format($stats['earnings_today'], 4) }}</div>
        <div class="stat-label">Earnings Today</div>
    </div>
    @endif
    @if($stats['balance'] !== null)
    <div class="stat-card" style="border:2px solid #01BF63;">
        <div class="stat-icon" style="background:#e6faf2;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div class="stat-value" style="color:#01BF63;">${{ number_format($stats['balance'], 4) }}</div>
        <div class="stat-label">Available Balance</div>
    </div>
    @endif
</div>

<!-- Chart -->
<div class="card mb-6">
    <div class="card-title mb-4">Click Performance (Last 7 Days)</div>
    <div id="pubClickChart"></div>
</div>

<!-- Country Breakdown -->
@if($countryBreakdown && $countryBreakdown->count() > 0)
@php
    $sortedBreakdown = $countryBreakdown->sortByDesc('clicks');
    $totalBreakdownClicks = $countryBreakdown->sum('clicks');
    $countryNames = \App\Models\CountryRate::whereIn('country_code', $countryBreakdown->keys()->toArray())
        ->pluck('country_name', 'country_code');
@endphp
<div class="card mb-6">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div>
            <div class="card-title">Traffic by Country</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ $countryBreakdown->count() }} {{ Str::plural('country', $countryBreakdown->count()) }} · {{ number_format($totalBreakdownClicks) }} clicks today</div>
        </div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:14px;padding:16px;background:#ffffff;border-radius:12px;border:1px solid #f3f4f6;">
        @foreach($sortedBreakdown as $code => $data)
        <div style="display:flex;flex-direction:column;align-items:center;gap:5px;width:66px;">
            <img src="https://flagcdn.com/48x36/{{ strtolower($code) }}.png"
                 alt="{{ $countryNames[$code] ?? $code }}"
                 title="{{ $countryNames[$code] ?? $code }}"
                 style="width:48px;height:36px;border-radius:5px;object-fit:cover;box-shadow:0 1px 6px rgba(0,0,0,0.15);flex-shrink:0;"
                 onerror="this.style.display='none'">
            <span style="font-size:11px;font-weight:800;color:#111827;line-height:1;text-align:center;">{{ number_format($data['clicks']) }}</span>
            <span style="font-size:9px;color:#9ca3af;font-weight:600;line-height:1;">{{ strtoupper($code) }}</span>
        </div>
        @endforeach
    </div>
</div>

<!-- Country + OS Pie Charts -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="charts-row">
    <!-- Country donut -->
    <div class="card">
        <div class="card-title mb-1">Clicks by Country</div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:4px;">Today · valid clicks only</div>
        <div id="countryPieChart"></div>
    </div>
    <!-- OS donut -->
    <div class="card">
        <div class="card-title mb-1">Clicks by OS</div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:4px;">Today · valid clicks only</div>
        <div id="osPieChart"></div>
    </div>
</div>
@endif

<!-- Status Cards -->
<div class="grid-2">
    <div class="card">
        <div class="card-title mb-3">Contract Status</div>
        @if($contract)
            <div style="background:#e6faf2;border-radius:8px;padding:16px;">
                <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">Active Contract</div>
                <div style="font-size:16px;font-weight:700;color:#01BF63;">
                    @if($contract->type === 'per_click')
                        ${{ number_format($contract->rate, 4) }} per 1,000 clicks
                    @else
                        ${{ number_format($contract->rate, 4) }}/day fixed
                    @endif
                </div>
                <div style="font-size:12px;color:#6b7280;margin-top:4px;">Accepted {{ $contract->responded_at?->format('M d, Y') }}</div>
            </div>
        @elseif($hasTestRunning)
            <div style="background:#fef3c7;border-radius:8px;padding:16px;">
                <div style="font-size:14px;font-weight:600;color:#92400e;">48-Hour Test Running</div>
                <div style="font-size:13px;color:#6b7280;margin-top:4px;">We're analyzing your traffic quality. Results will be used to determine your rate.</div>
            </div>
        @else
            <div style="color:#9ca3af;font-size:14px;">No active contract yet. Admin will offer one after reviewing your test period.</div>
        @endif
    </div>

    <div class="card">
        <div class="card-title mb-3">Quick Actions</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <a href="{{ route('publisher.adcode') }}" class="btn btn-primary" style="justify-content:center;">Get Ad Code</a>
            <a href="{{ route('publisher.stats') }}" class="btn btn-ghost" style="justify-content:center;">View Detailed Stats</a>
            @if($showEarnings)
                <a href="{{ route('publisher.withdrawals.index') }}" class="btn btn-ghost" style="justify-content:center;">Request Withdrawal</a>
            @endif
            <a href="{{ route('publisher.support.create') }}" class="btn btn-ghost" style="justify-content:center;">Contact Support</a>
        </div>
    </div>
</div>

<!-- Contact Team -->
<div class="card" style="margin-top:24px;">
    <div class="card-title mb-1">Contact Installs Bank Team</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">Reach us directly for fast help. We typically reply within a few hours.</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
        <a href="https://wa.me/19707426488?text=Hello%20Installs%20Bank%20team%2C%20I%27m%20a%20publisher%20on%20your%20network%20and%20I%20need%20help%20with%20my%20account." target="_blank" rel="noopener"
           style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#f0fdf4;border:1.5px solid #a7f3d0;border-radius:12px;text-decoration:none;transition:all 0.2s;"
           onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="width:40px;height:40px;background:#25D366;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:#065f46;">WhatsApp</div>
                <div style="font-size:12px;color:#6b7280;">+1 (970) 742-6488</div>
            </div>
        </a>
        <a href="https://t.me/installsbank?text=Hello%20Installs%20Bank%20team%2C%20I%27m%20a%20publisher%20on%20your%20network%20and%20I%20need%20help." target="_blank" rel="noopener"
           style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:12px;text-decoration:none;transition:all 0.2s;"
           onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="width:40px;height:40px;background:#229ED9;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:#1e40af;">Telegram</div>
                <div style="font-size:12px;color:#6b7280;">@installsbank</div>
            </div>
        </a>
        <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#f0fdf4;border:1.5px solid #a7f3d0;border-radius:12px;cursor:pointer;transition:all 0.2s;"
             onclick="navigator.clipboard.writeText('+19707426488');this.querySelector('.wechat-label').textContent='Copied!';setTimeout(()=>this.querySelector('.wechat-label').textContent='WeChat',1500);"
             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="width:40px;height:40px;background:#07C160;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 01.213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 00.167-.054l1.903-1.114a.864.864 0 01.717-.098 10.16 10.16 0 002.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348zM5.785 5.991c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 01-1.162 1.178A1.17 1.17 0 014.623 7.17c0-.651.52-1.18 1.162-1.18zm5.813 0c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 01-1.162 1.178 1.17 1.17 0 01-1.162-1.178c0-.651.52-1.18 1.162-1.18zm5.34 2.867c-1.797-.052-3.746.512-5.28 1.786-1.72 1.428-2.687 3.72-1.78 6.22.942 2.453 3.666 4.229 6.884 4.229.826 0 1.622-.12 2.361-.336a.722.722 0 01.598.082l1.584.926a.272.272 0 00.14.045c.134 0 .24-.111.24-.247 0-.06-.023-.12-.038-.177l-.327-1.233a.582.582 0 01-.023-.156.49.49 0 01.201-.398C23.024 18.48 24 16.82 24 14.98c0-3.21-2.931-5.837-7.062-6.122zm-3.518 3.064c.535 0 .969.44.969.982a.976.976 0 01-.969.983.976.976 0 01-.969-.983c0-.542.434-.982.969-.982zm4.965 0c.535 0 .969.44.969.982a.976.976 0 01-.969.983.976.976 0 01-.969-.983c0-.542.434-.982.969-.982z"/></svg>
            </div>
            <div>
                <div class="wechat-label" style="font-size:14px;font-weight:700;color:#065f46;">WeChat</div>
                <div style="font-size:12px;color:#6b7280;">+1 (970) 742-6488</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Real-time click counter + badge — polls every 30 seconds
function fetchLiveStats() {
    fetch('{{ route('publisher.live-stats') }}')
        .then(r => r.json())
        .then(data => {
            // Animate count update
            const countEl = document.getElementById('liveClickCount');
            const newVal = data.clicks_today.toLocaleString();
            if (countEl.textContent !== newVal) {
                countEl.style.transform = 'scale(1.15)';
                countEl.style.transition = 'transform 0.2s';
                countEl.textContent = newVal;
                setTimeout(() => { countEl.style.transform = 'scale(1)'; }, 200);
            }
            document.getElementById('liveLastHour').textContent = data.clicks_last_hour.toLocaleString();

            const badge = document.getElementById('liveBadge');
            const label = document.getElementById('badgeLabel');
            label.textContent = data.badge.label;
            badge.querySelector('.live-dot').style.background = data.badge.color;
        })
        .catch(() => {});
}
fetchLiveStats();
setInterval(fetchLiveStats, 30000);

const data = @json($clicksChart);
new ApexCharts(document.getElementById('pubClickChart'), {
    series: [
        { name: 'Clicks', data: data.map(d => d.clicks) },
        @if($showEarnings)
        { name: 'Earnings ($)', data: data.map(d => d.earnings) }
        @endif
    ],
    chart: { type: 'area', height: 220, toolbar: { show: false } },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
    colors: ['#01BF63', '#3b82f6'],
    xaxis: { categories: data.map(d => d.date) },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' }
}).render();

@php
    $donutOpts = function($id, $labels, $values, $colors) {
        return compact('id','labels','values','colors');
    };
@endphp
@if($countryBreakdown && $countryBreakdown->count() > 0)
@php
    $pieLabels = [];
    $pieValues = [];
    foreach ($sortedBreakdown->take(8) as $code => $data) {
        $pieLabels[] = $countryNames[$code] ?? strtoupper($code);
        $pieValues[] = (int) $data['clicks'];
    }
    if ($sortedBreakdown->count() > 8) {
        $pieLabels[] = 'Others';
        $pieValues[] = (int) $sortedBreakdown->slice(8)->sum('clicks');
    }
@endphp
new ApexCharts(document.getElementById('countryPieChart'), {
    series: @json($pieValues),
    labels: @json($pieLabels),
    chart: { type: 'donut', height: 280, toolbar: { show: false } },
    colors: ['#01BF63','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899','#9ca3af'],
    plotOptions: { pie: { donut: { size: '60%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, color: '#374151', formatter: w => w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString() } } } } },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '12px' },
    tooltip: { y: { formatter: val => val.toLocaleString() + ' clicks' } }
}).render();
@endif

@if($osBreakdown && $osBreakdown->count() > 0)
@php
    $osLabels = [];
    $osValues = [];
    foreach ($osBreakdown as $os => $data) {
        $osLabels[] = $os ?: 'Unknown';
        $osValues[] = (int) (is_array($data) ? $data['clicks'] : $data);
    }
@endphp
new ApexCharts(document.getElementById('osPieChart'), {
    series: @json($osValues),
    labels: @json($osLabels),
    chart: { type: 'donut', height: 280, toolbar: { show: false } },
    colors: ['#3b82f6','#01BF63','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#9ca3af'],
    plotOptions: { pie: { donut: { size: '60%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, color: '#374151', formatter: w => w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString() } } } } },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '12px' },
    tooltip: { y: { formatter: val => val.toLocaleString() + ' clicks' } }
}).render();
@endif
</script>
@endpush
