@extends('layouts.reseller')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

@if(auth()->user()->status === 'pending')
<div style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1.5px solid #fde68a;border-radius:14px;padding:20px 24px;margin-bottom:24px;display:flex;gap:14px;align-items:flex-start;">
    <div style="width:42px;height:42px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div>
        <div style="font-size:15px;font-weight:800;color:#92400e;margin-bottom:4px;">Account Pending Approval</div>
        <div style="font-size:13px;color:#92400e;line-height:1.7;">
            Your reseller account is being reviewed by our team. Once approved you can add your websites,
            receive your ad codes and start tracking clicks in real time.
        </div>
    </div>
</div>
@endif

{{-- Unread notifications --}}
@foreach($notifications as $n)
<div class="alert alert-info" style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
    <span>{{ $n->message }}</span>
</div>
@endforeach

<!-- Stat cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#f5f3ff;">
            <svg width="20" height="20" fill="none" stroke="#7c3aed" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
        </div>
        <div class="stat-value" id="liveClicksToday">{{ number_format($stats['clicks_today']) }}</div>
        <div class="stat-label">Valid Clicks Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['clicks_this_week']) }}</div>
        <div class="stat-label">This Week</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;">
            <svg width="20" height="20" fill="none" stroke="#059669" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['clicks_this_month']) }}</div>
        <div class="stat-label">This Month</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fffbeb;">
            <svg width="20" height="20" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['clicks_total']) }}</div>
        <div class="stat-label">All Time</div>
    </div>
</div>

<!-- 7-day chart -->
<div class="card mb-6">
    <div class="card-title mb-4">Clicks — Last 7 Days</div>
    <div id="clicksChart"></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="resellerChartsRow">
    <!-- Country breakdown today -->
    <div class="card">
        <div class="card-title mb-1">Clicks by Country — Today</div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:12px;">Valid clicks</div>
        @if($clicksByCountry->count() > 0)
        <div style="max-height:320px;overflow-y:auto;">
            <table>
                <thead><tr><th>Country</th><th style="text-align:right;">Valid Clicks</th></tr></thead>
                <tbody>
                    @foreach($clicksByCountry as $row)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <img src="https://flagcdn.com/24x18/{{ strtolower($row->country_code) }}.png"
                                     style="width:24px;height:18px;border-radius:3px;object-fit:cover;" onerror="this.style.display='none'">
                                {{ $row->country_name }}
                            </div>
                        </td>
                        <td style="text-align:right;font-weight:700;color:#7c3aed;">{{ number_format($row->valid_clicks) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center;padding:30px;color:#9ca3af;font-size:13px;">No clicks recorded today yet</div>
        @endif
    </div>

    <!-- OS breakdown today -->
    <div class="card">
        <div class="card-title mb-1">Clicks by OS — Today</div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:12px;">Valid clicks</div>
        @if($osBreakdown->count() > 0)
        <div id="osChart"></div>
        @else
        <div style="text-align:center;padding:30px;color:#9ca3af;font-size:13px;">No clicks recorded today yet</div>
        @endif
    </div>
</div>

<!-- Websites overview -->
<div class="card">
    <div class="flex-between mb-4">
        <div class="card-title" style="margin:0;">My Websites</div>
        @if(auth()->user()->status === 'active')
        <a href="{{ route('reseller.websites.index') }}" class="btn btn-primary btn-sm">Manage Websites →</a>
        @endif
    </div>
    @if($websites->count() > 0)
    <table>
        <thead><tr><th>Website</th><th>Status</th><th>Ad Code</th></tr></thead>
        <tbody>
            @foreach($websites as $site)
            <tr>
                <td style="font-weight:600;">{{ $site->domain }}</td>
                <td>
                    @if($site->isPending())<span class="badge badge-warning">Pending Review</span>
                    @elseif($site->isApproved())<span class="badge badge-success">Approved</span>
                    @else<span class="badge badge-danger">Rejected</span>@endif
                </td>
                <td>
                    @if($site->isApproved() && $site->trackingLink)
                        <code style="background:#f3f4f6;padding:2px 8px;border-radius:4px;font-size:12px;">{{ $site->trackingLink->unique_code }}</code>
                    @else
                        <span style="color:#9ca3af;font-size:12px;">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center;padding:30px;color:#9ca3af;font-size:13px;">
        No websites yet.
        @if(auth()->user()->status === 'active')
            <a href="{{ route('reseller.websites.index') }}" style="color:#7c3aed;font-weight:600;">Add your first website →</a>
        @else
            You can add websites once your account is approved.
        @endif
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>@media(max-width:800px){ .resellerChartsRow { grid-template-columns:1fr !important; } }</style>
@endpush

@push('scripts')
<script>
const chartData = @json($clicksChart);
new ApexCharts(document.getElementById('clicksChart'), {
    series: [{ name: 'Valid Clicks', data: chartData.map(d => d.clicks) }],
    chart: { type: 'area', height: 240, toolbar: { show: false } },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
    colors: ['#7c3aed'],
    xaxis: { categories: chartData.map(d => d.date), labels: { style: { fontSize: '11px' } } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' }
}).render();

@if($osBreakdown->count() > 0)
new ApexCharts(document.getElementById('osChart'), {
    series: @json($osBreakdown->values()),
    labels: @json($osBreakdown->keys()),
    chart: { type: 'donut', height: 260 },
    colors: ['#7c3aed','#01BF63','#3b82f6','#f59e0b','#ef4444','#06b6d4','#9ca3af'],
    plotOptions: { pie: { donut: { size: '62%' } } },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '12px' },
    tooltip: { y: { formatter: v => v.toLocaleString() + ' clicks' } }
}).render();
@endif

@if(auth()->user()->status === 'active')
// Live refresh of today's clicks every 30s
setInterval(() => {
    fetch('{{ route('reseller.live-stats') }}', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => {
            const el = document.getElementById('liveClicksToday');
            if (el && d.clicks_today !== undefined) el.textContent = d.clicks_today.toLocaleString();
        })
        .catch(() => {});
}, 30000);
@endif
</script>
@endpush
