@extends('layouts.advertiser')
@section('title', 'My Campaigns')
@section('page-title', 'My Campaigns')

@section('header-actions')
<a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> New Campaign</a>
@endsection

@section('content')

<div style="font-size:14px;color:#6b7280;margin-bottom:22px;">Upload your Windows software and track real installs — region, device and IP, per campaign.</div>

@forelse($campaigns as $c)
<div class="card mb-4">
    <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
        <div style="flex:1;min-width:220px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                <span style="font-size:16px;font-weight:700;">{{ $c->name }}</span>
                <span class="badge {{ $c->statusBadgeClass() }}">{{ $c->statusLabel() }}</span>
            </div>
            <div style="font-size:12px;color:#9ca3af;margin-bottom:6px;"><i class="fa-regular fa-file-lines"></i> {{ $c->payload_original_name }}</div>
            <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:#6b7280;">
                <span><i class="fa-solid fa-bullseye" style="color:#9ca3af;"></i> Target: <strong>{{ number_format($c->max_installs) }} installs</strong></span>
                <span><i class="fa-solid fa-tag" style="color:#9ca3af;"></i> Rate: <strong>${{ number_format($c->effectiveRate(), 2) }}</strong> / install</span>
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            <div style="font-size:24px;font-weight:800;color:#3b82f6;">{{ number_format($c->total_installs) }}</div>
            <div style="font-size:11px;color:#9ca3af;">of {{ number_format($c->max_installs) }} installs</div>
            <div style="margin-top:8px;">
                <a href="{{ route('advertiser.campaigns.show', $c) }}" class="btn btn-ghost btn-sm">View <i class="fa-solid fa-arrow-right"></i></a>
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
        <span><i class="fa-solid fa-triangle-exclamation"></i> Our team proposed <strong>${{ number_format($c->admin_rate, 2) }}</strong> / install. Review and approve to go live.</span>
        <a href="{{ route('advertiser.campaigns.show', $c) }}" class="btn btn-sm" style="background:#f59e0b;color:white;">Review <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    @elseif($c->status === 'pending_review')
    <div style="margin-top:14px;padding:12px 16px;background:#dbeafe;border-radius:8px;font-size:13px;color:#1e40af;">
        <i class="fa-regular fa-clock"></i> Under review — our team is checking your campaign and pricing. You'll be notified of the next step.
    </div>
    @endif
</div>
@empty
<div class="card" style="text-align:center;padding:56px 24px;">
    <div style="width:68px;height:68px;border-radius:16px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 14px;"><i class="fa-solid fa-compact-disc"></i></div>
    <div style="font-size:18px;font-weight:700;margin-bottom:8px;">No campaigns yet</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:24px;">Upload your Windows software to start getting installs</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Campaign</a>
</div>
@endforelse
@endsection
