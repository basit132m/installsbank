@extends('layouts.advertiser')
@section('title', $softwareCampaign->name)
@section('page-title', $softwareCampaign->name)

@section('content')
<a href="{{ route('advertiser.software.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom:16px;">&larr; All software</a>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Installs Today</div><div style="font-size:30px;font-weight:800;color:#3b82f6;">{{ number_format($installsToday) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Total Installs</div><div style="font-size:30px;font-weight:800;">{{ number_format($installsTotal) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Counting</div><div style="font-size:20px;font-weight:800;">{{ $softwareCampaign->install_mode==='open'?'On open':'On install' }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Status</div><div style="font-size:20px;font-weight:800;">{!! $softwareCampaign->enabled ? '<span style="color:#059669;">Live</span>' : '<span style="color:#d97706;">Under review</span>' !!}</div></div>
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
