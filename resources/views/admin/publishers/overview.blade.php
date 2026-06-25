@extends('layouts.admin')
@section('title', 'Publisher Overview')
@section('page-title', 'Publisher Overview')

@section('content')
@php
    $dateLabel = $dateFrom->isSameDay($dateTo)
        ? $dateFrom->format('D, M j, Y')
        : $dateFrom->format('M j') . ' – ' . $dateTo->format('M j, Y');

    $grandTotal = $totals['total'];
    $winGlob  = $grandTotal > 0 ? round(($totals['windows'] / $grandTotal) * 100) : 0;
    $andGlob  = $grandTotal > 0 ? round(($totals['android'] / $grandTotal) * 100) : 0;
    $macGlob  = $grandTotal > 0 ? round(($totals['mac']     / $grandTotal) * 100) : 0;
    $othGlob  = $grandTotal > 0 ? round(($totals['other']   / $grandTotal) * 100) : 0;

    $medals = ['🥇','🥈','🥉'];

    $avatarGradients = [
        'linear-gradient(135deg,#6366f1,#8b5cf6)',
        'linear-gradient(135deg,#3b82f6,#06b6d4)',
        'linear-gradient(135deg,#10b981,#059669)',
        'linear-gradient(135deg,#f59e0b,#ef4444)',
        'linear-gradient(135deg,#ec4899,#8b5cf6)',
        'linear-gradient(135deg,#14b8a6,#3b82f6)',
        'linear-gradient(135deg,#f97316,#ef4444)',
    ];
@endphp

{{-- ═══════════════════════════════════════════════
     HERO STATS PANEL
═══════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);border-radius:20px;padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;">

    {{-- Decorative blobs --}}
    <div style="position:absolute;top:-60px;right:-60px;width:240px;height:240px;background:rgba(99,102,241,.15);border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-80px;left:100px;width:200px;height:200px;background:rgba(139,92,246,.1);border-radius:50%;pointer-events:none;"></div>

    {{-- Top row: period tabs + date --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;position:relative;">
        <div style="display:flex;gap:6px;background:rgba(255,255,255,.08);padding:5px;border-radius:12px;">
            @foreach(['today'=>'Today','yesterday'=>'Yesterday','7days'=>'Last 7 Days','28days'=>'Last 28 Days'] as $key=>$lbl)
                <a href="?period={{ $key }}"
                   style="padding:7px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;
                          {{ $period===$key
                              ? 'background:#fff;color:#111827;box-shadow:0 2px 8px rgba(0,0,0,.15);'
                              : 'color:rgba(255,255,255,.6);' }}">
                    {{ $lbl }}
                </a>
            @endforeach
        </div>
        <div style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.5);font-size:13px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span style="color:rgba(255,255,255,.8);font-weight:500;">{{ $dateLabel }}</span>
            <span style="color:rgba(255,255,255,.3);">·</span>
            <span>{{ $rows->count() }} publisher{{ $rows->count() !== 1 ? 's' : '' }} active</span>
        </div>
    </div>

    {{-- Stats grid --}}
    <div style="display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr 1fr;gap:16px;position:relative;">

        {{-- Total Clicks (larger) --}}
        <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:20px 22px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <div style="width:32px;height:32px;background:rgba(99,102,241,.3);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="#a5b4fc" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                </div>
                <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.07em;">Total Clicks</div>
            </div>
            <div style="font-size:36px;font-weight:900;color:#fff;letter-spacing:-1px;line-height:1;">{{ number_format($totals['total']) }}</div>
            {{-- OS split bar --}}
            <div style="display:flex;height:5px;border-radius:4px;overflow:hidden;margin-top:14px;gap:2px;">
                <div style="width:{{ $winGlob }}%;background:#818cf8;border-radius:2px;"></div>
                <div style="width:{{ $andGlob }}%;background:#4ade80;border-radius:2px;"></div>
                <div style="width:{{ $macGlob }}%;background:#38bdf8;border-radius:2px;"></div>
                <div style="width:{{ $othGlob }}%;background:#fbbf24;border-radius:2px;"></div>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;flex-wrap:wrap;">
                <span style="font-size:10px;color:rgba(255,255,255,.4);display:flex;align-items:center;gap:3px;"><span style="width:6px;height:6px;background:#818cf8;border-radius:50%;display:inline-block;"></span>Win {{ $winGlob }}%</span>
                <span style="font-size:10px;color:rgba(255,255,255,.4);display:flex;align-items:center;gap:3px;"><span style="width:6px;height:6px;background:#4ade80;border-radius:50%;display:inline-block;"></span>And {{ $andGlob }}%</span>
                <span style="font-size:10px;color:rgba(255,255,255,.4);display:flex;align-items:center;gap:3px;"><span style="width:6px;height:6px;background:#38bdf8;border-radius:50%;display:inline-block;"></span>Mac {{ $macGlob }}%</span>
            </div>
        </div>

        {{-- Windows --}}
        <div style="background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.3);border-radius:16px;padding:20px 22px;">
            <div style="width:32px;height:32px;background:rgba(124,58,237,.3);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#c4b5fd"><path d="M3 5.557L10.526 4.5V11.5H3V5.557zM11.474 4.357L21 3v8.5h-9.526V4.357zM3 12.5h7.526V19.5L3 18.443V12.5zM11.474 12.5H21V21l-9.526-1.357V12.5z"/></svg>
            </div>
            <div style="font-size:11px;font-weight:700;color:rgba(196,181,253,.6);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Windows</div>
            <div style="font-size:28px;font-weight:900;color:#c4b5fd;letter-spacing:-1px;line-height:1;">{{ number_format($totals['windows']) }}</div>
            <div style="margin-top:12px;background:rgba(255,255,255,.08);border-radius:4px;height:4px;">
                <div style="height:4px;background:#a78bfa;border-radius:4px;width:{{ $winGlob }}%;"></div>
            </div>
            <div style="font-size:11px;color:rgba(196,181,253,.5);margin-top:6px;">{{ $winGlob }}% of total</div>
        </div>

        {{-- Android --}}
        <div style="background:rgba(22,163,74,.15);border:1px solid rgba(22,163,74,.3);border-radius:16px;padding:20px 22px;">
            <div style="width:32px;height:32px;background:rgba(22,163,74,.3);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#86efac"><path d="M17.523 15.341A5 5 0 0022 10.5H2a5 5 0 004.477 4.841L5 21h2l.5-3h9l.5 3h2l-1.477-5.659zM8.5 8a1 1 0 110-2 1 1 0 010 2zm7 0a1 1 0 110-2 1 1 0 010 2zM6.5 5.5l-1.5-2.5M17.5 5.5L19 3"/></svg>
            </div>
            <div style="font-size:11px;font-weight:700;color:rgba(134,239,172,.6);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Android</div>
            <div style="font-size:28px;font-weight:900;color:#86efac;letter-spacing:-1px;line-height:1;">{{ number_format($totals['android']) }}</div>
            <div style="margin-top:12px;background:rgba(255,255,255,.08);border-radius:4px;height:4px;">
                <div style="height:4px;background:#4ade80;border-radius:4px;width:{{ $andGlob }}%;"></div>
            </div>
            <div style="font-size:11px;color:rgba(134,239,172,.5);margin-top:6px;">{{ $andGlob }}% of total</div>
        </div>

        {{-- Mac / iOS --}}
        <div style="background:rgba(8,145,178,.15);border:1px solid rgba(8,145,178,.3);border-radius:16px;padding:20px 22px;">
            <div style="width:32px;height:32px;background:rgba(8,145,178,.3);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#7dd3fc"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm-1 14.5v-5l-3 3-1.5-1.5L10 9.5V8h4v1.5l3.5 3.5-1.5 1.5-3-3v5h-2z"/></svg>
            </div>
            <div style="font-size:11px;font-weight:700;color:rgba(125,211,252,.6);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Mac / iOS</div>
            <div style="font-size:28px;font-weight:900;color:#7dd3fc;letter-spacing:-1px;line-height:1;">{{ number_format($totals['mac']) }}</div>
            <div style="margin-top:12px;background:rgba(255,255,255,.08);border-radius:4px;height:4px;">
                <div style="height:4px;background:#38bdf8;border-radius:4px;width:{{ $macGlob }}%;"></div>
            </div>
            <div style="font-size:11px;color:rgba(125,211,252,.5);margin-top:6px;">{{ $macGlob }}% of total</div>
        </div>

        {{-- Other OS --}}
        <div style="background:rgba(217,119,6,.15);border:1px solid rgba(217,119,6,.3);border-radius:16px;padding:20px 22px;">
            <div style="width:32px;height:32px;background:rgba(217,119,6,.3);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="16" height="16" fill="none" stroke="#fcd34d" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
            </div>
            <div style="font-size:11px;font-weight:700;color:rgba(252,211,77,.6);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Other OS</div>
            <div style="font-size:28px;font-weight:900;color:#fcd34d;letter-spacing:-1px;line-height:1;">{{ number_format($totals['other']) }}</div>
            <div style="margin-top:12px;background:rgba(255,255,255,.08);border-radius:4px;height:4px;">
                <div style="height:4px;background:#fbbf24;border-radius:4px;width:{{ $othGlob }}%;"></div>
            </div>
            <div style="font-size:11px;color:rgba(252,211,77,.5);margin-top:6px;">{{ $othGlob }}% of total</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     PUBLISHER TABLE
═══════════════════════════════════════════════ --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:20px;overflow:hidden;">

    {{-- Table header --}}
    <div style="padding:18px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:15px;font-weight:700;color:#111827;">Publisher Breakdown</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Sorted by total clicks · {{ $dateLabel }}</div>
        </div>
        <div style="display:flex;gap:16px;font-size:12px;font-weight:600;">
            <span style="display:flex;align-items:center;gap:5px;color:#7c3aed;"><span style="width:8px;height:8px;background:#7c3aed;border-radius:2px;display:inline-block;"></span>Windows</span>
            <span style="display:flex;align-items:center;gap:5px;color:#16a34a;"><span style="width:8px;height:8px;background:#16a34a;border-radius:2px;display:inline-block;"></span>Android</span>
            <span style="display:flex;align-items:center;gap:5px;color:#0891b2;"><span style="width:8px;height:8px;background:#0891b2;border-radius:2px;display:inline-block;"></span>Mac/iOS</span>
            <span style="display:flex;align-items:center;gap:5px;color:#d97706;"><span style="width:8px;height:8px;background:#d97706;border-radius:2px;display:inline-block;"></span>Other</span>
        </div>
    </div>

    @if($rows->isEmpty())
    <div style="text-align:center;padding:64px;color:#9ca3af;">
        <svg width="48" height="48" fill="none" stroke="#e5e7eb" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 16px;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <div style="font-size:15px;font-weight:600;color:#6b7280;margin-bottom:6px;">No activity for {{ $label }}</div>
    </div>
    @else

    {{-- Column headers --}}
    <div style="display:grid;grid-template-columns:52px 1fr 110px 110px 110px 110px 130px 80px;padding:10px 24px;background:#f9fafb;border-bottom:1px solid #f3f4f6;">
        <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;">#</div>
        <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;">Publisher</div>
        <div style="font-size:11px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:.06em;text-align:right;">Windows</div>
        <div style="font-size:11px;font-weight:700;color:#16a34a;text-transform:uppercase;letter-spacing:.06em;text-align:right;">Android</div>
        <div style="font-size:11px;font-weight:700;color:#0891b2;text-transform:uppercase;letter-spacing:.06em;text-align:right;">Mac/iOS</div>
        <div style="font-size:11px;font-weight:700;color:#d97706;text-transform:uppercase;letter-spacing:.06em;text-align:right;">Other</div>
        <div style="font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.06em;text-align:right;">Total</div>
        <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;text-align:center;">View</div>
    </div>

    @foreach($rows as $i => $row)
    @php
        $user    = $row['user'];
        $total   = $row['total'];
        $winPct  = $total > 0 ? round(($row['windows'] / $total) * 100) : 0;
        $andPct  = $total > 0 ? round(($row['android'] / $total) * 100) : 0;
        $macPct  = $total > 0 ? round(($row['mac']     / $total) * 100) : 0;
        $othPct  = $total > 0 ? round(($row['other']   / $total) * 100) : 0;
        // Share of total across all publishers
        $shareOfTotal = $grandTotal > 0 ? round(($total / $grandTotal) * 100) : 0;
        $grad = $avatarGradients[$i % count($avatarGradients)];
        $isTop = $i < 3;
    @endphp

    <div style="display:grid;grid-template-columns:52px 1fr 110px 110px 110px 110px 130px 80px;
                padding:16px 24px;border-bottom:1px solid #f9fafb;align-items:center;
                transition:background .12s;{{ $i === 0 ? 'background:linear-gradient(90deg,#fefce8,#fff);' : '' }}"
         onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='{{ $i === 0 ? 'linear-gradient(90deg,#fefce8,#fff)' : '' }}'">

        {{-- Rank --}}
        <div style="font-size:{{ $isTop ? '18px' : '13px' }};{{ $isTop ? '' : 'color:#9ca3af;font-weight:600;' }}">
            @if($isTop)
                {{ $medals[$i] }}
            @else
                {{ $i + 1 }}
            @endif
        </div>

        {{-- Publisher --}}
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:12px;background:{{ $grad }};display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 4px 10px rgba(0,0,0,.15);">
                {{ strtoupper(substr($user ? $user->name : 'D', 0, 1)) }}
            </div>
            <div style="min-width:0;">
                @if($user)
                <div style="font-weight:700;font-size:14px;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <a href="{{ route('admin.publishers.show', $user) }}" style="text-decoration:none;color:inherit;">{{ $user->name }}</a>
                </div>
                <div style="font-size:11px;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $user->email }}</div>
                @else
                <div style="font-size:13px;color:#9ca3af;">Deleted account</div>
                @endif
                {{-- Width bar = share of grand total --}}
                <div style="height:3px;background:#f3f4f6;border-radius:2px;margin-top:5px;width:100%;max-width:160px;">
                    <div style="height:3px;background:linear-gradient(90deg,#6366f1,#8b5cf6);border-radius:2px;width:{{ $shareOfTotal }}%;"></div>
                </div>
            </div>
        </div>

        {{-- Windows --}}
        <div style="text-align:right;">
            <div style="font-weight:700;font-size:14px;color:#7c3aed;">{{ number_format($row['windows']) }}</div>
            <div style="font-size:11px;color:#c4b5fd;margin-top:1px;">{{ $winPct }}%</div>
        </div>

        {{-- Android --}}
        <div style="text-align:right;">
            <div style="font-weight:700;font-size:14px;color:#16a34a;">{{ number_format($row['android']) }}</div>
            <div style="font-size:11px;color:#86efac;margin-top:1px;">{{ $andPct }}%</div>
        </div>

        {{-- Mac/iOS --}}
        <div style="text-align:right;">
            <div style="font-weight:700;font-size:14px;color:#0891b2;">{{ number_format($row['mac']) }}</div>
            <div style="font-size:11px;color:#7dd3fc;margin-top:1px;">{{ $macPct }}%</div>
        </div>

        {{-- Other --}}
        <div style="text-align:right;">
            <div style="font-weight:700;font-size:14px;color:#d97706;">{{ number_format($row['other']) }}</div>
            <div style="font-size:11px;color:#fcd34d;margin-top:1px;">{{ $othPct }}%</div>
        </div>

        {{-- Total + split bar --}}
        <div style="text-align:right;">
            <div style="font-weight:900;font-size:16px;color:#111827;">{{ number_format($total) }}</div>
            <div style="display:flex;gap:1px;margin-top:5px;height:5px;border-radius:4px;overflow:hidden;margin-left:auto;width:100%;">
                @if($winPct > 0)<div style="width:{{ $winPct }}%;background:#7c3aed;"></div>@endif
                @if($andPct > 0)<div style="width:{{ $andPct }}%;background:#16a34a;"></div>@endif
                @if($macPct > 0)<div style="width:{{ $macPct }}%;background:#0891b2;"></div>@endif
                @if($othPct > 0)<div style="width:{{ $othPct }}%;background:#d97706;"></div>@endif
            </div>
            <div style="font-size:10px;color:#9ca3af;margin-top:3px;">{{ $shareOfTotal }}% of all</div>
        </div>

        {{-- Action --}}
        <div style="text-align:center;">
            @if($user)
            <a href="{{ route('admin.publishers.show', $user) }}"
               style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;color:#374151;text-decoration:none;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;transition:all .15s;"
               onmouseover="this.style.background='#111827';this.style.color='#fff'"
               onmouseout="this.style.background='#f3f4f6';this.style.color='#374151'">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View
            </a>
            @endif
        </div>
    </div>
    @endforeach

    {{-- Totals footer --}}
    <div style="display:grid;grid-template-columns:52px 1fr 110px 110px 110px 110px 130px 80px;
                padding:16px 24px;background:linear-gradient(90deg,#f8fafc,#f1f5f9);align-items:center;border-top:2px solid #e5e7eb;">
        <div></div>
        <div style="font-size:13px;font-weight:800;color:#374151;letter-spacing:.01em;">TOTAL</div>
        <div style="text-align:right;font-weight:800;font-size:14px;color:#7c3aed;">{{ number_format($totals['windows']) }}</div>
        <div style="text-align:right;font-weight:800;font-size:14px;color:#16a34a;">{{ number_format($totals['android']) }}</div>
        <div style="text-align:right;font-weight:800;font-size:14px;color:#0891b2;">{{ number_format($totals['mac']) }}</div>
        <div style="text-align:right;font-weight:800;font-size:14px;color:#d97706;">{{ number_format($totals['other']) }}</div>
        <div style="text-align:right;font-weight:900;font-size:18px;color:#111827;">{{ number_format($totals['total']) }}</div>
        <div></div>
    </div>
    @endif
</div>

@endsection
