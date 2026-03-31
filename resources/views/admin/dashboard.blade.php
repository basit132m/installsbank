@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6faf2;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['total_publishers']) }}</div>
        <div class="stat-label">Total Publishers</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ $stats['active_publishers'] }} active · {{ $stats['pending_publishers'] }} pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;">
            <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['total_clicks_today']) }}</div>
        <div class="stat-label">Total Clicks Today</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ number_format($stats['valid_clicks_today']) }} valid</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">
            <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['fraud_clicks_today']) }}</div>
        <div class="stat-label">Fraud Clicks Today</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ $stats['unresolved_fraud_alerts'] }} unresolved alerts</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fce7f3;">
            <svg width="20" height="20" fill="none" stroke="#ec4899" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div class="stat-value">${{ number_format($stats['pending_withdrawals_amount'], 2) }}</div>
        <div class="stat-label">Pending Withdrawals</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ $stats['pending_withdrawals'] }} requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ede9fe;">
            <svg width="20" height="20" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['open_tickets']) }}</div>
        <div class="stat-label">Open Tickets</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3f4f6;">
            <svg width="20" height="20" fill="none" stroke="#6b7280" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['total_managers'] }}</div>
        <div class="stat-label">Managers</div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid-2 mb-6">
    <div class="card">
        <div class="flex-between mb-4">
            <div>
                <div class="card-title">Click Traffic</div>
                <div class="card-subtitle">Last 7 days</div>
            </div>
            <span style="display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280;">
                <span class="live-dot"></span> Live tracking
            </span>
        </div>
        <div id="clicksChart"></div>
    </div>
    <div class="card">
        <div class="card-title mb-4">OS Breakdown Today</div>
        <div id="osChart"></div>
    </div>
</div>

<!-- Country Breakdown & Fraud Alerts -->
<div class="grid-2 mb-6">
    <div class="card">
        <div class="flex-between mb-4">
            <div class="card-title">Top Countries Today</div>
            <a href="{{ route('admin.rates.index') }}" class="btn btn-ghost btn-sm">Manage Rates</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Country</th><th>Clicks</th><th>Share</th></tr></thead>
                <tbody>
                    @php $totalCountryClicks = $countryBreakdown->sum('count'); @endphp
                    @forelse($countryBreakdown as $c)
                    <tr>
                        <td><span style="font-weight:500;">{{ $c->country_name ?? $c->country_code ?? 'Unknown' }}</span></td>
                        <td>{{ number_format($c->count) }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:60px;height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden;">
                                    <div style="width:{{ $totalCountryClicks > 0 ? round($c->count/$totalCountryClicks*100) : 0 }}%;height:100%;background:#01BF63;border-radius:3px;"></div>
                                </div>
                                <span style="font-size:12px;color:#6b7280;">{{ $totalCountryClicks > 0 ? round($c->count/$totalCountryClicks*100) : 0 }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="color:#9ca3af;text-align:center;padding:24px;">No clicks today</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="flex-between mb-4">
            <div class="card-title">Recent Fraud Alerts</div>
            <a href="{{ route('admin.fraud.index') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        @forelse($fraudAlerts as $alert)
        <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 0;border-bottom:1px solid #f3f4f6;">
            <div style="width:36px;height:36px;background:#fee2e2;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" fill="#ef4444" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            </div>
            <div style="flex:1;">
                <div style="font-size:13px;font-weight:600;color:#111827;">{{ $alert->publisher->name }}</div>
                <div style="font-size:12px;color:#6b7280;">{{ str_replace('_', ' ', ucfirst($alert->alert_type)) }} · {{ $alert->occurrences }}x · {{ $alert->created_at->diffForHumans() }}</div>
            </div>
            <form method="POST" action="{{ route('admin.fraud.resolve', $alert) }}">@csrf<button class="btn btn-ghost btn-sm">Resolve</button></form>
        </div>
        @empty
        <div style="text-align:center;padding:24px;color:#9ca3af;">No unresolved alerts</div>
        @endforelse
    </div>
</div>

<!-- Recent Registrations -->
<div class="card">
    <div class="flex-between mb-4">
        <div class="card-title">Recent Publisher Registrations</div>
        <a href="{{ route('admin.publishers.index') }}" class="btn btn-ghost btn-sm">View All</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Publisher</th><th>Email</th><th>Website</th><th>Registered</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($recentPublishers as $pub)
                <tr>
                    <td><strong>{{ $pub->name }}</strong></td>
                    <td style="color:#6b7280;">{{ $pub->email }}</td>
                    <td style="color:#6b7280;">{{ $pub->website ?? '—' }}</td>
                    <td style="color:#6b7280;">{{ $pub->created_at->diffForHumans() }}</td>
                    <td>
                        @if($pub->status === 'active')<span class="badge badge-success">Active</span>
                        @elseif($pub->status === 'pending')<span class="badge badge-warning">Pending</span>
                        @else<span class="badge badge-danger">Suspended</span>@endif
                    </td>
                    <td><a href="{{ route('admin.publishers.show', $pub) }}" class="btn btn-primary btn-sm">Manage</a></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:24px;">No publishers yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Clicks chart
const clicksData = @json($clicksChart);
new ApexCharts(document.getElementById('clicksChart'), {
    series: [
        { name: 'Valid Clicks', data: clicksData.map(d => d.total) },
        { name: 'Fraud', data: clicksData.map(d => d.fraud) }
    ],
    chart: { type: 'area', height: 220, toolbar: { show: false }, sparkline: { enabled: false } },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
    colors: ['#01BF63', '#ef4444'],
    xaxis: { categories: clicksData.map(d => d.date), labels: { style: { fontSize: '11px' } } },
    yaxis: { labels: { style: { fontSize: '11px' } } },
    tooltip: { shared: true },
    legend: { position: 'top' },
    grid: { borderColor: '#f3f4f6' },
    dataLabels: { enabled: false }
}).render();

// OS chart
const osData = @json($osBreakdown);
if (osData.length > 0) {
    new ApexCharts(document.getElementById('osChart'), {
        series: osData.map(d => d.count),
        labels: osData.map(d => d.os || 'Unknown'),
        chart: { type: 'donut', height: 220 },
        colors: ['#01BF63', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
        legend: { position: 'bottom' },
        dataLabels: { style: { fontSize: '12px' } },
        plotOptions: { pie: { donut: { size: '65%' } } }
    }).render();
} else {
    document.getElementById('osChart').innerHTML = '<div style="text-align:center;padding:40px;color:#9ca3af;">No data today</div>';
}

// Auto-refresh stats every 30 seconds
setTimeout(() => location.reload(), 30000);
</script>
@endpush
