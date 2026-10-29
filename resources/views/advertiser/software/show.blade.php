@extends('layouts.advertiser')
@section('title', $softwareCampaign->name)
@section('page-title', $softwareCampaign->name)

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
    <a href="{{ route('advertiser.campaigns.index') }}" class="btn btn-ghost btn-sm">&larr; All campaigns</a>
    @if($softwareCampaign->enabled)
    <form method="POST" action="{{ route('advertiser.campaigns.stop', $softwareCampaign) }}" onsubmit="return confirm('Stop this campaign? Traffic will be redirected to the fallback link until you resume.');">
        @csrf
        <button class="btn btn-sm" style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;"><i class="fa-solid fa-circle-stop"></i> Stop campaign</button>
    </form>
    @elseif($softwareCampaign->status === 'paused')
    <form method="POST" action="{{ route('advertiser.campaigns.start', $softwareCampaign) }}">
        @csrf
        <button class="btn btn-sm btn-primary"><i class="fa-solid fa-circle-play"></i> Resume campaign</button>
    </form>
    @endif
</div>

@if($softwareCampaign->status === 'paused' && !$softwareCampaign->enabled)
<div class="alert" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;margin-bottom:20px;padding:12px 16px;border-radius:8px;">
    ⏸ This campaign is <strong>stopped</strong>. Visitors are being redirected to the fallback link — no new installs are collected until you resume.
</div>
@endif

{{-- Rate offer banner --}}
@if($softwareCampaign->status === 'rate_offered')
<div class="card" style="margin-bottom:20px;border:1.5px solid #fde68a;background:#fffbeb;">
    <div style="font-size:15px;font-weight:700;color:#92400e;margin-bottom:6px;">Rate proposal — your approval needed</div>
    <div style="font-size:13px;color:#92400e;margin-bottom:14px;">
        You offered <strong>${{ number_format($softwareCampaign->offered_rate, 2) }}</strong> / install.
        Our team proposed <strong>${{ number_format($softwareCampaign->admin_rate, 2) }}</strong> / install.
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <form method="POST" action="{{ route('advertiser.campaigns.accept-rate', $softwareCampaign) }}">
            @csrf
            <button class="btn btn-primary btn-sm">Accept ${{ number_format($softwareCampaign->admin_rate, 2) }} / install</button>
        </form>
        <form method="POST" action="{{ route('advertiser.campaigns.reject-rate', $softwareCampaign) }}" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <input type="text" name="reason" class="form-control" placeholder="Reason (optional)" style="width:200px;height:34px;">
            <button class="btn btn-ghost btn-sm" style="color:#dc2626;">Decline</button>
        </form>
    </div>
</div>
@endif

{{-- Stat row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:16px;margin-bottom:24px;">
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Installs Today</div><div style="font-size:30px;font-weight:800;color:#3b82f6;">{{ number_format($installsToday) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Total Installs</div><div style="font-size:30px;font-weight:800;">{{ number_format($installsTotal) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Remaining</div><div style="font-size:30px;font-weight:800;color:#059669;">{{ number_format(max(0, $softwareCampaign->max_installs - $installsTotal)) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Rate / install</div><div style="font-size:24px;font-weight:800;">${{ number_format($softwareCampaign->effectiveRate(), 2) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Status</div><div style="font-size:18px;font-weight:800;">{{ $softwareCampaign->statusLabel() }}</div></div>
</div>

{{-- Target progress + increase --}}
<div class="card" style="margin-bottom:24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:12px;">
        <div class="card-title"><i class="fa-solid fa-bullseye"></i> Install Target</div>
        <div style="font-size:13px;color:#6b7280;">{{ number_format($installsTotal) }} / {{ number_format($softwareCampaign->max_installs) }} ({{ $softwareCampaign->progressPercent() }}%)</div>
    </div>
    <div style="background:#f3f4f6;border-radius:6px;height:10px;overflow:hidden;margin-bottom:16px;">
        <div style="height:100%;width:{{ $softwareCampaign->progressPercent() }}%;background:linear-gradient(90deg,#3b82f6,#01BF63);border-radius:6px;"></div>
    </div>
    <form method="POST" action="{{ route('advertiser.campaigns.increase-installs', $softwareCampaign) }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        @csrf
        <span style="font-size:13px;color:#6b7280;">Increase target to</span>
        <input type="number" name="max_installs" class="form-control" min="{{ $softwareCampaign->max_installs + 1 }}" value="{{ $softwareCampaign->max_installs + 100 }}" style="width:140px;">
        <button class="btn btn-ghost btn-sm">Update target</button>
    </form>
</div>

{{-- Billing --}}
@php
    $rate = $softwareCampaign->effectiveRate();
    $totalValue = $softwareCampaign->totalValue();
    $used = $softwareCampaign->valueDelivered();
    $balance = round($amountPaid - $used, 2);
    $prepaidInstalls = $softwareCampaign->installsCovered($amountPaid);
    $prepaidRemaining = $prepaidInstalls - $softwareCampaign->total_installs;
    $outstanding = max(0, round($totalValue - $amountPaid, 2));
@endphp
<div class="card" style="margin-bottom:24px;">
    <div class="card-title" style="margin-bottom:6px;"><i class="fa-solid fa-wallet"></i> Billing</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:16px;">
        You pay <strong>${{ number_format($rate, 2) }}</strong> per install. Payments are made directly to our team
        (outside this panel) — your balance here updates automatically as installs are delivered.
    </p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:18px;">
        <div style="background:#f9fafb;border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Campaign value</div>
            <div style="font-size:22px;font-weight:800;">${{ number_format($totalValue, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ number_format($softwareCampaign->max_installs) }} × ${{ number_format($rate, 2) }}</div>
        </div>
        <div style="background:#eff6ff;border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Advance paid</div>
            <div style="font-size:22px;font-weight:800;color:#3b82f6;">${{ number_format($amountPaid, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">covers {{ number_format($prepaidInstalls) }} installs</div>
        </div>
        <div style="background:#f9fafb;border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Delivered (used)</div>
            <div style="font-size:22px;font-weight:800;">−${{ number_format($used, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ number_format($softwareCampaign->total_installs) }} installs</div>
        </div>
        <div style="background:{{ $balance >= 0 ? '#d1fae5' : '#fee2e2' }};border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Balance</div>
            <div style="font-size:22px;font-weight:800;color:{{ $balance >= 0 ? '#065f46' : '#b91c1c' }};">${{ number_format($balance, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ $balance >= 0 ? number_format(max(0,$prepaidRemaining)).' installs left (prepaid)' : 'amount due' }}</div>
        </div>
    </div>

    @if($outstanding > 0)
    <div style="background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:8px;padding:12px 14px;font-size:13px;margin-bottom:8px;">
        To fully fund this campaign ({{ number_format($softwareCampaign->max_installs) }} installs), <strong>${{ number_format($outstanding, 2) }}</strong> is still to be paid. Send it to our team and we'll record it here.
    </div>
    @endif

    @if($payments->isNotEmpty())
    <div style="font-size:13px;font-weight:700;margin:14px 0 8px;">Payments received</div>
    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>Date</th><th>Amount</th><th>Note</th></tr></thead>
            <tbody>
                @foreach($payments as $p)
                <tr>
                    <td style="font-size:12px;">{{ $p->created_at->format('M d, Y') }}</td>
                    <td style="font-size:13px;font-weight:600;color:#059669;">+${{ number_format($p->amount, 2) }}</td>
                    <td style="font-size:12px;color:#6b7280;">{{ $p->note ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Geo targeting --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-title" style="margin-bottom:6px;"><i class="fa-solid fa-earth-americas"></i> Geo Targeting</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">Control which countries your installs come from. Changes apply to your links immediately.</p>
    <form method="POST" action="{{ route('advertiser.campaigns.geo', $softwareCampaign) }}">
        @csrf
        @include('advertiser.software._geo_fields', [
            'selectedMode'     => $softwareCampaign->geo_mode ?? 'mixed',
            'selectedExcluded' => $softwareCampaign->excluded_countries ?? [],
        ])
        <button class="btn btn-primary btn-sm">Save geo targeting</button>
        <span style="font-size:12px;color:#6b7280;margin-left:10px;">Currently: <strong>{{ $softwareCampaign->geoModeLabel() }}</strong>@if(!empty($softwareCampaign->excluded_countries)) · excluding {{ implode(', ', $softwareCampaign->excluded_countries) }}@endif</span>
    </form>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="sw-grid">
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;"><i class="fa-solid fa-earth-americas"></i> Installs by Region</div>
        @forelse($countryBreakdown as $row)
        @php $fc = strtolower($row->country_code ?? ''); @endphp
        <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
            <span style="display:flex;align-items:center;gap:8px;min-width:0;">
                @if($fc && $fc !== 'xx')
                <img src="https://flagcdn.com/24x18/{{ $fc }}.png" srcset="https://flagcdn.com/48x36/{{ $fc }}.png 2x" width="24" height="18" alt="" loading="lazy" style="border-radius:3px;box-shadow:0 0 0 1px rgba(0,0,0,.06);flex-shrink:0;">
                @else
                <span style="width:24px;text-align:center;flex-shrink:0;">🌐</span>
                @endif
                <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $row->country_name ?: 'Unknown' }}</span>
            </span>
            <strong style="flex-shrink:0;">{{ number_format($row->installs) }}</strong>
        </div>
        @empty
        <div style="color:#9ca3af;font-size:13px;">No installs yet</div>
        @endforelse
    </div>
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;"><i class="fa-solid fa-desktop"></i> Installs by Device</div>
        @forelse($deviceBreakdown as $row)
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
            <span>{{ ucfirst($row->device_type ?: 'desktop') }}</span>
            <strong>{{ number_format($row->installs) }}</strong>
        </div>
        @empty
        <div style="color:#9ca3af;font-size:13px;">No installs yet</div>
        @endforelse
    </div>
</div>

<div class="card">
    <div class="card-title" style="margin-bottom:16px;"><i class="fa-solid fa-list"></i> Install Log</div>
    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>When</th><th>Region</th><th>IP Address</th><th>Device</th><th>OS</th></tr></thead>
            <tbody>
                @php $cc = $softwareCampaign->countColumn(); @endphp
                @forelse($installs as $i)
                <tr>
                    <td style="font-size:12px;">{{ $i->{$cc}?->format('M d, H:i') ?? '—' }}</td>
                    @php $lc = strtolower($i->country_code ?? ''); @endphp
                    <td style="font-size:13px;"><span style="display:inline-flex;align-items:center;gap:6px;">@if($lc && $lc !== 'xx')<img src="https://flagcdn.com/24x18/{{ $lc }}.png" width="20" height="15" alt="" loading="lazy" style="border-radius:2px;box-shadow:0 0 0 1px rgba(0,0,0,.06);">@endif{{ $i->country_name ?: ($i->country_code ?: '—') }}</span></td>
                    <td style="font-size:12px;font-family:monospace;">{{ $i->ip_address ?: '—' }}</td>
                    <td style="font-size:13px;">{{ ucfirst($i->device_type ?: 'desktop') }}</td>
                    <td style="font-size:12px;">{{ trim(($i->os ?: '') . ' ' . ($i->os_version ?: '')) ?: '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:32px;color:#9ca3af;">No installs recorded yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $installs->links() }}</div>
</div>

{{-- Danger zone --}}
<div class="card" style="margin-top:24px;border:1px solid #fecaca;">
    <div class="card-title" style="color:#b91c1c;margin-bottom:6px;"><i class="fa-solid fa-triangle-exclamation"></i> Danger Zone</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">Permanently delete this campaign along with its installs, links and billing records. This cannot be undone.</p>
    <form method="POST" action="{{ route('advertiser.campaigns.destroy', $softwareCampaign) }}"
          onsubmit="return confirm('Delete “{{ $softwareCampaign->name }}” permanently?\n\nThis removes the campaign and ALL its data (installs, links, payment records) and cannot be undone.');">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm" style="background:#dc2626;color:#fff;"><i class="fa-solid fa-trash"></i> Delete campaign</button>
    </form>
</div>

<style>@media(max-width:860px){.sw-grid{grid-template-columns:1fr !important;}}</style>
@endsection
