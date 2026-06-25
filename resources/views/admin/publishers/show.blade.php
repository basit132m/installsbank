@extends('layouts.admin')
@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
@php
    $statusColor = match($user->status) {
        'active'    => ['bg'=>'rgba(16,185,129,0.18)','text'=>'#6ee7b7','dot'=>'#10b981','label'=>'Active'],
        'pending'   => ['bg'=>'rgba(245,158,11,0.18)', 'text'=>'#fcd34d','dot'=>'#f59e0b','label'=>'Pending'],
        'suspended' => ['bg'=>'rgba(239,68,68,0.18)',  'text'=>'#fca5a5','dot'=>'#ef4444','label'=>'Suspended'],
        default     => ['bg'=>'rgba(156,163,175,0.18)','text'=>'#d1d5db','dot'=>'#9ca3af','label'=>ucfirst($user->status)],
    };
    $contractType = $user->publisherProfile?->contract_type ?? 'none';
    $contractLabel = match($contractType) {
        'per_click'     => 'Per Click',
        'fixed'         => 'Fixed Daily',
        'installs_base' => 'Installs Based',
        default         => 'No Contract',
    };
    $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
    $tagColors = ['green'=>'#01BF63','blue'=>'#3b82f6','red'=>'#ef4444','amber'=>'#f59e0b','gray'=>'#6b7280','purple'=>'#8b5cf6'];
    $avatarGradients = [
        'linear-gradient(135deg,#01BF63,#059669)',
        'linear-gradient(135deg,#3b82f6,#1d4ed8)',
        'linear-gradient(135deg,#8b5cf6,#6d28d9)',
        'linear-gradient(135deg,#f59e0b,#d97706)',
        'linear-gradient(135deg,#ec4899,#db2777)',
    ];
    $avatarGradient = $avatarGradients[crc32($user->name) % count($avatarGradients)];
@endphp

{{-- ═══════════════════════════════════════════════
     HERO PANEL — Dark gradient
     ═══════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);border-radius:20px;padding:32px;margin-bottom:24px;position:relative;overflow:hidden;">

    {{-- Decorative orbs --}}
    <div style="position:absolute;top:-60px;right:-60px;width:240px;height:240px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.18) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-40px;left:20%;width:180px;height:180px;border-radius:50%;background:radial-gradient(circle,rgba(1,191,99,.12) 0%,transparent 70%);pointer-events:none;"></div>

    {{-- Top row: Avatar + Name + Badges + Earnings --}}
    <div style="display:flex;gap:24px;align-items:flex-start;flex-wrap:wrap;position:relative;">

        {{-- Avatar --}}
        <div style="width:72px;height:72px;border-radius:18px;background:{{ $avatarGradient }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:26px;font-weight:900;color:white;letter-spacing:-1px;box-shadow:0 8px 24px rgba(0,0,0,.3);">
            {{ $initials }}
        </div>

        {{-- Name + pills + tags --}}
        <div style="flex:1;min-width:200px;">
            <div style="font-size:24px;font-weight:900;color:#f8fafc;letter-spacing:-0.5px;line-height:1.1;margin-bottom:10px;">
                {{ $user->name }}
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                {{-- Status --}}
                <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};border:1px solid {{ $statusColor['dot'] }}40;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $statusColor['dot'] }};box-shadow:0 0 6px {{ $statusColor['dot'] }};display:inline-block;"></span>
                    {{ $statusColor['label'] }}
                </span>
                {{-- Contract --}}
                <span style="display:inline-flex;align-items:center;background:rgba(59,130,246,.2);color:#93c5fd;border:1px solid rgba(59,130,246,.3);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                    {{ $contractLabel }}
                </span>
                {{-- Payment --}}
                @if($user->publisherProfile?->payment_enabled)
                <span style="display:inline-flex;align-items:center;background:rgba(1,191,99,.15);color:#6ee7b7;border:1px solid rgba(1,191,99,.3);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                    Payments On
                </span>
                @else
                <span style="display:inline-flex;align-items:center;background:rgba(156,163,175,.12);color:#9ca3af;border:1px solid rgba(156,163,175,.2);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                    Payments Off
                </span>
                @endif
                @if($user->publisherProfile?->adcode_requested_at)
                <span style="display:inline-flex;align-items:center;background:rgba(245,158,11,.18);color:#fcd34d;border:1px solid rgba(245,158,11,.3);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;" title="Requested {{ $user->publisherProfile->adcode_requested_at->format('M d, Y H:i') }}">
                    Adcode Pending
                </span>
                @endif
                {{-- Tags --}}
                @foreach($user->publisherTags as $tag)
                @php $tc = $tagColors[$tag->color] ?? '#6b7280'; @endphp
                <span style="display:inline-flex;align-items:center;background:{{ $tc }}22;color:{{ $tc }};border:1px solid {{ $tc }}50;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                    {{ $tag->tag }}
                </span>
                @endforeach
            </div>
        </div>

        {{-- Earnings panels --}}
        <div style="display:flex;gap:12px;flex-shrink:0;flex-wrap:wrap;">
            <div style="background:rgba(1,191,99,.12);border:1px solid rgba(1,191,99,.25);border-radius:14px;padding:14px 22px;text-align:center;min-width:120px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#6ee7b7;margin-bottom:4px;">Balance</div>
                <div style="font-size:26px;font-weight:900;color:#01BF63;line-height:1;">${{ number_format($user->publisherProfile?->balance ?? 0, 2) }}</div>
            </div>
            <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:14px 22px;text-align:center;min-width:120px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#94a3b8;margin-bottom:4px;">Total Earned</div>
                <div style="font-size:26px;font-weight:900;color:#e2e8f0;line-height:1;">${{ number_format($user->publisherProfile?->total_earnings ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Contact info row --}}
    <div style="display:flex;flex-wrap:wrap;gap:20px;margin-top:22px;padding-top:20px;border-top:1px solid rgba(255,255,255,.08);position:relative;">
        <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#94a3b8;">
            <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            {{ $user->email }}
        </div>
        @if($user->phone)
        <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#94a3b8;">
            <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            {{ $user->phone }}
        </div>
        @endif
        @if($user->telegram)
        <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#94a3b8;">
            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="color:#64748b;"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.085 13.86l-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.834.7z"/></svg>
            {{ $user->telegram }}
        </div>
        @endif
        @if($user->website)
        <div style="display:flex;align-items:center;gap:7px;font-size:13px;">
            <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <a href="{{ $user->website }}" target="_blank" style="color:#34d399;font-weight:600;text-decoration:none;">{{ parse_url($user->website, PHP_URL_HOST) ?: $user->website }}</a>
        </div>
        @endif
        <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#64748b;">
            <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Joined {{ $user->created_at->format('M d, Y') }}
        </div>
        <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#64748b;">
            <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Last login {{ $user->last_login_at?->diffForHumans() ?? 'never' }}
        </div>
    </div>

    {{-- Action buttons --}}
    <div style="display:flex;gap:8px;align-items:center;margin-top:20px;padding-top:18px;border-top:1px solid rgba(255,255,255,.08);flex-wrap:wrap;position:relative;">
        <a href="{{ route('admin.publishers.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.08);color:#cbd5e1;border:1px solid rgba(255,255,255,.15);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:600;text-decoration:none;transition:background .15s;"
           onmouseover="this.style.background='rgba(255,255,255,.14)'" onmouseout="this.style.background='rgba(255,255,255,.08)'">
            ← All Publishers
        </a>
        <a href="{{ route('admin.publishers.stats', $user) }}"
           style="display:inline-flex;align-items:center;gap:6px;background:rgba(59,130,246,.2);color:#93c5fd;border:1px solid rgba(59,130,246,.35);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:600;text-decoration:none;"
           onmouseover="this.style.background='rgba(59,130,246,.3)'" onmouseout="this.style.background='rgba(59,130,246,.2)'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Detailed Stats
        </a>
        @if($user->status === 'pending')
            <form method="POST" action="{{ route('admin.publishers.activate', $user) }}" style="display:inline;margin:0;">@csrf
                <button style="display:inline-flex;align-items:center;gap:6px;background:rgba(1,191,99,.2);color:#6ee7b7;border:1px solid rgba(1,191,99,.35);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:700;cursor:pointer;">
                    Activate Publisher
                </button>
            </form>
        @elseif($user->status === 'active')
            <form method="POST" action="{{ route('admin.publishers.suspend', $user) }}" style="display:inline;margin:0;" onsubmit="return confirm('Suspend this publisher?')">@csrf
                <button style="display:inline-flex;align-items:center;gap:6px;background:rgba(239,68,68,.2);color:#fca5a5;border:1px solid rgba(239,68,68,.35);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:700;cursor:pointer;">
                    Suspend
                </button>
            </form>
        @elseif($user->status === 'suspended')
            <form method="POST" action="{{ route('admin.publishers.activate', $user) }}" style="display:inline;margin:0;" onsubmit="return confirm('Reactivate this publisher?')">@csrf
                <button style="display:inline-flex;align-items:center;gap:6px;background:rgba(1,191,99,.2);color:#6ee7b7;border:1px solid rgba(1,191,99,.35);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:700;cursor:pointer;">
                    Reactivate Publisher
                </button>
            </form>
        @endif
        <form method="POST" action="{{ route('admin.publishers.destroy', $user) }}" style="display:inline;margin-left:auto;margin-top:0;"
              onsubmit="return confirm('DELETE {{ addslashes($user->name) }}?\n\nThis permanently deletes ALL data including clicks, earnings, tracking links, withdrawals, and fraud alerts.\n\nThis cannot be undone.')">
            @csrf @method('DELETE')
            <button style="display:inline-flex;align-items:center;gap:6px;background:rgba(239,68,68,.15);color:#fca5a5;border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:700;cursor:pointer;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete Publisher
            </button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     STAT CARDS STRIP
     ═══════════════════════════════════════════════ --}}
@php
    $statCards = [
        ['label'=>'Valid Clicks Today',    'value'=>number_format($clickStats['today']),                'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',   'color'=>'#01BF63','bg'=>'#f0fdf4','border'=>'#bbf7d0','iconBg'=>'#dcfce7'],
        ['label'=>'This Week',             'value'=>number_format($clickStats['this_week']),            'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color'=>'#3b82f6','bg'=>'#eff6ff','border'=>'#bfdbfe','iconBg'=>'#dbeafe'],
        ['label'=>'All Time Valid',        'value'=>number_format($clickStats['total']),                'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',                   'color'=>'#7c3aed','bg'=>'#f5f3ff','border'=>'#ddd6fe','iconBg'=>'#ede9fe'],
        ['label'=>'Windows Today (Shown)', 'value'=>number_format($clickStats['today_windows']),        'icon'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a','iconBg'=>'#fef3c7'],
        ['label'=>'Actual Windows (Raw)',  'value'=>number_format($clickStats['total_actual_windows']), 'icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'color'=>'#ea580c','bg'=>'#fff7ed','border'=>'#fed7aa','iconBg'=>'#ffedd5'],
        ['label'=>'Fraud Clicks Today',   'value'=>number_format($clickStats['today_fraud']),          'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color'=>'#ef4444','bg'=>'#fef2f2','border'=>'#fecaca','iconBg'=>'#fee2e2'],
    ];
@endphp
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;">
    @foreach($statCards as $sc)
    <div style="background:{{ $sc['bg'] }};border:1.5px solid {{ $sc['border'] }};border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:16px;">
        <div style="width:46px;height:46px;background:{{ $sc['iconBg'] }};border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" stroke="{{ $sc['color'] }}" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sc['icon'] }}"/></svg>
        </div>
        <div>
            <div style="font-size:26px;font-weight:900;color:{{ $sc['color'] }};line-height:1;letter-spacing:-0.5px;">{{ $sc['value'] }}</div>
            <div style="font-size:12px;color:#6b7280;margin-top:3px;font-weight:500;">{{ $sc['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════
     CHARTS ROW
     ═══════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:{{ $osBreakdown->isNotEmpty() ? '2fr 1fr' : '1fr' }};gap:20px;margin-bottom:24px;align-items:start;">

    {{-- Click History --}}
    <div class="card" style="padding:20px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:38px;height:38px;background:linear-gradient(135deg,#01BF63,#059669);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(1,191,99,.25);">
                <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Click History</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Last 14 days — valid, Windows &amp; fraud</div>
            </div>
            <a href="{{ route('admin.publishers.stats', $user) }}" style="margin-left:auto;font-size:12px;color:#3b82f6;font-weight:600;text-decoration:none;">Full stats →</a>
        </div>
        <div id="publisherClickChart"></div>
    </div>

    {{-- OS Breakdown --}}
    @if($osBreakdown->isNotEmpty())
    @php
        $osTotal  = $osBreakdown->sum();
        $osColors = ['#7c3aed','#01BF63','#3b82f6','#f59e0b','#ef4444','#06b6d4','#ec4899','#84cc16','#6b7280'];
        $oIdx = 0;
    @endphp
    <div class="card" style="padding:20px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:38px;height:38px;background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(124,58,237,.25);">
                <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Clicks by OS</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Last 30 days — <strong style="color:#374151;">{{ number_format($osTotal) }}</strong> total</div>
            </div>
        </div>
        <div id="osDonutChart" style="margin:0 auto 12px;"></div>
        <div>
            @foreach($osBreakdown as $os => $cnt)
            @php $pct = $osTotal > 0 ? round(($cnt / $osTotal) * 100, 1) : 0; $c = $osColors[$oIdx % count($osColors)]; @endphp
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:9px;">
                <div style="width:9px;height:9px;border-radius:50%;background:{{ $c }};flex-shrink:0;"></div>
                <div style="flex:1;font-size:12px;font-weight:600;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $os }}</div>
                <div style="font-size:12px;color:#6b7280;flex-shrink:0;">{{ number_format($cnt) }}</div>
                <div style="width:60px;flex-shrink:0;">
                    <div style="height:4px;background:#f3f4f6;border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $c }};border-radius:4px;"></div>
                    </div>
                </div>
                <div style="font-size:11px;color:#9ca3af;width:34px;text-align:right;flex-shrink:0;">{{ $pct }}%</div>
            </div>
            @php $oIdx++; @endphp
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════
     SCREENSHOTS
     ═══════════════════════════════════════════════ --}}
@if($user->stat_screenshots && count($user->stat_screenshots) > 0)
<div class="card" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
        <div style="width:38px;height:38px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div style="font-size:14px;font-weight:800;color:#111827;">Submitted Statistics Screenshots</div>
            <div style="font-size:12px;color:#6b7280;margin-top:1px;">{{ count($user->stat_screenshots) }} file(s) submitted with application</div>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;">
        @foreach($user->stat_screenshots as $i => $path)
        <a href="{{ asset('storage/' . $path) }}" target="_blank"
           style="display:block;border-radius:10px;overflow:hidden;border:1.5px solid #e5e7eb;transition:all .15s;"
           onmouseover="this.style.borderColor='#01BF63';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e5e7eb';this.style.transform=''">
            <img src="{{ asset('storage/' . $path) }}" alt="Screenshot {{ $i+1 }}" style="width:100%;height:95px;object-fit:cover;display:block;">
            <div style="background:#f9fafb;padding:5px 8px;font-size:11px;color:#6b7280;font-weight:600;text-align:center;">Screenshot {{ $i+1 }}</div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════
     INSTALL EARNINGS (installs_base only)
     ═══════════════════════════════════════════════ --}}
@if($installStats)
<div class="card" style="border:1.5px solid #ddd6fe;margin-bottom:24px;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
        <div style="width:40px;height:40px;background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(124,58,237,.25);">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </div>
        <div>
            <div style="font-size:15px;font-weight:800;color:#111827;">Install Earnings — Today</div>
            <div style="font-size:12px;color:#6b7280;margin-top:2px;">Divider: <strong>{{ $installStats['divider_value'] }}×</strong> &nbsp;·&nbsp; Clicks per install: <strong>{{ $installStats['ratio'] }}</strong> &nbsp;·&nbsp; Effective threshold: <strong>{{ (int)round($installStats['ratio'] * $installStats['divider_value']) }} raw clicks</strong></div>
        </div>
        <span style="margin-left:auto;background:#ede9fe;color:#7c3aed;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;">Installs Base</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:12px;padding:18px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#059669;margin-bottom:10px;">Publisher Sees (Divider Applied)</div>
            <div style="display:flex;gap:20px;">
                <div><div style="font-size:28px;font-weight:900;color:#059669;">{{ number_format($installStats['publisher_installs']) }}</div><div style="font-size:11px;color:#6b7280;">Installs</div></div>
                <div><div style="font-size:28px;font-weight:900;color:#059669;">${{ number_format($installStats['publisher_earnings'], 4) }}</div><div style="font-size:11px;color:#6b7280;">Earnings</div></div>
            </div>
        </div>
        <div style="background:#fff7ed;border:1.5px solid #fed7aa;border-radius:12px;padding:18px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#c2410c;margin-bottom:10px;">Actual (Admin Only — No Divider)</div>
            <div style="display:flex;gap:20px;">
                <div><div style="font-size:28px;font-weight:900;color:#ea580c;">{{ number_format($installStats['actual_installs']) }}</div><div style="font-size:11px;color:#6b7280;">Installs</div></div>
                <div><div style="font-size:28px;font-weight:900;color:#ea580c;">${{ number_format($installStats['actual_earnings'], 4) }}</div><div style="font-size:11px;color:#6b7280;">Earnings</div></div>
            </div>
        </div>
    </div>

    @if($installStats['publisher_by_country']->count() > 0 || $installStats['actual_by_country']->count() > 0)
    <div style="overflow-x:auto;border-radius:10px;border:1px solid #f3f4f6;">
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <th style="text-align:left;padding:10px 14px;color:#6b7280;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;">Country</th>
                    <th style="text-align:right;padding:10px 14px;color:#059669;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;">Pub Installs</th>
                    <th style="text-align:right;padding:10px 14px;color:#059669;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;">Pub Earnings</th>
                    <th style="text-align:right;padding:10px 14px;color:#c2410c;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;">Act Installs</th>
                    <th style="text-align:right;padding:10px 14px;color:#c2410c;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;">Act Earnings</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $pubMap    = $installStats['publisher_by_country']->keyBy('country_code');
                    $actualMap = collect($installStats['actual_by_country'])->keyBy('country_code');
                    $allCodes  = $pubMap->keys()->merge($actualMap->keys())->unique();
                @endphp
                @foreach($allCodes as $code)
                @php $pub = $pubMap->get($code); $actual = $actualMap->get($code); @endphp
                <tr style="border-bottom:1px solid #f3f4f6;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:10px 14px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <img src="https://flagcdn.com/20x15/{{ strtolower($code) }}.png" style="border-radius:2px;" onerror="this.style.display='none'">
                            <span style="font-weight:600;color:#111827;">{{ $pub?->country_name ?? strtoupper($code) }}</span>
                            <code style="font-size:11px;color:#9ca3af;">{{ strtoupper($code) }}</code>
                        </div>
                    </td>
                    <td style="padding:10px 14px;text-align:right;font-weight:700;color:#059669;">{{ number_format($pub?->install_count ?? 0) }}</td>
                    <td style="padding:10px 14px;text-align:right;color:#059669;">${{ number_format($pub?->earnings ?? 0, 4) }}</td>
                    <td style="padding:10px 14px;text-align:right;font-weight:700;color:#ea580c;">{{ number_format($actual['installs'] ?? 0) }}</td>
                    <td style="padding:10px 14px;text-align:right;color:#ea580c;">${{ number_format($actual['earnings'] ?? 0, 4) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid #e5e7eb;background:#f9fafb;">
                    <td style="padding:10px 14px;font-weight:800;color:#374151;">Total</td>
                    <td style="padding:10px 14px;text-align:right;font-weight:800;color:#059669;">{{ number_format($installStats['publisher_installs']) }}</td>
                    <td style="padding:10px 14px;text-align:right;font-weight:700;color:#059669;">${{ number_format($installStats['publisher_earnings'], 4) }}</td>
                    <td style="padding:10px 14px;text-align:right;font-weight:800;color:#ea580c;">{{ number_format($installStats['actual_installs']) }}</td>
                    <td style="padding:10px 14px;text-align:right;font-weight:700;color:#ea580c;">${{ number_format($installStats['actual_earnings'], 4) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:16px;color:#9ca3af;font-size:13px;">No installs recorded today yet.</div>
    @endif

    <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
        <div style="flex:1;">
            <div style="font-size:13px;font-weight:700;color:#374151;">Apply Divider Retroactively</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Rebuilds all historical install records using the divider-adjusted threshold. Run once to sync existing data.</div>
        </div>
        <form method="POST" action="{{ route('admin.publishers.recalculate-installs', $user) }}"
              onsubmit="return confirm('Recalculate all install history for {{ addslashes($user->name) }} using divider {{ $installStats["divider_value"] }}×?\n\nThis will rebuild publisher_installs from Click data and adjust their balance. This cannot be undone.')">
            @csrf
            <button type="submit" style="padding:10px 20px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;box-shadow:0 4px 12px rgba(124,58,237,.3);">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Recalculate All History
            </button>
        </form>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════
     SECTION DIVIDER: Management
     ═══════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;margin-top:32px;">
    <div style="width:4px;height:18px;background:linear-gradient(180deg,#01BF63,#3b82f6);border-radius:4px;flex-shrink:0;"></div>
    <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#374151;">Management</div>
    <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);"></div>
</div>

<div class="grid-3" style="margin-bottom:24px;gap:16px;">

    {{-- Click Divider --}}
    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(245,158,11,.25);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Windows Click Divider</div>
                <div style="font-size:11px;color:#9ca3af;margin-top:1px;">Hidden from publisher</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.publishers.update-divider', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Divider Value</label>
                <input type="number" name="divider_value" class="form-control" value="{{ $divider->divider_value }}" min="1" max="100" step="0.1">
            </div>
            <div class="toggle-wrap mb-4">
                <label class="toggle"><input type="checkbox" name="is_enabled" value="1" {{ $divider->is_enabled ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                <span style="font-size:13px;font-weight:500;">Enable divider</span>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Update Divider</button>
        </form>
    </div>

    {{-- 48-Hour Test --}}
    <div class="card" style="border:1.5px solid #e5e7eb;">
        @php $testStatus = $user->publisherProfile?->test_status ?? 'not_started'; @endphp
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,{{ $testStatus === 'running' ? '#01BF63,#059669' : ($testStatus === 'completed' ? '#3b82f6,#1d4ed8' : '#6b7280,#4b5563') }});border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(0,0,0,.12);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">48-Hour Test Results</div>
                <div style="font-size:11px;margin-top:1px;font-weight:600;color:{{ $testStatus === 'running' ? '#01BF63' : ($testStatus === 'completed' ? '#3b82f6' : '#9ca3af') }};">
                    {{ ucfirst(str_replace('_', ' ', $testStatus)) }}
                </div>
            </div>
        </div>

        @if($testStatus === 'not_started')
            <div style="background:#f9fafb;border:1px solid #f3f4f6;border-radius:10px;padding:12px;margin-bottom:14px;font-size:13px;color:#6b7280;">
                Waiting for <strong>20 unique clicks</strong> to auto-trigger the 48-hour test period.
            </div>
        @elseif($testStatus === 'running')
            @php
                $endAt    = $user->publisherProfile->test_ended_at;
                $startAt  = $user->publisherProfile->test_started_at;
                $hoursLeft = $endAt ? max(0, now()->diffInHours($endAt, false)) : 0;
                $minsLeft  = $endAt ? max(0, now()->diffInMinutes($endAt, false) % 60) : 0;
            @endphp
            <div style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #6ee7b7;padding:14px;border-radius:10px;margin-bottom:14px;">
                <div style="font-size:20px;font-weight:900;color:#059669;">{{ $hoursLeft }}h {{ $minsLeft }}m remaining</div>
                <div style="font-size:11px;color:#6b7280;margin-top:4px;">
                    Started: {{ $startAt?->format('M d, Y H:i') ?? '—' }}<br>
                    Ends: {{ $endAt?->format('M d, Y H:i') ?? '—' }}
                </div>
            </div>
            @php $liveClicks = \App\Models\Click::where('user_id', $user->id)->where('is_counted', true)->count(); @endphp
            <div style="font-size:13px;color:#374151;margin-bottom:14px;">Current counted clicks: <strong>{{ number_format($liveClicks) }}</strong></div>
        @elseif($testStatus === 'completed')
            @php
                $startAt  = $user->publisherProfile->test_started_at;
                $endAt    = $user->publisherProfile->test_ended_at;
                $duration = ($startAt && $endAt) ? $startAt->diffForHumans($endAt, true) : '—';
                $testClicks = \App\Models\Click::where('user_id', $user->id)->where('is_counted', true)->count();
            @endphp
            <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #93c5fd;padding:14px;border-radius:10px;margin-bottom:14px;">
                <div style="font-size:14px;font-weight:800;color:#1e40af;">Test Completed</div>
                <div style="font-size:12px;color:#1e40af;margin-top:4px;">Duration: {{ $duration }}</div>
                <div style="font-size:12px;color:#1e40af;">Total counted clicks: <strong>{{ number_format($testClicks) }}</strong></div>
            </div>
        @endif

        @if($user->publisherProfile?->test_total_clicks !== null)
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;padding:12px;border-radius:10px;margin-bottom:14px;">
                <div style="font-size:22px;font-weight:900;color:#01BF63;">{{ number_format($user->publisherProfile->test_total_clicks) }}</div>
                <div style="font-size:12px;color:#6b7280;">Test Clicks (Manually Entered)</div>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.publishers.test-results', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Total Clicks (manual override)</label>
                <input type="number" name="test_total_clicks" class="form-control" value="{{ $user->publisherProfile?->test_total_clicks ?? '' }}" placeholder="e.g. 5200" min="0">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Update Results</button>
        </form>
    </div>

    {{-- Offer Contract --}}
    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(59,130,246,.25);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Offer Contract</div>
                <div style="font-size:11px;color:#9ca3af;margin-top:1px;">Publisher accepts in their dashboard</div>
            </div>
        </div>
        @php $pendingContracts = $user->contracts()->where('status','pending')->get(); @endphp
        @if($pendingContracts->isNotEmpty())
            <div style="margin-bottom:14px;">
                <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Pending Offers</div>
                @foreach($pendingContracts as $pc)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 12px;background:#fef3c7;border:1px solid #fde68a;border-radius:8px;margin-bottom:6px;gap:8px;">
                    <span style="font-size:13px;color:#92400e;font-weight:600;">
                        {{ ucfirst(str_replace('_', ' ', $pc->type)) }}
                        @if($pc->rate > 0) · ${{ $pc->rate }}@endif
                        @if($pc->admin_note) <span style="font-weight:400;font-size:11px;">({{ $pc->admin_note }})</span>@endif
                    </span>
                    <form method="POST" action="{{ route('admin.contracts.expire', $pc) }}" style="flex-shrink:0;">
                        @csrf
                        <button type="submit" style="padding:3px 10px;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">Expire</button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin.contracts.offer', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Contract Type</label>
                <select name="type" class="form-control form-select" id="contractType" onchange="toggleRate()">
                    <option value="per_click">Per 1,000 Unique Clicks</option>
                    <option value="fixed">Fixed Daily Rate</option>
                    <option value="installs_base">Installs Based</option>
                </select>
            </div>
            <div class="form-group" id="rateGroup" style="display:none;">
                <label class="form-label">Fixed Daily Rate (USD)</label>
                <input type="number" name="rate" id="rateInput" class="form-control" placeholder="0.0000" step="0.0001" min="0.0001">
            </div>
            <div id="perClickNote" style="background:#e6faf2;border:1px solid #6ee7b7;border-radius:8px;padding:11px;font-size:13px;color:#065f46;margin-bottom:12px;">
                ✓ Rate calculated automatically from country rates. No manual rate needed.
            </div>
            <div id="installsNote" style="display:none;background:#eff6ff;border:1px solid #93c5fd;border-radius:8px;padding:11px;font-size:13px;color:#1e40af;margin-bottom:12px;">
                Publisher earns per install. Rates configured in the <a href="{{ route('admin.install-rates.index') }}" style="color:#2563eb;font-weight:600;">Install Rates</a> page.
            </div>
            <div class="form-group">
                <label class="form-label">Note (optional)</label>
                <textarea name="admin_note" class="form-control" rows="2" placeholder="Internal note..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Send Contract Offer</button>
        </form>
        <script>
        function toggleRate() {
            const type = document.getElementById('contractType').value;
            document.getElementById('rateGroup').style.display     = type === 'fixed'         ? 'block' : 'none';
            document.getElementById('perClickNote').style.display  = type === 'per_click'     ? 'block' : 'none';
            document.getElementById('installsNote').style.display  = type === 'installs_base' ? 'block' : 'none';
            document.getElementById('rateInput').required          = type === 'fixed';
        }
        toggleRate();
        </script>
    </div>
</div>

{{-- Fixed Rate --}}
@if($user->publisherProfile?->contract_type === 'fixed')
<div class="card" style="border:1.5px solid #bbf7d0;margin-bottom:24px;background:linear-gradient(135deg,#f0fdf4,#ecfdf5);">
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;flex-wrap:wrap;">
        <div style="width:42px;height:42px;background:linear-gradient(135deg,#01BF63,#059669);border-radius:11px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(1,191,99,.3);">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div style="font-size:15px;font-weight:800;color:#111827;">Adjust Fixed Daily Rate</div>
            <div style="font-size:12px;color:#6b7280;">Publisher is notified immediately on change</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <div style="font-size:11px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Current Rate</div>
            <div style="font-size:28px;font-weight:900;color:#059669;line-height:1;">${{ number_format($user->publisherProfile->fixed_daily_rate, 4) }}<span style="font-size:13px;font-weight:600;color:#6b7280;">/day</span></div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.publishers.fixed-rate', $user) }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        @csrf
        <div style="flex:1;min-width:180px;">
            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">New Daily Rate (USD)</label>
            <input type="number" name="fixed_daily_rate" class="form-control"
                   value="{{ $user->publisherProfile->fixed_daily_rate }}"
                   step="0.0001" min="0.0001" required
                   style="font-size:16px;font-weight:700;color:#059669;">
        </div>
        <button type="submit" class="btn btn-primary" onclick="return confirm('Update fixed daily rate for {{ addslashes($user->name) }}?\n\nThey will be notified immediately.')">
            Update Rate & Notify Publisher
        </button>
    </form>
</div>
@endif

{{-- ═══════════════════════════════════════════════
     SECTION DIVIDER: Publisher Settings
     ═══════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;margin-top:32px;">
    <div style="width:4px;height:18px;background:linear-gradient(180deg,#3b82f6,#7c3aed);border-radius:4px;flex-shrink:0;"></div>
    <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#374151;">Publisher Settings</div>
    <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);"></div>
</div>

<div class="grid-2" style="margin-bottom:24px;gap:16px;">

    {{-- Payment Settings --}}
    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#01BF63,#059669);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(1,191,99,.25);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Payment Settings</div>
                <div style="font-size:12px;color:#9ca3af;">Control payment processing for this publisher</div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:20px;font-weight:900;color:#059669;">${{ number_format($user->publisherProfile?->balance ?? 0, 4) }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:2px;">Current Balance</div>
            </div>
            <div style="background:#f9fafb;border:1px solid #f3f4f6;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:20px;font-weight:900;color:#374151;">${{ number_format($user->publisherProfile?->total_earnings ?? 0, 4) }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:2px;">Total Earned</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.publishers.payment-status', $user) }}">
            @csrf
            <div class="toggle-wrap">
                <label class="toggle"><input type="checkbox" name="payment_enabled" value="1" {{ $user->publisherProfile?->payment_enabled ? 'checked' : '' }} onchange="this.form.submit()"><span class="toggle-slider"></span></label>
                <div>
                    <span style="font-size:13px;font-weight:600;">Enable Payments</span>
                    <div style="font-size:11px;color:#9ca3af;">Publisher can receive payouts when enabled</div>
                </div>
            </div>
        </form>
    </div>

    {{-- Publisher Tags --}}
    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(59,130,246,.25);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Publisher Tags</div>
                <div style="font-size:12px;color:#9ca3af;">Internal labels for categorizing this publisher</div>
            </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:16px;min-height:36px;">
            @forelse($user->publisherTags as $tag)
            @php $tc = $tagColors[$tag->color] ?? '#6b7280'; @endphp
            <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $tc }}15;color:{{ $tc }};border:1px solid {{ $tc }}40;padding:5px 11px;border-radius:20px;font-size:12px;font-weight:600;">
                {{ $tag->tag }}
                <form method="POST" action="{{ route('admin.publishers.tags.remove', $user) }}" style="display:inline;">
                    @csrf @method('DELETE')
                    <input type="hidden" name="tag" value="{{ $tag->tag }}">
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:{{ $tc }};font-size:15px;line-height:1;padding:0;margin-left:1px;">×</button>
                </form>
            </span>
            @empty
            <span style="color:#9ca3af;font-size:13px;line-height:36px;">No tags yet</span>
            @endforelse
        </div>
        <form method="POST" action="{{ route('admin.publishers.tags.add', $user) }}" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div class="form-group" style="margin:0;flex:1;min-width:120px;">
                <label class="form-label">Tag Name</label>
                <input type="text" name="tag" class="form-control" placeholder="e.g. VIP, Trusted" maxlength="50" required>
            </div>
            <div class="form-group" style="margin:0;width:100px;">
                <label class="form-label">Color</label>
                <select name="color" class="form-control form-select">
                    <option value="green">Green</option>
                    <option value="blue">Blue</option>
                    <option value="amber">Amber</option>
                    <option value="red">Red</option>
                    <option value="purple">Purple</option>
                    <option value="gray">Gray</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="height:38px;">Add</button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     SECTION DIVIDER: Security & Fraud
     ═══════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;margin-top:32px;">
    <div style="width:4px;height:18px;background:linear-gradient(180deg,#ef4444,#f97316);border-radius:4px;flex-shrink:0;"></div>
    <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#374151;">Security & Fraud</div>
    <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);"></div>
</div>

<div class="grid-2" style="margin-bottom:24px;gap:16px;">

    {{-- Domain Restriction --}}
    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(245,158,11,.25);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Domain Restriction</div>
                <div style="font-size:12px;color:#9ca3af;">Control which referrer sources are accepted</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.publishers.settings', $user) }}">
            @csrf
            <div class="toggle-wrap mb-4">
                <label class="toggle">
                    <input type="hidden" name="enforce_domain_restriction" value="0">
                    <input type="checkbox" name="enforce_domain_restriction" value="1" {{ $user->publisherProfile?->enforce_domain_restriction ?? true ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
                <div>
                    <span style="font-size:13px;font-weight:600;">Enforce Domain Restriction</span>
                    <div style="font-size:11px;color:#9ca3af;">Only count clicks from registered domain. Disable to accept any referrer.</div>
                </div>
            </div>
            @if($user->website)
            <div style="background:#f9fafb;border:1px solid #f3f4f6;border-radius:8px;padding:10px 12px;margin-bottom:16px;font-size:12px;color:#6b7280;">
                Registered domain: <strong style="color:#374151;">{{ parse_url($user->website, PHP_URL_HOST) ?: $user->website }}</strong>
            </div>
            @endif
            <button type="submit" class="btn btn-primary btn-sm">Save Setting</button>
        </form>
    </div>

    {{-- Fraud Detection --}}
    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(239,68,68,.25);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">Fraud Detection</div>
                <div style="font-size:12px;color:#9ca3af;">Per-publisher fraud checks</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.publishers.fraud-settings', $user) }}">
            @csrf
            <div class="toggle-wrap mb-3">
                <label class="toggle"><input type="checkbox" name="fraud_headless_browser" value="1" {{ $user->publisherProfile?->fraud_headless_browser ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                <div>
                    <span style="font-size:13px;font-weight:600;">Headless Browser Detection</span>
                    <div style="font-size:11px;color:#9ca3af;">Blocks Selenium, Puppeteer, PhantomJS</div>
                </div>
            </div>
            <div class="toggle-wrap mb-3">
                <label class="toggle"><input type="checkbox" name="fraud_country_mismatch" value="1" {{ $user->publisherProfile?->fraud_country_mismatch ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                <div>
                    <span style="font-size:13px;font-weight:600;">Country Mismatch Detection</span>
                    <div style="font-size:11px;color:#9ca3af;">Flags IP country vs. browser language mismatch</div>
                </div>
            </div>
            <div class="toggle-wrap mb-4">
                <label class="toggle"><input type="checkbox" name="fraud_suspicious_referrer" value="1" {{ $user->publisherProfile?->fraud_suspicious_referrer ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                <div>
                    <span style="font-size:13px;font-weight:600;">Suspicious Referrer Detection</span>
                    <div style="font-size:11px;color:#9ca3af;">Blocks known exchanges, PTC, and bot farms</div>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label">Country Whitelist <span style="font-weight:400;color:#9ca3af;">(comma-separated, e.g. ID, PK, US)</span></label>
                <input type="text" name="allowed_countries" class="form-control"
                    value="{{ $user->publisherProfile?->allowed_countries ? implode(', ', $user->publisherProfile->allowed_countries) : '' }}"
                    placeholder="Leave empty to allow all countries">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save Fraud Settings</button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     SECTION DIVIDER: Tracking & Websites
     ═══════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;margin-top:32px;">
    <div style="width:4px;height:18px;background:linear-gradient(180deg,#01BF63,#06b6d4);border-radius:4px;flex-shrink:0;"></div>
    <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#374151;">Tracking Links & Websites</div>
    <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);"></div>
</div>

{{-- Tracking Links --}}
@php $maxClicks = $user->trackingLinks->max('unique_clicks') ?: 1; @endphp
<div class="card" style="margin-bottom:20px;padding:0;overflow:hidden;">
    <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;">
        <div style="width:36px;height:36px;background:linear-gradient(135deg,#01BF63,#059669);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(1,191,99,.25);">
            <svg width="17" height="17" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        </div>
        <div>
            <div style="font-size:14px;font-weight:800;color:#111827;">Tracking Links</div>
            <div style="font-size:12px;color:#9ca3af;">{{ $user->trackingLinks->count() }} link(s) assigned</div>
        </div>
        <a href="{{ route('admin.tracking.create') }}" class="btn btn-primary btn-sm" style="margin-left:auto;">+ Add Link</a>
    </div>
    <div style="padding:4px 0;">
        @forelse($user->trackingLinks as $link)
        @php $pct = $maxClicks > 0 ? round(($link->unique_clicks / $maxClicks) * 100) : 0; @endphp
        <div style="padding:14px 20px;border-bottom:1px solid #f9fafb;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                <div style="width:8px;height:8px;border-radius:50%;background:{{ $link->is_active ? '#10b981' : '#ef4444' }};flex-shrink:0;box-shadow:0 0 0 2px {{ $link->is_active ? '#d1fae5' : '#fee2e2' }};"></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:700;color:#111827;">{{ $link->name ?: 'Unnamed Link' }}</div>
                    <div style="font-size:11px;color:#9ca3af;font-family:monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;">{{ $link->tracking_url }}</div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:17px;font-weight:900;color:#111827;">{{ number_format($link->unique_clicks) }}</div>
                    <div style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.04em;">valid clicks</div>
                </div>
                <div style="text-align:right;flex-shrink:0;min-width:52px;">
                    <div style="font-size:15px;font-weight:700;color:#ef4444;">{{ number_format($link->fraud_clicks) }}</div>
                    <div style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.04em;">fraud</div>
                </div>
                <span class="badge {{ $link->is_active ? 'badge-success' : 'badge-danger' }}" style="flex-shrink:0;">{{ $link->is_active ? 'Active' : 'Off' }}</span>
            </div>
            <div style="margin-left:20px;">
                <div style="height:4px;background:#f3f4f6;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#01BF63,#3b82f6);border-radius:4px;"></div>
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px 24px;color:#9ca3af;">
            <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 10px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            <div style="font-size:14px;font-weight:600;">No tracking links assigned yet</div>
        </div>
        @endforelse
    </div>
</div>

{{-- Publisher Websites --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;">
        <div style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(59,130,246,.25);">
            <svg width="17" height="17" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
        </div>
        <div>
            <div style="font-size:14px;font-weight:800;color:#111827;">Submitted Websites</div>
            <div style="font-size:12px;color:#9ca3af;">Websites registered for additional ad codes</div>
        </div>
        <a href="{{ route('admin.publisher-websites.index') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;">View All →</a>
    </div>
    <div style="padding:4px 0;">
        @forelse($publisherWebsites as $website)
        <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f9fafb;flex-wrap:wrap;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #bfdbfe;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            </div>
            <div style="flex:1;min-width:160px;">
                <div style="font-size:13px;font-weight:700;color:#111827;">{{ $website->domain }}</div>
                <div style="font-size:11px;color:#9ca3af;">Submitted {{ $website->created_at->format('M d, Y') }}</div>
            </div>
            <span class="badge {{ $website->status === 'approved' ? 'badge-success' : ($website->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                {{ ucfirst($website->status) }}
            </span>
            @if($website->trackingLink)
            <div style="text-align:right;">
                <div style="font-size:13px;font-weight:700;color:#111827;">{{ number_format($website->trackingLink->unique_clicks) }}</div>
                <div style="font-size:11px;color:#9ca3af;">valid clicks</div>
            </div>
            @endif
            @if($website->isPending())
            <a href="{{ route('admin.publisher-websites.index') }}" class="btn btn-primary btn-sm">Review</a>
            @endif
        </div>
        @empty
        <div style="text-align:center;padding:40px 24px;color:#9ca3af;">
            <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 10px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            <div style="font-size:14px;font-weight:600;">No website submissions yet</div>
        </div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
@if($osBreakdown->isNotEmpty())
new ApexCharts(document.getElementById('osDonutChart'), {
    series: @json($osBreakdown->values()),
    labels: @json($osBreakdown->keys()),
    chart: { type: 'donut', width: 200, height: 200 },
    colors: ['#7c3aed','#01BF63','#3b82f6','#f59e0b','#ef4444','#06b6d4','#ec4899','#84cc16','#6b7280'],
    legend: { show: false },
    dataLabels: { enabled: false },
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total',
                        fontSize: '12px',
                        color: '#6b7280',
                        formatter: (w) => w.globals.seriesTotals.reduce((a,b) => a+b, 0).toLocaleString()
                    }
                }
            }
        }
    },
    stroke: { width: 2 },
    tooltip: { y: { formatter: (v) => v.toLocaleString() + ' clicks' } }
}).render();
@endif

const chartData = @json($clicksChart);
new ApexCharts(document.getElementById('publisherClickChart'), {
    series: [
        { name: 'Valid Clicks (Shown)', data: chartData.map(d => d.actual) },
        { name: 'Windows (Actual)',     data: chartData.map(d => d.windows) },
        { name: 'Fraud',               data: chartData.map(d => d.fraud) }
    ],
    chart: {
        type: 'bar',
        height: 240,
        toolbar: { show: false },
        stacked: false,
        fontFamily: 'inherit',
    },
    colors: ['#01BF63', '#f59e0b', '#ef4444'],
    xaxis: {
        categories: chartData.map(d => d.date),
        labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
    legend: { position: 'top', fontSize: '12px', markers: { radius: 4 } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
    plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
    tooltip: { y: { formatter: (v) => v.toLocaleString() + ' clicks' } }
}).render();
</script>
@endpush
