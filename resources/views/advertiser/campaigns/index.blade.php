@extends('layouts.advertiser')
@section('title', 'My Campaigns')
@section('page-title', 'My Campaigns')

@section('content')
@if(session('success'))
<div class="alert alert-success mb-6">{{ session('success') }}</div>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <div style="font-size:14px;color:#6b7280;">Manage your advertising campaigns and track performance</div>
    </div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary">+ New Campaign</a>
</div>

@forelse($campaigns as $campaign)
@php $statusColors = ['draft'=>['bg'=>'#f3f4f6','text'=>'#374151'],'pending_payment'=>['bg'=>'#fef3c7','text'=>'#92400e'],'active'=>['bg'=>'#d1fae5','text'=>'#065f46'],'paused'=>['bg'=>'#dbeafe','text'=>'#1e40af'],'completed'=>['bg'=>'#f3f4f6','text'=>'#374151'],'cancelled'=>['bg'=>'#fee2e2','text'=>'#991b1b']]; $sc=$statusColors[$campaign->status]??['bg'=>'#f3f4f6','text'=>'#374151']; @endphp
<div class="card mb-4">
    <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
        <div style="flex:1;min-width:220px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                <span style="font-size:16px;font-weight:700;">{{ $campaign->name }}</span>
                <span style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;">{{ ucfirst(str_replace('_',' ',$campaign->status)) }}</span>
            </div>
            <div style="font-size:12px;color:#6b7280;margin-bottom:8px;">
                <a href="{{ $campaign->destination_url }}" target="_blank" style="color:#3b82f6;">{{ $campaign->destination_url }}</a>
            </div>
            <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:#6b7280;">
                <span>Contract: <strong>{{ $campaign->contract_type === 'fixed_rate' ? 'Fixed Rate' : 'Per Click' }}</strong></span>
                <span>Target: <strong>{{ number_format($campaign->target_clicks) }} clicks</strong></span>
                @if($campaign->total_value > 0)
                <span>Value: <strong>${{ number_format($campaign->total_value, 2) }}</strong></span>
                @endif
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            <div style="font-size:24px;font-weight:800;color:#3b82f6;">{{ number_format($campaign->delivered_clicks) }}</div>
            <div style="font-size:11px;color:#9ca3af;">clicks delivered</div>
            <div style="margin-top:8px;">
                <a href="{{ route('advertiser.campaigns.show', $campaign) }}" class="btn btn-ghost btn-sm">View Details →</a>
            </div>
        </div>
    </div>

    <!-- Progress bar -->
    <div style="margin-top:16px;">
        <div style="display:flex;justify-content:space-between;font-size:12px;color:#9ca3af;margin-bottom:6px;">
            <span>{{ $campaign->progressPercent() }}% delivered</span>
            <span>{{ number_format($campaign->remainingClicks()) }} remaining</span>
        </div>
        <div style="background:#f3f4f6;border-radius:6px;height:8px;overflow:hidden;">
            <div style="height:100%;width:{{ $campaign->progressPercent() }}%;background:linear-gradient(90deg,#3b82f6,#01BF63);border-radius:6px;transition:width 0.5s;"></div>
        </div>
    </div>

    @if($campaign->status === 'pending_payment')
    <div style="margin-top:14px;padding:12px 16px;background:#fef3c7;border-radius:8px;font-size:13px;color:#92400e;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <span>⚠ Advance payment required: <strong>${{ number_format($campaign->advance_amount, 2) }}</strong> (50% of ${{{ number_format($campaign->total_value, 2) }}})</span>
        <a href="{{ route('advertiser.campaigns.show', $campaign) }}" class="btn btn-sm" style="background:#f59e0b;color:white;">Pay Now →</a>
    </div>
    @endif

    @if($campaign->status === 'draft')
    <div style="margin-top:14px;padding:12px 16px;background:#dbeafe;border-radius:8px;font-size:13px;color:#1e40af;">
        ⏳ Under review — Admin is setting rates for your campaign. You'll be notified once ready for payment.
    </div>
    @endif
</div>
@empty
<div class="card" style="text-align:center;padding:56px 24px;">
    <div style="font-size:48px;margin-bottom:12px;">📢</div>
    <div style="font-size:18px;font-weight:700;margin-bottom:8px;">No campaigns yet</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:24px;">Create your first campaign to start driving installs</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary">+ Create Campaign</a>
</div>
@endforelse
@endsection
