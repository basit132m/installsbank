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
        <div class="stat-label">Total Unique Clicks</div>
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

<!-- Country Flags (moved here) -->
@if(count($countryAgg) > 0)
@php
    $sortedCountries = collect($countryAgg)->sortByDesc('clicks');
    $totalClicks = collect($countryAgg)->sum('clicks');
    $countryNames = \App\Models\CountryRate::whereIn('country_code', array_keys($countryAgg))
        ->pluck('country_name', 'country_code');
@endphp
<div class="card mb-6">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div>
            <div class="card-title">Traffic by Country</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ count($countryAgg) }} countries · {{ number_format($totalClicks) }} total clicks</div>
        </div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:14px;padding:20px;background:#f9fafb;border-radius:12px;border:1px solid #f3f4f6;">
        @foreach($sortedCountries as $code => $data)
        <div style="display:flex;flex-direction:column;align-items:center;gap:5px;width:66px;">
            <img src="https://flagcdn.com/48x36/{{ strtolower($code) }}.png"
                 alt="{{ $countryNames[$code] ?? $code }}"
                 title="{{ $countryNames[$code] ?? $code }}"
                 style="width:48px;height:36px;border-radius:5px;object-fit:cover;box-shadow:0 1px 6px rgba(0,0,0,0.15);flex-shrink:0;"
                 onerror="this.style.display='none'">
            <span style="font-size:11px;font-weight:800;color:#111827;line-height:1;text-align:center;">{{ number_format($data['clicks']) }}</span>
            <span style="font-size:9px;color:#9ca3af;font-weight:600;line-height:1;">{{ strtoupper($code) }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Daily Breakdown Table -->
<div class="card mb-6">
    <div class="card-title mb-4">Daily Breakdown</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Unique Clicks</th>
                    @if($showEarnings)<th>Earnings</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($dailyStats as $day)
                <tr>
                    <td>{{ $day->date->format('M d, Y') }}</td>
                    <td><strong>{{ number_format($day->valid_clicks) }}</strong></td>
                    @if($showEarnings)<td style="color:#01BF63;">${{ number_format($day->earnings, 4) }}</td>@endif
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center;padding:24px;color:#9ca3af;">No data for this period</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Country Breakdown -->
<!-- Link-Level Stats -->
@if($linkStats->count() > 0)
<div class="card mb-6">
    <div class="card-title mb-4">Performance by Link</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Link Name</th>
                    <th>Status</th>
                    <th>Unique Clicks</th>
                    @if($showEarnings)<th>Earnings</th>@endif
                </tr>
            </thead>
            <tbody>
                @foreach($linkStats as $ls)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $ls['name'] }}</div>
                        <div style="font-family:monospace;font-size:11px;color:#9ca3af;">{{ $ls['code'] }}</div>
                    </td>
                    <td><span class="badge {{ $ls['active'] ? 'badge-success' : 'badge-danger' }}">{{ $ls['active'] ? 'Active' : 'Inactive' }}</span></td>
                    <td><strong>{{ number_format($ls['valid']) }}</strong></td>
                    @if($showEarnings)<td style="color:#01BF63;">${{ number_format($ls['earnings'], 4) }}</td>@endif
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
        @if($showEarnings)
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
