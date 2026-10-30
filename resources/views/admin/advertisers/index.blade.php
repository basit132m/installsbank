@extends('layouts.admin')
@section('title', 'Advertisers')
@section('page-title', 'Advertisers')

@section('content')

{{-- Summary --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fa-solid fa-user-tie"></i></div>
        <div class="stat-value">{{ number_format($stats['total']) }}</div>
        <div class="stat-label">Total Advertisers</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6faf2;color:#059669;"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-value">{{ number_format($stats['active']) }}</div>
        <div class="stat-label">Active</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-bullhorn"></i></div>
        <div class="stat-value">{{ number_format($stats['campaigns']) }}</div>
        <div class="stat-label">Install Campaigns</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3e8ff;color:#7c3aed;"><i class="fa-solid fa-download"></i></div>
        <div class="stat-value">{{ number_format($stats['installs']) }}</div>
        <div class="stat-label">Total Installs</div>
    </div>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div class="card-title"><i class="fa-solid fa-users" style="color:#3b82f6;"></i> All Advertisers</div>
        <form method="GET" style="display:flex;gap:8px;">
            <div style="position:relative;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:13px;"></i>
                <input type="text" name="search" class="form-control" style="width:240px;padding-left:34px;" value="{{ request('search') }}" placeholder="Search name or email…">
            </div>
            <button class="btn btn-ghost btn-sm">Search</button>
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>Advertiser</th><th>Company</th><th>Contact</th><th>Campaigns</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($advertisers as $adv)
                @php
                    $profile = $adv->advertiserProfile;
                    $statusCls = $adv->status==='active' ? 'badge-success' : ($adv->status==='suspended' ? 'badge-danger' : 'badge-warning');
                    $palette = ['#3b82f6','#8b5cf6','#06b6d4','#ec4899','#f59e0b','#10b981'];
                    $color = $palette[$adv->id % count($palette)];
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:40px;height:40px;border-radius:50%;background:{{ $color }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;">{{ strtoupper(substr($adv->name, 0, 1)) }}</div>
                            <div style="min-width:0;">
                                <div style="font-weight:600;">{{ $adv->name }}</div>
                                <div style="font-size:12px;color:#9ca3af;">{{ $adv->email }}</div>
                                <div style="font-size:11px;color:#b6bcc6;"><i class="fa-regular fa-calendar"></i> Joined {{ $adv->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;">{{ $profile?->company_name ?? '—' }}</td>
                    <td style="font-size:12px;color:#4b5563;">
                        @if($profile?->telegram)<div><i class="fa-brands fa-telegram" style="color:#0088cc;"></i> {{ $profile->telegram }}</div>@endif
                        @if($profile?->whatsapp)<div style="margin-top:2px;"><i class="fa-brands fa-whatsapp" style="color:#25d366;"></i> {{ $profile->whatsapp }}</div>@endif
                        @if(!$profile?->telegram && !$profile?->whatsapp)<span style="color:#9ca3af;">—</span>@endif
                    </td>
                    <td><span style="display:inline-flex;align-items:center;gap:6px;font-weight:600;"><i class="fa-solid fa-bullhorn" style="color:#9ca3af;font-size:12px;"></i> {{ $adv->software_campaigns_count }}</span></td>
                    <td><span class="badge {{ $statusCls }}">{{ ucfirst($adv->status) }}</span></td>
                    <td><a href="{{ route('admin.advertisers.show', $adv) }}" class="btn btn-ghost btn-sm">View <i class="fa-solid fa-arrow-right"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;"><i class="fa-regular fa-folder-open" style="font-size:26px;display:block;margin-bottom:8px;"></i> No advertisers yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $advertisers->links() }}</div>
</div>
@endsection
