@extends('layouts.admin')
@section('title', 'Traffic Tiers')
@section('page-title', 'Traffic Tiers')

@section('content')
@php
    $tierMeta = [
        1 => [
            'label'    => 'Tier 1',
            'desc'     => 'US, GB, CA, AU, DE, FR, NL, SE, NO, DK',
            'accent'   => '#059669',
            'bg'       => '#f0fdf4',
            'border'   => '#bbf7d0',
            'badge_bg' => '#dcfce7',
            'badge_tx' => '#166534',
        ],
        2 => [
            'label'    => 'Tier 2',
            'desc'     => 'ES, IT, PT, PL, CZ, HU, RO, GR, TR, AE',
            'accent'   => '#d97706',
            'bg'       => '#fffbeb',
            'border'   => '#fde68a',
            'badge_bg' => '#fef3c7',
            'badge_tx' => '#92400e',
        ],
        3 => [
            'label'    => 'Tier 3',
            'desc'     => 'All other countries',
            'accent'   => '#6366f1',
            'bg'       => '#eef2ff',
            'border'   => '#c7d2fe',
            'badge_bg' => '#e0e7ff',
            'badge_tx' => '#3730a3',
        ],
    ];
@endphp

{{-- Total summary bar --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;">
    @foreach([1,2,3] as $t)
    @php
        $m       = $tierMeta[$t];
        $clicks  = $tiers[$t]['clicks'];
        $earn    = $tiers[$t]['earnings'];
        $pct     = $totalClicks > 0 ? round($clicks / $totalClicks * 100, 1) : 0;
        $cCount  = count($tiers[$t]['countries']);
    @endphp
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:22px 24px;box-shadow:0 1px 6px rgba(0,0,0,0.04);border-top:4px solid {{ $m['accent'] }};">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div>
                <div style="font-size:18px;font-weight:800;color:#111827;">{{ $m['label'] }}</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ $m['desc'] }}</div>
            </div>
            <div style="background:{{ $m['badge_bg'] }};color:{{ $m['badge_tx'] }};border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;">
                {{ $pct }}% of traffic
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
            <div style="background:{{ $m['bg'] }};border:1px solid {{ $m['border'] }};border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:20px;font-weight:800;color:{{ $m['accent'] }};">{{ number_format($clicks) }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:2px;font-weight:600;">Valid Clicks</div>
            </div>
            <div style="background:{{ $m['bg'] }};border:1px solid {{ $m['border'] }};border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:20px;font-weight:800;color:{{ $m['accent'] }};">${{ number_format($earn, 2) }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:2px;font-weight:600;">Earnings</div>
            </div>
            <div style="background:{{ $m['bg'] }};border:1px solid {{ $m['border'] }};border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:20px;font-weight:800;color:{{ $m['accent'] }};">{{ $cCount }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:2px;font-weight:600;">Countries</div>
            </div>
        </div>

        {{-- Mini bar chart showing contribution vs total --}}
        <div style="margin-top:14px;">
            <div style="height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $m['accent'] }};border-radius:3px;transition:width 0.4s;"></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Grand total strip --}}
<div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:14px 20px;margin-bottom:28px;display:flex;align-items:center;gap:32px;">
    <div style="font-size:13px;font-weight:700;color:#374151;">All-Time Totals</div>
    <div style="display:flex;gap:28px;">
        <div>
            <span style="font-size:13px;color:#6b7280;">Total Valid Clicks: </span>
            <span style="font-size:14px;font-weight:800;color:#111827;">{{ number_format($totalClicks) }}</span>
        </div>
        <div>
            <span style="font-size:13px;color:#6b7280;">Total Earnings: </span>
            <span style="font-size:14px;font-weight:800;color:#111827;">${{ number_format($totalEarnings, 2) }}</span>
        </div>
    </div>
    <div style="margin-left:auto;font-size:12px;color:#9ca3af;">Counted Windows clicks only · All time</div>
</div>

{{-- Per-tier country tables --}}
@foreach([1,2,3] as $t)
@php
    $m         = $tierMeta[$t];
    $countries = $tiers[$t]['countries'];
    $tierTotal = $tiers[$t]['clicks'];
@endphp
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px;box-shadow:0 1px 6px rgba(0,0,0,0.04);">
    {{-- Tier header --}}
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:14px;background:{{ $m['bg'] }};">
        <div style="width:4px;height:24px;background:{{ $m['accent'] }};border-radius:2px;flex-shrink:0;"></div>
        <div>
            <div style="font-size:15px;font-weight:800;color:#111827;">{{ $m['label'] }} Countries</div>
            <div style="font-size:12px;color:#6b7280;margin-top:1px;">{{ $m['desc'] }}</div>
        </div>
        <div style="margin-left:auto;display:flex;gap:16px;font-size:13px;">
            <span style="color:#6b7280;">{{ count($countries) }} countries</span>
            <span style="font-weight:700;color:{{ $m['accent'] }};">{{ number_format($tierTotal) }} clicks</span>
        </div>
    </div>

    @if(count($countries) === 0)
    <div style="text-align:center;padding:40px;color:#9ca3af;font-size:14px;">No traffic recorded for this tier yet.</div>
    @else
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid #f3f4f6;">
                    <th style="padding:10px 20px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;text-align:left;">#</th>
                    <th style="padding:10px 20px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;text-align:left;">Country</th>
                    <th style="padding:10px 20px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;text-align:right;">Valid Clicks</th>
                    <th style="padding:10px 20px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;text-align:right;">Earnings</th>
                    <th style="padding:10px 20px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;text-align:left;">Share of Tier</th>
                </tr>
            </thead>
            <tbody>
                @foreach($countries as $i => $c)
                @php $share = $tierTotal > 0 ? round($c->clicks / $tierTotal * 100, 1) : 0; @endphp
                <tr style="border-bottom:1px solid #f9fafb;">
                    <td style="padding:11px 20px;font-size:13px;color:#9ca3af;font-weight:600;">{{ $i + 1 }}</td>
                    <td style="padding:11px 20px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span style="font-size:12px;font-weight:700;background:{{ $m['badge_bg'] }};color:{{ $m['badge_tx'] }};padding:2px 8px;border-radius:6px;font-family:monospace;letter-spacing:0.05em;">
                                {{ $c->country_code }}
                            </span>
                            <span style="font-size:13px;font-weight:600;color:#111827;">{{ $c->country_name ?: $c->country_code }}</span>
                        </div>
                    </td>
                    <td style="padding:11px 20px;font-size:14px;font-weight:700;color:#111827;text-align:right;">
                        {{ number_format($c->clicks) }}
                    </td>
                    <td style="padding:11px 20px;font-size:13px;font-weight:600;color:#374151;text-align:right;">
                        ${{ number_format((float)$c->earnings, 4) }}
                    </td>
                    <td style="padding:11px 20px;min-width:160px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $share }}%;background:{{ $m['accent'] }};border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:700;color:#6b7280;white-space:nowrap;">{{ $share }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endforeach

@endsection
