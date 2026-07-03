@extends('layouts.reseller')
@section('title', 'Websites Overview')
@section('page-title', 'Websites Overview')

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
        'linear-gradient(135deg,#7c3aed,#6d28d9)',
        'linear-gradient(135deg,#8b5cf6,#6366f1)',
        'linear-gradient(135deg,#a855f7,#7c3aed)',
        'linear-gradient(135deg,#6366f1,#8b5cf6)',
        'linear-gradient(135deg,#ec4899,#8b5cf6)',
    ];
@endphp

{{-- ═══ HERO STATS ═══ --}}
<div style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 50%,#1e1b4b 100%);border-radius:20px;padding:26px 30px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;background:rgba(139,92,246,.18);border-radius:50%;pointer-events:none;"></div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;position:relative;">
        <div style="display:flex;gap:6px;background:rgba(255,255,255,.08);padding:5px;border-radius:12px;flex-wrap:wrap;">
            @foreach(['today'=>'Today','yesterday'=>'Yesterday','7days'=>'Last 7 Days','28days'=>'Last 28 Days'] as $key=>$lbl)
                <a href="?period={{ $key }}"
                   style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;
                          {{ $period===$key ? 'background:#fff;color:#111827;box-shadow:0 2px 8px rgba(0,0,0,.15);' : 'color:rgba(255,255,255,.6);' }}">
                    {{ $lbl }}
                </a>
            @endforeach
        </div>
        <div style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.55);font-size:13px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span style="color:rgba(255,255,255,.85);font-weight:500;">{{ $dateLabel }}</span>
            <span style="color:rgba(255,255,255,.3);">·</span>
            <span>{{ $rows->count() }} website{{ $rows->count() !== 1 ? 's' : '' }} active</span>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr 1fr;gap:14px;position:relative;" class="heroGrid">
        {{-- Total --}}
        <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:18px 20px;">
            <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">Total Valid Clicks</div>
            <div style="font-size:34px;font-weight:900;color:#fff;letter-spacing:-1px;line-height:1;">{{ number_format($totals['total']) }}</div>
            <div style="display:flex;height:5px;border-radius:4px;overflow:hidden;margin-top:14px;gap:2px;">
                <div style="width:{{ $winGlob }}%;background:#a78bfa;"></div>
                <div style="width:{{ $andGlob }}%;background:#4ade80;"></div>
                <div style="width:{{ $macGlob }}%;background:#38bdf8;"></div>
                <div style="width:{{ $othGlob }}%;background:#fbbf24;"></div>
            </div>
        </div>
        {{-- Windows --}}
        <div style="background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.3);border-radius:16px;padding:18px 20px;">
            <div style="font-size:11px;font-weight:700;color:rgba(196,181,253,.6);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Windows</div>
            <div style="font-size:26px;font-weight:900;color:#c4b5fd;line-height:1;">{{ number_format($totals['windows']) }}</div>
            <div style="margin-top:10px;background:rgba(255,255,255,.08);border-radius:4px;height:4px;"><div style="height:4px;background:#a78bfa;border-radius:4px;width:{{ $winGlob }}%;"></div></div>
            <div style="font-size:11px;color:rgba(196,181,253,.5);margin-top:6px;">{{ $winGlob }}% of total</div>
        </div>
        {{-- Android --}}
        <div style="background:rgba(22,163,74,.15);border:1px solid rgba(22,163,74,.3);border-radius:16px;padding:18px 20px;">
            <div style="font-size:11px;font-weight:700;color:rgba(134,239,172,.6);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Android</div>
            <div style="font-size:26px;font-weight:900;color:#86efac;line-height:1;">{{ number_format($totals['android']) }}</div>
            <div style="margin-top:10px;background:rgba(255,255,255,.08);border-radius:4px;height:4px;"><div style="height:4px;background:#4ade80;border-radius:4px;width:{{ $andGlob }}%;"></div></div>
            <div style="font-size:11px;color:rgba(134,239,172,.5);margin-top:6px;">{{ $andGlob }}% of total</div>
        </div>
        {{-- Mac --}}
        <div style="background:rgba(8,145,178,.15);border:1px solid rgba(8,145,178,.3);border-radius:16px;padding:18px 20px;">
            <div style="font-size:11px;font-weight:700;color:rgba(125,211,252,.6);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Mac / iOS</div>
            <div style="font-size:26px;font-weight:900;color:#7dd3fc;line-height:1;">{{ number_format($totals['mac']) }}</div>
            <div style="margin-top:10px;background:rgba(255,255,255,.08);border-radius:4px;height:4px;"><div style="height:4px;background:#38bdf8;border-radius:4px;width:{{ $macGlob }}%;"></div></div>
            <div style="font-size:11px;color:rgba(125,211,252,.5);margin-top:6px;">{{ $macGlob }}% of total</div>
        </div>
        {{-- Other --}}
        <div style="background:rgba(217,119,6,.15);border:1px solid rgba(217,119,6,.3);border-radius:16px;padding:18px 20px;">
            <div style="font-size:11px;font-weight:700;color:rgba(252,211,77,.6);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Other OS</div>
            <div style="font-size:26px;font-weight:900;color:#fcd34d;line-height:1;">{{ number_format($totals['other']) }}</div>
            <div style="margin-top:10px;background:rgba(255,255,255,.08);border-radius:4px;height:4px;"><div style="height:4px;background:#fbbf24;border-radius:4px;width:{{ $othGlob }}%;"></div></div>
            <div style="font-size:11px;color:rgba(252,211,77,.5);margin-top:6px;">{{ $othGlob }}% of total</div>
        </div>
    </div>
</div>

{{-- ═══ TABLE ═══ --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:20px;overflow:hidden;">
    <div style="padding:18px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div>
            <div style="font-size:15px;font-weight:700;color:#111827;">Per-Website Breakdown</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Sorted by total valid clicks · {{ $dateLabel }}</div>
        </div>
    </div>

    @if($rows->isEmpty())
    <div style="text-align:center;padding:56px;color:#9ca3af;">
        <div style="font-size:40px;margin-bottom:10px;">📊</div>
        <div style="font-size:15px;font-weight:600;color:#6b7280;">No activity for {{ $label }}</div>
    </div>
    @else

    <div style="overflow-x:auto;">
    <div style="min-width:720px;">
    {{-- Column headers --}}
    <div style="display:grid;grid-template-columns:48px 1fr 100px 100px 100px 100px 120px;padding:10px 24px;background:#f9fafb;border-bottom:1px solid #f3f4f6;">
        <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">#</div>
        <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Website</div>
        <div style="font-size:11px;font-weight:700;color:#7c3aed;text-transform:uppercase;text-align:right;">Windows</div>
        <div style="font-size:11px;font-weight:700;color:#16a34a;text-transform:uppercase;text-align:right;">Android</div>
        <div style="font-size:11px;font-weight:700;color:#0891b2;text-transform:uppercase;text-align:right;">Mac/iOS</div>
        <div style="font-size:11px;font-weight:700;color:#d97706;text-transform:uppercase;text-align:right;">Other</div>
        <div style="font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;text-align:right;">Total</div>
    </div>

    @foreach($rows as $i => $row)
    @php
        $total  = $row['total'];
        $winPct = $total > 0 ? round(($row['windows'] / $total) * 100) : 0;
        $andPct = $total > 0 ? round(($row['android'] / $total) * 100) : 0;
        $macPct = $total > 0 ? round(($row['mac']     / $total) * 100) : 0;
        $othPct = $total > 0 ? round(($row['other']   / $total) * 100) : 0;
        $shareOfTotal = $grandTotal > 0 ? round(($total / $grandTotal) * 100) : 0;
        $grad = $avatarGradients[$i % count($avatarGradients)];
        $isTop = $i < 3;
    @endphp
    <div style="display:grid;grid-template-columns:48px 1fr 100px 100px 100px 100px 120px;padding:15px 24px;border-bottom:1px solid #f9fafb;align-items:center;{{ $i === 0 ? 'background:linear-gradient(90deg,#faf5ff,#fff);' : '' }}">
        <div style="font-size:{{ $isTop ? '18px' : '13px' }};{{ $isTop ? '' : 'color:#9ca3af;font-weight:600;' }}">
            {{ $isTop ? $medals[$i] : $i + 1 }}
        </div>
        <div style="display:flex;align-items:center;gap:12px;min-width:0;">
            <div style="width:38px;height:38px;border-radius:11px;background:{{ $grad }};display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr($row['name'], 0, 1)) }}
            </div>
            <div style="min-width:0;">
                <div style="font-weight:700;font-size:14px;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $row['name'] }}</div>
                <div style="font-size:11px;color:#9ca3af;display:flex;align-items:center;gap:6px;">
                    @if($row['code'])<code>{{ $row['code'] }}</code>@endif
                    @if(!$row['active'])<span style="color:#d97706;font-weight:700;">· paused</span>@endif
                </div>
                <div style="height:3px;background:#f3f4f6;border-radius:2px;margin-top:5px;max-width:160px;">
                    <div style="height:3px;background:linear-gradient(90deg,#7c3aed,#a855f7);border-radius:2px;width:{{ $shareOfTotal }}%;"></div>
                </div>
            </div>
        </div>
        <div style="text-align:right;"><div style="font-weight:700;font-size:14px;color:#7c3aed;">{{ number_format($row['windows']) }}</div><div style="font-size:11px;color:#c4b5fd;">{{ $winPct }}%</div></div>
        <div style="text-align:right;"><div style="font-weight:700;font-size:14px;color:#16a34a;">{{ number_format($row['android']) }}</div><div style="font-size:11px;color:#86efac;">{{ $andPct }}%</div></div>
        <div style="text-align:right;"><div style="font-weight:700;font-size:14px;color:#0891b2;">{{ number_format($row['mac']) }}</div><div style="font-size:11px;color:#7dd3fc;">{{ $macPct }}%</div></div>
        <div style="text-align:right;"><div style="font-weight:700;font-size:14px;color:#d97706;">{{ number_format($row['other']) }}</div><div style="font-size:11px;color:#fcd34d;">{{ $othPct }}%</div></div>
        <div style="text-align:right;"><div style="font-weight:900;font-size:16px;color:#111827;">{{ number_format($total) }}</div><div style="font-size:10px;color:#9ca3af;">{{ $shareOfTotal }}% of all</div></div>
    </div>
    @endforeach

    {{-- Footer totals --}}
    <div style="display:grid;grid-template-columns:48px 1fr 100px 100px 100px 100px 120px;padding:15px 24px;background:linear-gradient(90deg,#f8fafc,#f1f5f9);align-items:center;border-top:2px solid #e5e7eb;">
        <div></div>
        <div style="font-size:13px;font-weight:800;color:#374151;">TOTAL</div>
        <div style="text-align:right;font-weight:800;font-size:14px;color:#7c3aed;">{{ number_format($totals['windows']) }}</div>
        <div style="text-align:right;font-weight:800;font-size:14px;color:#16a34a;">{{ number_format($totals['android']) }}</div>
        <div style="text-align:right;font-weight:800;font-size:14px;color:#0891b2;">{{ number_format($totals['mac']) }}</div>
        <div style="text-align:right;font-weight:800;font-size:14px;color:#d97706;">{{ number_format($totals['other']) }}</div>
        <div style="text-align:right;font-weight:900;font-size:17px;color:#111827;">{{ number_format($totals['total']) }}</div>
    </div>
    </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>@media(max-width:820px){ .heroGrid { grid-template-columns:1fr 1fr !important; } }</style>
@endpush
