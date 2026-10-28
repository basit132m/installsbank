@extends('layouts.advertiser')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@if(session('registered'))
<div class="alert alert-info mb-6" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #bfdbfe;color:#1e40af;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:12px;">
    <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div><strong>Welcome to Installs Bank!</strong> Create your first campaign to start getting installs.</div>
</div>
@endif
@if(session('success'))
<div class="alert alert-success mb-6">{{ session('success') }}</div>
@endif

{{-- Top stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Installs Today</div><div style="font-size:30px;font-weight:800;color:#3b82f6;">{{ number_format($installsToday) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Total Installs</div><div style="font-size:30px;font-weight:800;">{{ number_format($totalInstalls) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Remaining</div><div style="font-size:30px;font-weight:800;color:#059669;">{{ number_format(max(0, $totalTarget - $totalInstalls)) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Live Campaigns</div><div style="font-size:30px;font-weight:800;">{{ number_format($liveCount) }}</div></div>
</div>

{{-- Campaigns with progress --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:12px;">
    <div class="card-title" style="font-size:16px;">Your Campaigns</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary btn-sm">+ New Campaign</a>
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
<div class="card" style="text-align:center;padding:48px 24px;">
    <div style="font-size:44px;margin-bottom:10px;">💿</div>
    <div style="font-size:17px;font-weight:700;margin-bottom:6px;">No campaigns yet</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:20px;">Upload your Windows software to start getting installs.</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary">+ New Campaign</a>
</div>
@endforelse

{{-- Featured campaign breakdowns --}}
@if($activeCampaign && $activeCampaign->total_installs > 0)
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:8px;" class="sw-grid">
    <div class="card">
        <div class="card-title" style="margin-bottom:14px;">{{ $activeCampaign->name }} — by Region</div>
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
        <div class="card-title" style="margin-bottom:14px;">{{ $activeCampaign->name }} — by Device</div>
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
