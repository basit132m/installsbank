@extends('layouts.advertiser')
@section('title', 'My Campaigns')
@section('page-title', 'My Campaigns')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div style="font-size:14px;color:#6b7280;">Upload your Windows software and track real installs — region, device and IP, per campaign.</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary">+ New Campaign</a>
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
    <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
        <div style="flex:1;min-width:220px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                <span style="font-size:16px;font-weight:700;">{{ $c->name }}</span>
                <span style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;">{{ $c->statusLabel() }}</span>
            </div>
            <div style="font-size:12px;color:#9ca3af;margin-bottom:4px;">{{ $c->payload_original_name }}</div>
            <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:#6b7280;">
                <span>Target: <strong>{{ number_format($c->max_installs) }} installs</strong></span>
                <span>Rate: <strong>${{ number_format($c->effectiveRate(), 2) }}</strong> / install</span>
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            <div style="font-size:24px;font-weight:800;color:#3b82f6;">{{ number_format($c->total_installs) }}</div>
            <div style="font-size:11px;color:#9ca3af;">of {{ number_format($c->max_installs) }} installs</div>
            <div style="margin-top:8px;">
                <a href="{{ route('advertiser.campaigns.show', $c) }}" class="btn btn-ghost btn-sm">View →</a>
            </div>
        </div>
    </div>

    {{-- Progress --}}
    <div style="margin-top:16px;">
        <div style="display:flex;justify-content:space-between;font-size:12px;color:#9ca3af;margin-bottom:6px;">
            <span>{{ $c->progressPercent() }}% delivered</span>
            <span>{{ number_format(max(0, $c->max_installs - $c->total_installs)) }} remaining</span>
        </div>
        <div style="background:#f3f4f6;border-radius:6px;height:8px;overflow:hidden;">
            <div style="height:100%;width:{{ $c->progressPercent() }}%;background:linear-gradient(90deg,#3b82f6,#01BF63);border-radius:6px;transition:width 0.5s;"></div>
        </div>
    </div>

    @if($c->status === 'rate_offered')
    <div style="margin-top:14px;padding:12px 16px;background:#fef3c7;border-radius:8px;font-size:13px;color:#92400e;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <span>⚠ Our team proposed <strong>${{ number_format($c->admin_rate, 2) }}</strong> / install. Review and approve to go live.</span>
        <a href="{{ route('advertiser.campaigns.show', $c) }}" class="btn btn-sm" style="background:#f59e0b;color:white;">Review →</a>
    </div>
    @elseif($c->status === 'pending_review')
    <div style="margin-top:14px;padding:12px 16px;background:#dbeafe;border-radius:8px;font-size:13px;color:#1e40af;">
        ⏳ Under review — our team is checking your campaign and pricing. You'll be notified of the next step.
    </div>
    @endif
</div>
@empty
<div class="card" style="text-align:center;padding:56px 24px;">
    <div style="font-size:48px;margin-bottom:12px;">💿</div>
    <div style="font-size:18px;font-weight:700;margin-bottom:8px;">No campaigns yet</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:24px;">Upload your Windows software to start getting installs</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary">+ New Campaign</a>
</div>
@endforelse
@endsection
