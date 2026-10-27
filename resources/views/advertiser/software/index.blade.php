@extends('layouts.advertiser')
@section('title', 'Software Installs')
@section('page-title', 'Software Installs')

@section('content')
@if(session('success'))
<div class="alert alert-success mb-6">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;margin-bottom:20px;padding:12px 16px;border-radius:8px;">{{ session('error') }}</div>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div style="font-size:14px;color:#6b7280;">Upload your Windows software and track real installs — region, device and IP, per campaign.</div>
    <a href="{{ route('advertiser.software.create') }}" class="btn btn-primary">+ Upload Software</a>
</div>

@forelse($campaigns as $c)
<div class="card mb-4">
    <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
        <div style="flex:1;min-width:220px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                <span style="font-size:16px;font-weight:700;">{{ $c->name }}</span>
                <span style="background:{{ $c->enabled?'#d1fae5':'#fef3c7' }};color:{{ $c->enabled?'#065f46':'#92400e' }};font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;">{{ $c->enabled?'Live':'Under review' }}</span>
                <span style="background:#eff6ff;color:#1e40af;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;">{{ $c->install_mode==='open'?'Counts on open':'Counts on install' }}</span>
            </div>
            <div style="font-size:12px;color:#9ca3af;">{{ $c->payload_original_name }} · uploaded {{ $c->created_at->format('M d, Y') }}</div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            <div style="font-size:24px;font-weight:800;color:#3b82f6;">{{ number_format($c->installs_count) }}</div>
            <div style="font-size:11px;color:#9ca3af;">installs tracked</div>
            <div style="margin-top:8px;">
                <a href="{{ route('advertiser.software.show', $c) }}" class="btn btn-ghost btn-sm">View Installs →</a>
            </div>
        </div>
    </div>
    @unless($c->enabled)
    <div style="margin-top:14px;padding:12px 16px;background:#dbeafe;border-radius:8px;font-size:13px;color:#1e40af;">
        ⏳ Under review — our team is preparing your install campaign. You'll see installs here once it goes live.
    </div>
    @endunless
</div>
@empty
<div class="card" style="text-align:center;padding:56px 24px;">
    <div style="font-size:48px;margin-bottom:12px;">💿</div>
    <div style="font-size:18px;font-weight:700;margin-bottom:8px;">No software uploaded yet</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:24px;">Upload your Windows .exe to start tracking installs</div>
    <a href="{{ route('advertiser.software.create') }}" class="btn btn-primary">+ Upload Software</a>
</div>
@endforelse
@endsection
