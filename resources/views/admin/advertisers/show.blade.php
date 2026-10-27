@extends('layouts.admin')
@section('title', $user->name)
@section('page-title', $user->name)

@section('content')

<div style="display:flex;gap:8px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('admin.advertisers.index') }}" class="btn btn-ghost btn-sm">← Back</a>
    <span class="badge {{ $user->status==='active'?'badge-success':($user->status==='suspended'?'badge-danger':'badge-warning') }}">{{ ucfirst($user->status) }}</span>
    @if($user->status==='active')
        <form method="POST" action="{{ route('admin.advertisers.suspend', $user) }}" style="display:inline;">@csrf<button class="btn btn-danger btn-sm">Suspend</button></form>
    @else
        <form method="POST" action="{{ route('admin.advertisers.activate', $user) }}" style="display:inline;">@csrf<button class="btn btn-success btn-sm">Activate</button></form>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="card">
        <div class="card-title mb-4">Advertiser Information</div>
        @php $p = $user->advertiserProfile; @endphp
        <table style="width:100%;">
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;width:40%;">Email</td><td>{{ $user->email }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Company</td><td>{{ $p?->company_name ?? '—' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Website</td><td>{{ $p?->website ?? '—' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Telegram</td><td>{{ $p?->telegram ?? '—' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">WhatsApp</td><td>{{ $p?->whatsapp ?? '—' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Phone</td><td>{{ $user->phone ?? '—' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Joined</td><td>{{ $user->created_at->format('M d, Y') }}</td></tr>
        </table>
    </div>
    <div class="card">
        <div class="card-title mb-4">Account Summary</div>
        @php
            $totalInstalls = $campaigns->sum('total_installs');
            $estValue = $campaigns->sum(fn($c) => $c->total_installs * $c->effectiveRate());
        @endphp
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="background:#eff6ff;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:22px;font-weight:800;color:#3b82f6;">{{ number_format($totalInstalls) }}</div>
                <div style="font-size:12px;color:#6b7280;">Total Installs</div>
            </div>
            <div style="background:#f9fafb;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:22px;font-weight:800;">${{ number_format($estValue, 2) }}</div>
                <div style="font-size:12px;color:#6b7280;">Est. Install Value</div>
            </div>
            <div style="background:#f9fafb;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:22px;font-weight:800;">{{ $campaigns->count() }}</div>
                <div style="font-size:12px;color:#6b7280;">Total Campaigns</div>
            </div>
            <div style="background:#d1fae5;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:22px;font-weight:800;color:#065f46;">{{ $campaigns->where('status','live')->count() }}</div>
                <div style="font-size:12px;color:#6b7280;">Live</div>
            </div>
        </div>
    </div>
</div>

<!-- Campaigns -->
<div class="card">
    <div class="flex-between mb-4">
        <div class="card-title">Install Campaigns</div>
        <a href="{{ route('admin.software-campaigns.index') }}" class="btn btn-ghost btn-sm">View All Campaigns →</a>
    </div>
    @forelse($campaigns as $c)
    @php
        $live = $c->status === 'live';
        $needs = in_array($c->status, ['pending_review','approved']);
    @endphp
    <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f3f4f6;flex-wrap:wrap;">
        <div style="flex:1;min-width:180px;">
            <div style="font-size:13px;font-weight:600;">{{ $c->name }}</div>
            <div style="font-size:11px;color:#9ca3af;">${{ number_format($c->effectiveRate(), 2) }} / install · {{ $c->payload_original_name ?: 'no installer' }}</div>
        </div>
        <span class="badge {{ $live ? 'badge-success' : ($needs ? 'badge-warning' : 'badge-info') }}">{{ $c->statusLabel() }}</span>
        <div style="font-size:13px;color:#6b7280;">{{ number_format($c->total_installs) }} / {{ number_format($c->max_installs) }}</div>
        <a href="{{ route('admin.software-campaigns.show', $c) }}" class="btn btn-ghost btn-sm">Manage</a>
    </div>
    @empty
    <div style="text-align:center;padding:24px;color:#9ca3af;">No campaigns yet</div>
    @endforelse
</div>
@endsection
