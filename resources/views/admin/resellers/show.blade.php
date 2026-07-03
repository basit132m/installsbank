@extends('layouts.admin')
@section('title', $user->name . ' — Reseller')
@section('page-title', 'Reseller Details')

@section('content')

{{-- ═══ HERO ═══ --}}
<div style="background:linear-gradient(135deg,#1e1b4b,#312e81);border-radius:16px;padding:26px 28px;margin-bottom:24px;">
    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
        <div style="width:52px;height:52px;background:rgba(255,255,255,.12);color:#c4b5fd;border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:20px;">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span style="font-size:19px;font-weight:800;color:#fff;">{{ $user->name }}</span>
                <span style="background:#ede9fe;color:#6d28d9;padding:3px 12px;border-radius:20px;font-size:11px;font-weight:800;">RESELLER</span>
                @if($user->status === 'active')<span style="background:rgba(1,191,99,.2);color:#6ee7b7;padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;">● Active</span>
                @elseif($user->status === 'pending')<span style="background:rgba(245,158,11,.2);color:#fcd34d;padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;">● Pending</span>
                @else<span style="background:rgba(239,68,68,.2);color:#fca5a5;padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;">● Suspended</span>@endif
            </div>
            <div style="font-size:12px;color:#a5b4fc;margin-top:6px;">
                {{ $user->email }}
                @if($user->website) &nbsp;·&nbsp; <a href="{{ $user->website }}" target="_blank" style="color:#c4b5fd;">{{ parse_url($user->website, PHP_URL_HOST) ?? $user->website }}</a>@endif
                @if($user->telegram) &nbsp;·&nbsp; TG: {{ $user->telegram }}@endif
                &nbsp;·&nbsp; Joined {{ $user->created_at->format('M d, Y') }}
                @if($user->last_login_at) &nbsp;·&nbsp; Last login {{ $user->last_login_at->diffForHumans() }}@endif
            </div>
        </div>
    </div>

    <div style="display:flex;gap:10px;margin-top:18px;flex-wrap:wrap;">
        <a href="{{ route('admin.resellers.index') }}" style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);color:#e0e7ff;border:1px solid rgba(255,255,255,.15);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:700;text-decoration:none;">← All Resellers</a>
        <a href="{{ route('admin.publishers.stats', $user) }}" style="display:inline-flex;align-items:center;gap:6px;background:#7c3aed;color:#fff;border-radius:10px;padding:8px 16px;font-size:13px;font-weight:700;text-decoration:none;">📊 Detailed Stats</a>

        @if($user->status === 'pending')
            <form method="POST" action="{{ route('admin.resellers.activate', $user) }}" style="margin:0;">@csrf
                <button style="background:rgba(1,191,99,.2);color:#6ee7b7;border:1px solid rgba(1,191,99,.35);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:700;cursor:pointer;">Approve Reseller</button>
            </form>
        @elseif($user->status === 'active')
            <form method="POST" action="{{ route('admin.resellers.suspend', $user) }}" style="margin:0;" onsubmit="return confirm('Suspend this reseller? Their tracking links will be paused.')">@csrf
                <button style="background:rgba(239,68,68,.2);color:#fca5a5;border:1px solid rgba(239,68,68,.35);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:700;cursor:pointer;">Suspend</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.resellers.activate', $user) }}" style="margin:0;">@csrf
                <button style="background:rgba(1,191,99,.2);color:#6ee7b7;border:1px solid rgba(1,191,99,.35);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:700;cursor:pointer;">Reactivate</button>
            </form>
        @endif
    </div>
</div>

{{-- ═══ STAT CARDS ═══ --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;" class="grid-3-resp">
    @php
        $cards = [
            ['label'=>'Raw Valid Today',    'value'=>number_format($clickStats['today']),         'color'=>'#7c3aed','bg'=>'#f5f3ff','border'=>'#ddd6fe'],
            ['label'=>'Shown Today (Divider)','value'=>number_format($clickStats['today_shown']), 'color'=>'#01BF63','bg'=>'#f0fdf4','border'=>'#bbf7d0'],
            ['label'=>'Windows Today (Shown)','value'=>number_format($clickStats['today_windows']),'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a'],
            ['label'=>'Mac Today (Shown)',  'value'=>number_format($clickStats['today_mac']),     'color'=>'#8b5cf6','bg'=>'#faf5ff','border'=>'#e9d5ff'],
            ['label'=>'This Week',          'value'=>number_format($clickStats['this_week']),     'color'=>'#3b82f6','bg'=>'#eff6ff','border'=>'#bfdbfe'],
            ['label'=>'Fraud Today',        'value'=>number_format($clickStats['today_fraud']),   'color'=>'#ef4444','bg'=>'#fef2f2','border'=>'#fecaca'],
        ];
    @endphp
    @foreach($cards as $c)
    <div style="background:{{ $c['bg'] }};border:1.5px solid {{ $c['border'] }};border-radius:13px;padding:16px 18px;">
        <div style="font-size:24px;font-weight:900;color:{{ $c['color'] }};line-height:1;">{{ $c['value'] }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ $c['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- ═══ CHART ═══ --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-title mb-4">Valid Clicks — Last 14 Days (divider applied)</div>
    <div id="resellerChart"></div>
</div>

{{-- ═══ DIVIDERS ═══ --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;" class="grid-2-resp">
    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="font-size:14px;font-weight:800;color:#111827;margin-bottom:4px;">Windows Click Divider</div>
        <div style="font-size:11px;color:#9ca3af;margin-bottom:14px;">Hidden from reseller — same system as publishers</div>
        <form method="POST" action="{{ route('admin.publishers.update-divider', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Divider Value</label>
                <input type="number" name="divider_value" class="form-control" value="{{ $divider->divider_value }}" min="1" max="100" step="0.1">
            </div>
            <div class="toggle-wrap mb-4">
                <label class="toggle"><input type="checkbox" name="is_enabled" value="1" {{ $divider->is_enabled ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                <span style="font-size:13px;font-weight:500;">Enable divider</span>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Update Divider</button>
        </form>
    </div>

    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="font-size:14px;font-weight:800;color:#111827;margin-bottom:4px;">Mac Click Divider</div>
        <div style="font-size:11px;color:#9ca3af;margin-bottom:14px;">Hidden from reseller</div>
        <form method="POST" action="{{ route('admin.publishers.update-mac-divider', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Mac Divider Value</label>
                <input type="number" name="mac_divider_value" class="form-control" value="{{ $divider->mac_divider_value ?? 1 }}" min="1" max="100" step="0.1">
            </div>
            <div class="toggle-wrap mb-4">
                <label class="toggle"><input type="checkbox" name="mac_divider_enabled" value="1" {{ $divider->mac_divider_enabled ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                <span style="font-size:13px;font-weight:500;">Enable Mac divider</span>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;background:linear-gradient(135deg,#8b5cf6,#6d28d9);border:none;">Update Mac Divider</button>
        </form>
    </div>
</div>

{{-- ═══ SIGNUP SCREENSHOTS ═══ --}}
@if($user->stat_screenshots && count($user->stat_screenshots) > 0)
<div class="card" style="margin-bottom:24px;">
    <div class="card-title mb-1">Signup Statistics Screenshots</div>
    <div style="font-size:12px;color:#9ca3af;margin-bottom:14px;">{{ count($user->stat_screenshots) }} file(s) submitted with the application</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;">
        @foreach($user->stat_screenshots as $i => $path)
        <a href="{{ asset('storage/' . $path) }}" target="_blank" style="display:block;border-radius:10px;overflow:hidden;border:1.5px solid #e5e7eb;">
            <img src="{{ asset('storage/' . $path) }}" alt="Screenshot {{ $i+1 }}" style="width:100%;height:95px;object-fit:cover;display:block;">
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ═══ WEBSITES ═══ --}}
<div class="card" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px;">
        <div class="card-title" style="margin:0;">Websites &amp; Ad Codes</div>
        <a href="{{ route('admin.publisher-websites.index') }}" style="font-size:12px;color:#3b82f6;font-weight:600;text-decoration:none;">Review queue →</a>
    </div>
    @if($websites->count() > 0)
    <table>
        <thead><tr><th>Website</th><th>Status</th><th>Tracking Link</th><th style="text-align:right;">Clicks</th></tr></thead>
        <tbody>
            @foreach($websites as $site)
            <tr>
                <td>
                    <div style="font-weight:600;">{{ $site->domain }}</div>
                    <div style="font-size:11px;color:#9ca3af;">Added {{ $site->created_at->format('M d, Y') }}</div>
                </td>
                <td>
                    @if($site->isPending())<span class="badge badge-warning">Pending</span>
                    @elseif($site->isApproved())<span class="badge badge-success">Approved</span>
                    @else<span class="badge badge-danger">Rejected</span>@endif
                </td>
                <td>
                    @if($site->trackingLink)
                    <a href="{{ route('admin.tracking.edit', $site->trackingLink) }}" style="color:#3b82f6;font-weight:600;font-size:13px;text-decoration:none;">
                        <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:12px;">{{ $site->trackingLink->unique_code }}</code> →
                    </a>
                    @else<span style="color:#9ca3af;font-size:12px;">—</span>@endif
                </td>
                <td style="text-align:right;font-weight:700;color:#7c3aed;">{{ number_format($site->trackingLink?->unique_clicks ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center;padding:26px;color:#9ca3af;font-size:13px;">No websites submitted yet.</div>
    @endif
</div>

{{-- ═══ TRACKING LINKS ═══ --}}
<div class="card">
    <div class="card-title mb-4">All Tracking Links</div>
    @if($trackingLinks->count() > 0)
    <table>
        <thead><tr><th>Link</th><th>Code</th><th>Domain</th><th style="text-align:right;">Valid Clicks</th><th style="text-align:center;">Status</th><th style="text-align:right;"></th></tr></thead>
        <tbody>
            @foreach($trackingLinks as $link)
            <tr>
                <td style="font-weight:600;">{{ $link->name ?: 'Unnamed' }}</td>
                <td><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:12px;">{{ $link->unique_code }}</code></td>
                <td style="font-size:13px;color:#6b7280;">{{ $link->trackingDomain?->domain ?? 'default' }}</td>
                <td style="text-align:right;font-weight:700;color:#7c3aed;">{{ number_format($link->unique_clicks) }}</td>
                <td style="text-align:center;">
                    @if($link->is_active)<span class="badge badge-success">Active</span>
                    @else<span class="badge badge-gray">Paused</span>@endif
                </td>
                <td style="text-align:right;"><a href="{{ route('admin.tracking.edit', $link) }}" class="btn btn-ghost btn-sm">Edit →</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center;padding:26px;color:#9ca3af;font-size:13px;">
        No tracking links yet. Approve one of their websites to auto-create a domain-locked link,
        or <a href="{{ route('admin.tracking.create') }}" style="color:#3b82f6;font-weight:600;">create one manually →</a>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
@media(max-width:900px){ .grid-3-resp { grid-template-columns:repeat(2,1fr) !important; } .grid-2-resp { grid-template-columns:1fr !important; } }
</style>
@endpush

@push('scripts')
<script>
const chartData = @json($clicksChart);
new ApexCharts(document.getElementById('resellerChart'), {
    series: [{ name: 'Valid Clicks', data: chartData.map(d => d.clicks) }],
    chart: { type: 'bar', height: 230, toolbar: { show: false } },
    colors: ['#7c3aed'],
    plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
    xaxis: { categories: chartData.map(d => d.date), labels: { style: { fontSize: '11px' } } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6', strokeDashArray: 4 }
}).render();
</script>
@endpush
