@extends('layouts.publisher')
@section('title', 'Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
@if(auth()->user()->status === 'pending')
    <div class="alert alert-warning" style="margin-bottom:24px;">
        <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        Your account is <strong>pending approval</strong>. Our team will review your application and activate your account soon.
    </div>
@endif

@if($pendingContract)
<div class="contract-box mb-6">
    <h3 style="margin-bottom:8px;">📋 New Contract Offer</h3>
    <p style="font-size:14px;color:#374151;margin-bottom:16px;">
        @if($pendingContract->type === 'per_click')
            You have been offered <strong>${{ number_format($pendingContract->rate, 4) }} per 1,000 unique clicks</strong>.
        @else
            You have been offered a <strong>fixed daily rate of ${{ number_format($pendingContract->rate, 4) }}/day</strong>.
        @endif
        @if($pendingContract->test_total_clicks)
            <br><span style="font-size:13px;color:#6b7280;">Based on your 48-hour test: {{ number_format($pendingContract->test_total_clicks) }} clicks</span>
        @endif
    </p>
    <div style="display:flex;gap:10px;">
        <form method="POST" action="{{ route('publisher.contract.accept', $pendingContract) }}">
            @csrf<button class="btn btn-primary">Accept Contract</button>
        </form>
        <form method="POST" action="{{ route('publisher.contract.reject', $pendingContract) }}">
            @csrf<button class="btn btn-ghost">Decline</button>
        </form>
    </div>
</div>
@endif

<!-- Performance Badge + Live Counter -->
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;align-items:center;">
    <div id="liveBadge" style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:20px;font-size:13px;font-weight:700;background:#f3f4f6;color:#6b7280;">
        <span class="live-dot" style="background:#6b7280;"></span>
        <span id="badgeLabel">Loading...</span>
    </div>
    <div style="display:inline-flex;align-items:center;gap:8px;background:#f9fafb;border:1px solid #e5e7eb;padding:8px 16px;border-radius:20px;">
        <span class="live-dot"></span>
        <span style="font-size:13px;color:#6b7280;">Live:</span>
        <span id="liveClickCount" style="font-size:15px;font-weight:800;color:#111827;">—</span>
        <span style="font-size:12px;color:#9ca3af;">clicks today</span>
        <span style="font-size:11px;color:#9ca3af;margin-left:4px;">(<span id="liveLastHour">—</span> last hour)</span>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6faf2;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['clicks_today']) }}</div>
        <div class="stat-label">Clicks Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;">
            <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['clicks_this_week']) }}</div>
        <div class="stat-label">This Week</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">
            <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['clicks_this_month']) }}</div>
        <div class="stat-label">This Month</div>
    </div>
    @if($stats['earnings_today'] !== null)
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6faf2;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        </div>
        <div class="stat-value">${{ number_format($stats['earnings_today'], 4) }}</div>
        <div class="stat-label">Earnings Today</div>
    </div>
    @endif
    @if($stats['balance'] !== null)
    <div class="stat-card" style="border:2px solid #01BF63;">
        <div class="stat-icon" style="background:#e6faf2;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div class="stat-value" style="color:#01BF63;">${{ number_format($stats['balance'], 4) }}</div>
        <div class="stat-label">Available Balance</div>
    </div>
    @endif
</div>

<!-- Chart -->
<div class="card mb-6">
    <div class="card-title mb-4">Click Performance (Last 7 Days)</div>
    <div id="pubClickChart"></div>
</div>

<!-- Country Breakdown -->
@if($countryBreakdown && $countryBreakdown->count() > 0)
<div class="card mb-6">
    <div class="card-title mb-4">Traffic by Country (Today)</div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Country</th><th>Clicks</th>@if($showEarnings)<th>Earnings</th>@endif</tr></thead>
            <tbody>
                @foreach($countryBreakdown as $code => $data)
                <tr>
                    <td>{{ $code }}</td>
                    <td><strong>{{ number_format($data['clicks']) }}</strong></td>
                    @if($showEarnings)<td style="color:#01BF63;">${{ number_format($data['earnings'], 4) }}</td>@endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Status Cards -->
<div class="grid-2">
    <div class="card">
        <div class="card-title mb-3">Contract Status</div>
        @if($contract)
            <div style="background:#e6faf2;border-radius:8px;padding:16px;">
                <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">Active Contract</div>
                <div style="font-size:16px;font-weight:700;color:#01BF63;">
                    @if($contract->type === 'per_click')
                        ${{ number_format($contract->rate, 4) }} per 1,000 clicks
                    @else
                        ${{ number_format($contract->rate, 4) }}/day fixed
                    @endif
                </div>
                <div style="font-size:12px;color:#6b7280;margin-top:4px;">Accepted {{ $contract->responded_at?->format('M d, Y') }}</div>
            </div>
        @elseif($hasTestRunning)
            <div style="background:#fef3c7;border-radius:8px;padding:16px;">
                <div style="font-size:14px;font-weight:600;color:#92400e;">48-Hour Test Running</div>
                <div style="font-size:13px;color:#6b7280;margin-top:4px;">We're analyzing your traffic quality. Results will be used to determine your rate.</div>
            </div>
        @else
            <div style="color:#9ca3af;font-size:14px;">No active contract yet. Admin will offer one after reviewing your test period.</div>
        @endif
    </div>

    <div class="card">
        <div class="card-title mb-3">Quick Actions</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <a href="{{ route('publisher.adcode') }}" class="btn btn-primary" style="justify-content:center;">Get Ad Code</a>
            <a href="{{ route('publisher.stats') }}" class="btn btn-ghost" style="justify-content:center;">View Detailed Stats</a>
            @if($showEarnings)
                <a href="{{ route('publisher.withdrawals.index') }}" class="btn btn-ghost" style="justify-content:center;">Request Withdrawal</a>
            @endif
            <a href="{{ route('publisher.support.create') }}" class="btn btn-ghost" style="justify-content:center;">Contact Support</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Real-time click counter + badge — polls every 30 seconds
function fetchLiveStats() {
    fetch('{{ route('publisher.live-stats') }}')
        .then(r => r.json())
        .then(data => {
            document.getElementById('liveClickCount').textContent = data.clicks_today.toLocaleString();
            document.getElementById('liveLastHour').textContent = data.clicks_last_hour;

            const badge = document.getElementById('liveBadge');
            const label = document.getElementById('badgeLabel');
            label.textContent = data.badge.label;
            badge.style.background = data.badge.color + '1a';
            badge.style.color = data.badge.color;
            badge.style.border = '1px solid ' + data.badge.color + '40';
            badge.querySelector('.live-dot').style.background = data.badge.color;
        })
        .catch(() => {});
}
fetchLiveStats();
setInterval(fetchLiveStats, 30000);

const data = @json($clicksChart);
new ApexCharts(document.getElementById('pubClickChart'), {
    series: [
        { name: 'Clicks', data: data.map(d => d.clicks) },
        @if($showEarnings)
        { name: 'Earnings ($)', data: data.map(d => d.earnings) }
        @endif
    ],
    chart: { type: 'area', height: 220, toolbar: { show: false } },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
    colors: ['#01BF63', '#3b82f6'],
    xaxis: { categories: data.map(d => d.date) },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' }
}).render();
</script>
@endpush
