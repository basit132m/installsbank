@extends('layouts.advertiser')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('header-actions')
<a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> New Campaign</a>
@endsection

@section('content')
@if(session('registered'))
<div class="alert alert-info mb-6" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
    <i class="fa-solid fa-circle-check"></i>
    <span><strong>Welcome to Installs Bank!</strong> Create your first campaign to start getting installs.</span>
</div>
@endif

{{-- Account balance --}}
<div class="card mb-6" style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;background:linear-gradient(135deg,#ecfdf5,#ffffff);border-color:#a7f3d0;">
    <div style="display:flex;align-items:center;gap:15px;">
        <div style="width:54px;height:54px;border-radius:14px;background:linear-gradient(135deg,var(--primary),#00d873);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;box-shadow:0 4px 12px rgba(1,191,99,.28);"><i class="fa-solid fa-wallet"></i></div>
        <div>
            <div style="font-size:13px;color:#6b7280;">Account Balance</div>
            <div style="font-size:32px;font-weight:800;line-height:1.1;color:{{ $balance >= 0 ? '#059669' : '#b91c1c' }};">${{ number_format($balance, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Decreases automatically as installs are delivered.</div>
        </div>
    </div>
    <div style="display:flex;gap:26px;flex-wrap:wrap;">
        <div>
            <div style="font-size:12px;color:#9ca3af;"><i class="fa-solid fa-arrow-down" style="color:#059669;"></i> Total paid</div>
            <div style="font-size:19px;font-weight:800;">${{ number_format($totalPaid, 2) }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:#9ca3af;"><i class="fa-solid fa-arrow-up" style="color:#9a3412;"></i> Used (delivered)</div>
            <div style="font-size:19px;font-weight:800;color:#9a3412;">−${{ number_format($totalUsed, 2) }}</div>
        </div>
    </div>
</div>

{{-- Top stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fa-solid fa-download"></i></div>
        <div class="stat-value" style="color:#3b82f6;">{{ number_format($installsToday) }}</div>
        <div class="stat-label">Installs Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3f4f6;color:#374151;"><i class="fa-solid fa-chart-simple"></i></div>
        <div class="stat-value">{{ number_format($totalInstalls) }}</div>
        <div class="stat-label">Total Installs</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6faf2;color:#059669;"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="stat-value" style="color:#059669;">{{ number_format(max(0, $totalTarget - $totalInstalls)) }}</div>
        <div class="stat-label">Remaining</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-bullhorn"></i></div>
        <div class="stat-value">{{ number_format($liveCount) }}</div>
        <div class="stat-label">Live Campaigns</div>
    </div>
</div>

{{-- Campaigns with progress --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:12px;">
    <div class="card-title" style="font-size:16px;"><i class="fa-solid fa-bullhorn"></i> Your Campaigns</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> New Campaign</a>
</div>

@forelse($campaigns as $c)
@php
    $statusColors = [
        'pending_review' => ['bg'=>'#dbeafe','text'=>'#1e40af'],
        'rate_offered'   => ['bg'=>'#fef3c7','text'=>'#92400e'],
        'approved'       => ['bg'=>'#e0e7ff','text'=>'#3730a3'],
        'live'           => ['bg'=>'#d1fae5','text'=>'#065f46'],
        'paused'         => ['bg'=>'#f3f4f6','text'=>'#374151'],
        'completed'      => ['bg'=>'#f3f4f6','text'=>'#374151'],
    ];
    $sc = $statusColors[$c->status] ?? ['bg'=>'#f3f4f6','text'=>'#374151'];
@endphp
<div class="card mb-4">
    <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;flex-wrap:wrap;">
                <span style="font-size:15px;font-weight:700;">{{ $c->name }}</span>
                <span style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;">{{ $c->statusLabel() }}</span>
            </div>
            <div style="font-size:12px;color:#6b7280;">${{ number_format($c->effectiveRate(), 2) }} / install · target {{ number_format($c->max_installs) }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:22px;font-weight:800;color:#3b82f6;">{{ number_format($c->total_installs) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ number_format(max(0, $c->max_installs - $c->total_installs)) }} remaining</div>
        </div>
        <a href="{{ route('advertiser.campaigns.show', $c) }}" class="btn btn-ghost btn-sm">View →</a>
    </div>
    <div style="margin-top:14px;">
        <div style="background:#f3f4f6;border-radius:6px;height:8px;overflow:hidden;">
            <div style="height:100%;width:{{ $c->progressPercent() }}%;background:linear-gradient(90deg,#3b82f6,#01BF63);border-radius:6px;"></div>
        </div>
    </div>
</div>
@empty
<div class="card" style="text-align:center;padding:52px 24px;">
    <div style="width:64px;height:64px;border-radius:16px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 14px;"><i class="fa-solid fa-compact-disc"></i></div>
    <div style="font-size:17px;font-weight:700;margin-bottom:6px;">No campaigns yet</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:20px;">Upload your Windows software to start getting installs.</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Campaign</a>
</div>
@endforelse

{{-- Featured campaign breakdowns --}}
@if($activeCampaign && $activeCampaign->total_installs > 0)
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:8px;" class="sw-grid">
    <div class="card">
        <div class="card-title" style="margin-bottom:14px;"><i class="fa-solid fa-earth-americas"></i> {{ $activeCampaign->name }} — by Region</div>
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
        <div class="card-title" style="margin-bottom:14px;"><i class="fa-solid fa-desktop"></i> {{ $activeCampaign->name }} — by Device</div>
        @forelse($deviceBreakdown as $row)
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
            <span>{{ ucfirst($row->device_type ?: 'desktop') }}</span><strong>{{ number_format($row->installs) }}</strong>
        </div>
        @empty
        <div style="color:#9ca3af;font-size:13px;">No installs yet</div>
        @endforelse
    </div>
</div>
@endif

<style>@media(max-width:860px){.sw-grid{grid-template-columns:1fr !important;}}</style>
@endsection
