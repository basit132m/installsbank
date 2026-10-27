@extends('layouts.advertiser')
@section('title', $softwareCampaign->name)
@section('page-title', $softwareCampaign->name)

@section('content')
@if(session('success'))
<div class="alert alert-success mb-6">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;margin-bottom:20px;padding:12px 16px;border-radius:8px;">{{ session('error') }}</div>
@endif

<a href="{{ route('advertiser.campaigns.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom:16px;">&larr; All campaigns</a>

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
        <div class="card-title">Install Target</div>
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

{{-- Geo targeting --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-title" style="margin-bottom:6px;">Geo Targeting</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">Control which countries your installs come from. Changes apply to your links immediately.</p>
    <form method="POST" action="{{ route('advertiser.campaigns.geo', $softwareCampaign) }}">
        @csrf
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;margin-bottom:14px;">
            @foreach($geoModes as $val => $label)
            <label style="border:2px solid {{ $softwareCampaign->geo_mode===$val ? '#3b82f6' : '#e5e7eb' }};border-radius:10px;padding:12px;cursor:pointer;display:flex;align-items:center;gap:8px;font-size:13px;">
                <input type="radio" name="geo_mode" value="{{ $val }}" {{ $softwareCampaign->geo_mode===$val?'checked':'' }}>
                <strong>{{ $label }}</strong>
            </label>
            @endforeach
        </div>
        <label class="form-label" style="font-size:13px;">Excluded countries</label>
        <select name="excluded_countries[]" multiple size="6" class="form-control" style="height:auto;margin-bottom:12px;">
            @php $excl = $softwareCampaign->excluded_countries ?? []; @endphp
            @foreach($countryGroups as $group => $list)
            <optgroup label="{{ $group }}">
                @foreach($list as $code => $cname)
                <option value="{{ $code }}" {{ in_array($code, $excl) ? 'selected' : '' }}>{{ $cname }} ({{ $code }})</option>
                @endforeach
            </optgroup>
            @endforeach
        </select>
        <button class="btn btn-primary btn-sm">Save geo targeting</button>
        <span style="font-size:12px;color:#6b7280;margin-left:10px;">Currently: <strong>{{ $softwareCampaign->geoModeLabel() }}</strong>@if(!empty($excl)) · excluding {{ implode(', ', $excl) }}@endif</span>
    </form>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="sw-grid">
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Installs by Region</div>
        @forelse($countryBreakdown as $row)
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
            <span>{{ $row->country_name ?: 'Unknown' }}</span>
            <strong>{{ number_format($row->installs) }}</strong>
        </div>
        @empty
        <div style="color:#9ca3af;font-size:13px;">No installs yet</div>
        @endforelse
    </div>
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Installs by Device</div>
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
    <div class="card-title" style="margin-bottom:16px;">Install Log</div>
    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>When</th><th>Region</th><th>IP Address</th><th>Device</th><th>OS</th></tr></thead>
            <tbody>
                @php $cc = $softwareCampaign->countColumn(); @endphp
                @forelse($installs as $i)
                <tr>
                    <td style="font-size:12px;">{{ $i->{$cc}?->format('M d, H:i') ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $i->country_name ?: ($i->country_code ?: '—') }}</td>
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

<style>@media(max-width:860px){.sw-grid{grid-template-columns:1fr !important;}}</style>
@endsection
