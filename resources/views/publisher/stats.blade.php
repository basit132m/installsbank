@extends('layouts.publisher')
@section('title', 'Stats')
@section('page-title', 'Statistics')

@section('content')
<!-- Period Filter -->
<div style="display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap;">
    @foreach(['1'=>'Today','7'=>'7 Days','30'=>'30 Days','90'=>'90 Days'] as $val => $label)
        <a href="?period={{ $val }}" class="btn {{ $period == $val ? 'btn-primary' : 'btn-ghost' }} btn-sm">{{ $label }}</a>
    @endforeach
</div>

<!-- Totals -->
<div class="stats-grid mb-6">
    <div class="stat-card">
        <div class="stat-value">{{ number_format($totals['clicks']) }}</div>
        <div class="stat-label">Total Valid Clicks</div>
    </div>
    @if($totals['earnings'] !== null)
    <div class="stat-card">
        <div class="stat-value" style="color:#01BF63;">${{ number_format($totals['earnings'], 4) }}</div>
        <div class="stat-label">Total Earnings</div>
    </div>
    @endif
</div>

<!-- Daily Chart -->
<div class="card mb-6">
    <div class="card-title mb-4">Daily Performance</div>
    <div id="statsChart"></div>
</div>

<!-- Daily Breakdown Table -->
<div class="card mb-6">
    <div class="card-title mb-4">Daily Breakdown</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Valid Clicks</th>
                    @if($profile->payment_enabled)<th>Earnings</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($dailyStats as $day)
                <tr>
                    <td>{{ $day->date->format('M d, Y') }}</td>
                    <td><strong>{{ number_format($day->valid_clicks) }}</strong></td>
                    @if($profile->payment_enabled)<td style="color:#01BF63;">${{ number_format($day->earnings, 4) }}</td>@endif
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center;padding:24px;color:#9ca3af;">No data for this period</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Country Breakdown -->
@if(count($countryAgg) > 0)
<div class="card">
    <div class="card-title mb-4">Traffic by Country</div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Country</th><th>Clicks</th>@if($profile->payment_enabled)<th>Earnings</th>@endif</tr></thead>
            <tbody>
                @foreach($countryAgg as $code => $data)
                <tr>
                    <td>{{ $code }}</td>
                    <td>{{ number_format($data['clicks']) }}</td>
                    @if($profile->payment_enabled)
                        @if(in_array($code, $unratedCountries))
                            <td><span style="background:#fef3c7;color:#92400e;padding:2px 10px;border-radius:10px;font-size:12px;font-weight:600;">N/A — Rate Pending</span></td>
                        @else
                            <td style="color:#01BF63;">${{ number_format($data['earnings'], 4) }}</td>
                        @endif
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const daily = @json($dailyStats);
new ApexCharts(document.getElementById('statsChart'), {
    series: [
        { name: 'Clicks', data: daily.map(d => d.valid_clicks) },
        @if($profile->payment_enabled)
        { name: 'Earnings ($)', data: daily.map(d => parseFloat(d.earnings)) }
        @endif
    ],
    chart: { type: 'area', height: 250, toolbar: { show: false } },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
    colors: ['#01BF63', '#3b82f6'],
    xaxis: { categories: daily.map(d => d.date), labels: { style: { fontSize: '11px' } } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' }
}).render();
</script>
@endpush
