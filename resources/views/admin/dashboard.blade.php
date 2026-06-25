@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ═══════════════════════════════════════════════
     HERO STRIP — Dark gradient with live metrics
     ═══════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);border-radius:20px;padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;">

    {{-- Decorative orbs --}}
    <div style="position:absolute;top:-50px;right:-50px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(1,191,99,.15) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-30px;left:30%;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.12) 0%,transparent 70%);pointer-events:none;"></div>

    <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;position:relative;">

        {{-- Left: title + live indicator --}}
        <div style="flex:1;min-width:200px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#64748b;margin-bottom:6px;display:flex;align-items:center;gap:7px;">
                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#01BF63;box-shadow:0 0 8px #01BF63;animation:pulse 2s infinite;"></span>
                Live Overview
            </div>
            <div style="font-size:28px;font-weight:900;color:#f8fafc;letter-spacing:-0.5px;line-height:1.1;">Installs Bank</div>
            <div style="font-size:14px;color:#64748b;margin-top:4px;">Admin Dashboard · {{ now()->format('l, F j Y') }}</div>
        </div>

        {{-- Right: 3 hero KPIs --}}
        <div style="display:flex;gap:12px;flex-wrap:wrap;flex-shrink:0;">
            <div style="background:rgba(1,191,99,.12);border:1px solid rgba(1,191,99,.25);border-radius:14px;padding:14px 22px;text-align:center;min-width:110px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#6ee7b7;margin-bottom:4px;">Valid Today</div>
                <div style="font-size:28px;font-weight:900;color:#01BF63;line-height:1;">{{ number_format($stats['valid_clicks_today']) }}</div>
                <div style="font-size:11px;color:#475569;margin-top:2px;">clicks counted</div>
            </div>
            <div style="background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.25);border-radius:14px;padding:14px 22px;text-align:center;min-width:110px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#93c5fd;margin-bottom:4px;">Active</div>
                <div style="font-size:28px;font-weight:900;color:#60a5fa;line-height:1;">{{ number_format($stats['active_publishers']) }}</div>
                <div style="font-size:11px;color:#475569;margin-top:2px;">publishers</div>
            </div>
            <div style="background:rgba(236,72,153,.12);border:1px solid rgba(236,72,153,.25);border-radius:14px;padding:14px 22px;text-align:center;min-width:110px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#f9a8d4;margin-bottom:4px;">Pending</div>
                <div style="font-size:28px;font-weight:900;color:#f472b6;line-height:1;">${{ number_format($stats['pending_withdrawals_amount'], 0) }}</div>
                <div style="font-size:11px;color:#475569;margin-top:2px;">withdrawals</div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.4;} }
</style>

{{-- ═══════════════════════════════════════════════
     STAT CARDS (6 cards, 3-col)
     ═══════════════════════════════════════════════ --}}
@php
$statCards = [
    [
        'label'   => 'Total Publishers',
        'value'   => number_format($stats['total_publishers']),
        'sub'     => $stats['active_publishers'] . ' active · ' . $stats['pending_publishers'] . ' pending',
        'color'   => '#01BF63','bg'=>'#f0fdf4','border'=>'#bbf7d0','iconBg'=>'#dcfce7',
        'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0',
    ],
    [
        'label'   => 'Total Clicks Today',
        'value'   => number_format($stats['total_clicks_today']),
        'sub'     => number_format($stats['valid_clicks_today']) . ' valid',
        'color'   => '#3b82f6','bg'=>'#eff6ff','border'=>'#bfdbfe','iconBg'=>'#dbeafe',
        'icon'    => 'M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122',
    ],
    [
        'label'   => 'Fraud Clicks Today',
        'value'   => number_format($stats['fraud_clicks_today']),
        'sub'     => $stats['unresolved_fraud_alerts'] . ' unresolved alerts',
        'color'   => '#ef4444','bg'=>'#fef2f2','border'=>'#fecaca','iconBg'=>'#fee2e2',
        'icon'    => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    ],
    [
        'label'   => 'Pending Withdrawals',
        'value'   => '$' . number_format($stats['pending_withdrawals_amount'], 2),
        'sub'     => $stats['pending_withdrawals'] . ' requests',
        'color'   => '#ec4899','bg'=>'#fdf2f8','border'=>'#fbcfe8','iconBg'=>'#fce7f3',
        'icon'    => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
    ],
    [
        'label'   => 'Open Tickets',
        'value'   => number_format($stats['open_tickets']),
        'sub'     => 'Support requests',
        'color'   => '#7c3aed','bg'=>'#f5f3ff','border'=>'#ddd6fe','iconBg'=>'#ede9fe',
        'icon'    => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
    ],
    [
        'label'   => 'Managers',
        'value'   => $stats['total_managers'],
        'sub'     => 'Admin team members',
        'color'   => '#0891b2','bg'=>'#ecfeff','border'=>'#a5f3fc','iconBg'=>'#cffafe',
        'icon'    => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    ],
];
@endphp

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;">
    @foreach($statCards as $sc)
    <div style="background:{{ $sc['bg'] }};border:1.5px solid {{ $sc['border'] }};border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:16px;">
        <div style="width:46px;height:46px;background:{{ $sc['iconBg'] }};border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" stroke="{{ $sc['color'] }}" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sc['icon'] }}"/></svg>
        </div>
        <div>
            <div style="font-size:24px;font-weight:900;color:{{ $sc['color'] }};line-height:1;letter-spacing:-0.5px;">{{ $sc['value'] }}</div>
            <div style="font-size:12px;font-weight:700;color:#374151;margin-top:2px;">{{ $sc['label'] }}</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $sc['sub'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════
     REVENUE OVERVIEW
     ═══════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
    <div style="width:4px;height:18px;background:linear-gradient(180deg,#01BF63,#3b82f6);border-radius:4px;flex-shrink:0;"></div>
    <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#374151;">Revenue Overview</div>
    <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);"></div>
</div>

<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:24px;">
    @php
    $revCards = [
        ['label'=>'Paid Today',       'value'=>'$'.number_format($revenue['paid_today'],4),      'sub'=>'Publisher earnings', 'color'=>'#01BF63','bg'=>'#f0fdf4','border'=>'#bbf7d0','iconBg'=>'#dcfce7','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'This Month',       'value'=>'$'.number_format($revenue['paid_this_month'],2),  'sub'=>'Publisher earnings', 'color'=>'#3b82f6','bg'=>'#eff6ff','border'=>'#bfdbfe','iconBg'=>'#dbeafe','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label'=>'Pending Payouts',  'value'=>'$'.number_format($revenue['pending_payouts'],2),  'sub'=>'Unpaid balances',   'color'=>'#f59e0b','bg'=>'#fffbeb','border'=>'#fde68a','iconBg'=>'#fef3c7','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'All-Time Paid',    'value'=>'$'.number_format($revenue['paid_all_time'],2),    'sub'=>'Total to publishers','color'=>'#059669','bg'=>'#ecfdf5','border'=>'#6ee7b7','iconBg'=>'#d1fae5','icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ['label'=>'Withdrawn',        'value'=>'$'.number_format($revenue['withdrawn_total'],2),  'sub'=>'Confirmed payments', 'color'=>'#7c3aed','bg'=>'#f5f3ff','border'=>'#ddd6fe','iconBg'=>'#ede9fe','icon'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
    ];
    @endphp
    @foreach($revCards as $rc)
    <div style="background:{{ $rc['bg'] }};border:1.5px solid {{ $rc['border'] }};border-radius:14px;padding:16px 18px;">
        <div style="width:36px;height:36px;background:{{ $rc['iconBg'] }};border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
            <svg width="16" height="16" fill="none" stroke="{{ $rc['color'] }}" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $rc['icon'] }}"/></svg>
        </div>
        <div style="font-size:20px;font-weight:900;color:{{ $rc['color'] }};line-height:1;letter-spacing:-0.3px;">{{ $rc['value'] }}</div>
        <div style="font-size:12px;font-weight:700;color:#374151;margin-top:4px;">{{ $rc['label'] }}</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:1px;">{{ $rc['sub'] }}</div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════
     CHARTS ROW
     ═══════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
    <div style="width:4px;height:18px;background:linear-gradient(180deg,#3b82f6,#7c3aed);border-radius:4px;flex-shrink:0;"></div>
    <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#374151;">Analytics</div>
    <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);"></div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px;align-items:start;">

    {{-- Click Traffic Chart --}}
    <div class="card" style="padding:20px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:38px;height:38px;background:linear-gradient(135deg,#01BF63,#059669);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(1,191,99,.25);">
                <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Click Traffic</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Last 7 days — valid &amp; fraud</div>
            </div>
            <span style="margin-left:auto;display:flex;align-items:center;gap:6px;font-size:12px;color:#9ca3af;">
                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#01BF63;box-shadow:0 0 6px #01BF63;animation:pulse 2s infinite;"></span>
                Live tracking
            </span>
        </div>
        <div id="clicksChart"></div>
    </div>

    {{-- OS Breakdown --}}
    <div class="card" style="padding:20px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:38px;height:38px;background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(124,58,237,.25);">
                <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">OS Breakdown</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Valid clicks today</div>
            </div>
        </div>
        <div id="osChart"></div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     COUNTRY BREAKDOWN + FRAUD ALERTS
     ═══════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
    <div style="width:4px;height:18px;background:linear-gradient(180deg,#ef4444,#f97316);border-radius:4px;flex-shrink:0;"></div>
    <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#374151;">Traffic & Alerts</div>
    <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);"></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

    {{-- Top Countries --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(59,130,246,.25);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Top Countries Today</div>
                <div style="font-size:12px;color:#9ca3af;">Valid clicks by geography</div>
            </div>
            <a href="{{ route('admin.rates.index') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;font-size:12px;">Manage Rates →</a>
        </div>
        @php $totalCountryClicks = $countryBreakdown->sum('count'); @endphp
        <div style="padding:8px 0;">
            @forelse($countryBreakdown as $c)
            @php $pct = $totalCountryClicks > 0 ? round($c->count / $totalCountryClicks * 100) : 0; @endphp
            <div style="padding:10px 20px;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:5px;">
                    <span style="font-size:13px;font-weight:600;color:#111827;flex:1;">{{ $c->country_name ?? $c->country_code ?? 'Unknown' }}</span>
                    <span style="font-size:13px;font-weight:700;color:#374151;">{{ number_format($c->count) }}</span>
                    <span style="font-size:12px;color:#9ca3af;width:32px;text-align:right;">{{ $pct }}%</span>
                </div>
                <div style="height:4px;background:#f3f4f6;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#01BF63,#3b82f6);border-radius:4px;"></div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:40px 24px;color:#9ca3af;">
                <svg width="32" height="32" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064"/></svg>
                <div style="font-size:14px;font-weight:600;">No clicks today</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Fraud Alerts --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(239,68,68,.25);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Recent Fraud Alerts</div>
                <div style="font-size:12px;color:#9ca3af;">{{ $stats['unresolved_fraud_alerts'] }} unresolved total</div>
            </div>
            <a href="{{ route('admin.fraud.index') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;font-size:12px;">View All →</a>
        </div>
        <div style="padding:4px 0;">
            @forelse($fraudAlerts as $alert)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f9fafb;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background=''">
                <div style="width:38px;height:38px;background:linear-gradient(135deg,#fef2f2,#fee2e2);border:1px solid #fecaca;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="16" height="16" fill="#ef4444" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $alert->publisher->name }}</div>
                    <div style="font-size:11px;color:#6b7280;margin-top:1px;">{{ str_replace('_', ' ', ucfirst($alert->alert_type)) }} · {{ $alert->occurrences }}x · {{ $alert->created_at->diffForHumans() }}</div>
                </div>
                <form method="POST" action="{{ route('admin.fraud.resolve', $alert) }}" style="flex-shrink:0;">
                    @csrf
                    <button style="padding:5px 12px;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">Resolve</button>
                </form>
            </div>
            @empty
            <div style="text-align:center;padding:40px 24px;color:#9ca3af;">
                <svg width="32" height="32" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div style="font-size:14px;font-weight:600;">No unresolved alerts</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     RECENT REGISTRATIONS
     ═══════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
    <div style="width:4px;height:18px;background:linear-gradient(180deg,#01BF63,#06b6d4);border-radius:4px;flex-shrink:0;"></div>
    <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#374151;">Recent Publishers</div>
    <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);"></div>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:14px;font-weight:800;color:#111827;">Recent Publisher Registrations</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Latest 5 sign-ups</div>
        </div>
        <a href="{{ route('admin.publishers.index') }}" class="btn btn-primary btn-sm">View All Publishers →</a>
    </div>

    @php
    $avatarGrads = [
        'linear-gradient(135deg,#01BF63,#059669)',
        'linear-gradient(135deg,#3b82f6,#1d4ed8)',
        'linear-gradient(135deg,#8b5cf6,#6d28d9)',
        'linear-gradient(135deg,#f59e0b,#d97706)',
        'linear-gradient(135deg,#ec4899,#db2777)',
    ];
    @endphp

    @forelse($recentPublishers as $idx => $pub)
    @php
        $initials = collect(explode(' ', $pub->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
        $grad = $avatarGrads[$idx % count($avatarGrads)];
    @endphp
    <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f9fafb;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
        {{-- Avatar --}}
        <div style="width:40px;height:40px;border-radius:12px;background:{{ $grad }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;font-weight:900;color:white;letter-spacing:-0.5px;box-shadow:0 3px 8px rgba(0,0,0,.12);">
            {{ $initials }}
        </div>

        {{-- Name + Email --}}
        <div style="flex:1;min-width:0;">
            <div style="font-size:13px;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pub->name }}</div>
            <div style="font-size:12px;color:#6b7280;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pub->email }}</div>
        </div>

        {{-- Website --}}
        <div style="font-size:12px;color:#9ca3af;min-width:120px;text-align:left;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            {{ $pub->website ?? '—' }}
        </div>

        {{-- Registered --}}
        <div style="font-size:12px;color:#9ca3af;white-space:nowrap;min-width:90px;text-align:right;">
            {{ $pub->created_at->diffForHumans() }}
        </div>

        {{-- Status --}}
        <div style="min-width:70px;text-align:center;">
            @if($pub->status === 'active')
                <span style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">Active</span>
            @elseif($pub->status === 'pending')
                <span style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">Pending</span>
            @else
                <span style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">Suspended</span>
            @endif
        </div>

        {{-- Action --}}
        <a href="{{ route('admin.publishers.show', $pub) }}"
           style="padding:6px 14px;background:linear-gradient(135deg,#01BF63,#059669);color:white;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;box-shadow:0 2px 6px rgba(1,191,99,.3);">
            Manage →
        </a>
    </div>
    @empty
    <div style="text-align:center;padding:48px 24px;color:#9ca3af;">
        <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 10px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857"/></svg>
        <div style="font-size:14px;font-weight:600;">No publishers yet</div>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
const clicksData = @json($clicksChart);
new ApexCharts(document.getElementById('clicksChart'), {
    series: [
        { name: 'Valid Clicks', data: clicksData.map(d => d.total) },
        { name: 'Fraud',        data: clicksData.map(d => d.fraud) }
    ],
    chart: {
        type: 'area',
        height: 230,
        toolbar: { show: false },
        fontFamily: 'inherit',
    },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.03, shadeIntensity: 1 } },
    colors: ['#01BF63', '#ef4444'],
    xaxis: {
        categories: clicksData.map(d => d.date),
        labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
    tooltip: { shared: true, y: { formatter: v => v.toLocaleString() + ' clicks' } },
    legend: { position: 'top', fontSize: '12px', markers: { radius: 4 } },
    grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
    dataLabels: { enabled: false },
    markers: { size: 4, strokeWidth: 0 }
}).render();

const osData = @json($osBreakdown);
if (osData.length > 0) {
    new ApexCharts(document.getElementById('osChart'), {
        series: osData.map(d => d.count),
        labels: osData.map(d => d.os || 'Unknown'),
        chart: { type: 'donut', height: 230, fontFamily: 'inherit' },
        colors: ['#01BF63', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
        legend: { position: 'bottom', fontSize: '12px' },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '12px',
                            color: '#6b7280',
                            formatter: w => w.globals.seriesTotals.reduce((a,b) => a+b, 0).toLocaleString()
                        }
                    }
                }
            }
        },
        stroke: { width: 2 },
        tooltip: { y: { formatter: v => v.toLocaleString() + ' clicks' } }
    }).render();
} else {
    document.getElementById('osChart').innerHTML = '<div style="text-align:center;padding:60px 24px;color:#9ca3af;"><svg width="32" height="32" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><div style="font-size:13px;font-weight:600;">No click data today</div></div>';
}

setTimeout(() => location.reload(), 30000);
</script>
@endpush
