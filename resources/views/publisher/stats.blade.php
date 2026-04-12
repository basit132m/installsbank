@extends('layouts.publisher')
@section('title', 'Stats')
@section('page-title', 'Statistics')

@section('content')

<!-- Filters row: Period + Link selector -->
<div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;align-items:center;">
    <!-- Period buttons -->
    <div style="display:flex;gap:6px;flex-wrap:wrap;">
        @foreach(['1'=>'Today','7'=>'7 Days','30'=>'30 Days','90'=>'90 Days'] as $val => $label)
            <a href="?period={{ $val }}{{ $selectedLink ? '&link_id='.$selectedLink->id : '' }}"
               class="btn {{ $period == $val ? 'btn-primary' : 'btn-ghost' }} btn-sm">{{ $label }}</a>
        @endforeach
    </div>

    <!-- Link selector -->
    @if($allLinks->count() > 0)
    <div style="display:flex;align-items:center;gap:8px;margin-left:auto;">
        <span style="font-size:12px;color:#6b7280;font-weight:600;">Filter by link:</span>
        <form method="GET" style="display:flex;gap:6px;align-items:center;">
            <input type="hidden" name="period" value="{{ $period }}">
            <select name="link_id" onchange="this.form.submit()"
                    style="font-size:12px;border:1.5px solid #e5e7eb;border-radius:8px;padding:5px 10px;background:#fff;color:#111827;outline:none;">
                <option value="">All Links</option>
                @foreach($allLinks as $lnk)
                    <option value="{{ $lnk->id }}" {{ $selectedLink?->id == $lnk->id ? 'selected' : '' }}>
                        {{ $lnk->name ?: 'Link #'.$lnk->id }}
                    </option>
                @endforeach
            </select>
            @if($selectedLink)
            <a href="?period={{ $period }}" class="btn btn-ghost btn-sm" style="padding:4px 10px;font-size:11px;">✕ Clear</a>
            @endif
        </form>
    </div>
    @endif
</div>

@if($selectedLink)
<div style="background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:10px;padding:10px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <svg width="15" height="15" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 015.656 0l4-4a4 4 0 01-5.656-5.656l-1.102 1.101"/></svg>
    <span style="font-size:13px;font-weight:600;color:#1e40af;">Showing stats for: {{ $selectedLink->name ?: 'Link #'.$selectedLink->id }}</span>
    <code style="font-size:11px;color:#6b7280;background:#f1f5f9;padding:2px 6px;border-radius:4px;">{{ $selectedLink->unique_code }}</code>
</div>
@endif

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

<!-- Traffic by Country — proper table with Windows Clicks + Earnings -->
@if($countryStats->count() > 0)
<div class="card mb-6">
    <div class="card-title mb-1">Traffic by Country</div>
    <div style="font-size:12px;color:#9ca3af;margin-bottom:16px;">
        {{ $countryStats->count() }} {{ Str::plural('country', $countryStats->count()) }} · Windows clicks only
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Windows Clicks</th>
                    @if($showEarnings && $profile->contract_type === 'per_click')<th>Earnings</th>@endif
                </tr>
            </thead>
            <tbody>
                @foreach($countryStats as $c)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="https://flagcdn.com/24x18/{{ strtolower($c->country_code) }}.png"
                                 style="width:24px;height:18px;border-radius:3px;object-fit:cover;flex-shrink:0;"
                                 onerror="this.style.display='none'">
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $c->country_name }}</div>
                                <div style="font-size:11px;color:#9ca3af;"><code>{{ $c->country_code }}</code></div>
                            </div>
                        </div>
                    </td>
                    <td><span style="font-weight:700;color:#01BF63;">{{ number_format($c->windows) }}</span></td>
                    @if($showEarnings && $profile->contract_type === 'per_click')
                    <td style="color:#01BF63;font-weight:600;">${{ number_format($c->earnings, 4) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Country + OS Charts -->
@if($countryStats->count() > 0 || $osBreakdown->count() > 0)
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="stats-charts-row">
    @if($countryStats->count() > 0)
    <div class="card">
        <div class="card-title mb-1">Clicks by Country</div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:4px;">{{ ['1'=>'Today','7'=>'Last 7 Days','30'=>'Last 30 Days','90'=>'Last 90 Days'][$period] ?? 'Selected period' }} · Windows clicks</div>
        <div id="statsCountryChart"></div>
    </div>
    @endif
    @if($osBreakdown->count() > 0)
    <div class="card">
        <div class="card-title mb-1">Clicks by OS</div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:4px;">{{ ['1'=>'Today','7'=>'Last 7 Days','30'=>'Last 30 Days','90'=>'Last 90 Days'][$period] ?? 'Selected period' }} · valid clicks</div>
        <div id="statsOsChart"></div>
    </div>
    @endif
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
                    <td>{{ $day->date_label }}</td>
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


@endsection

@push('styles')
<style>
@media(max-width:700px){ .stats-charts-row { grid-template-columns: 1fr !important; } }
</style>
@endpush

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
    xaxis: { categories: daily.map(d => d.date_raw), labels: { style: { fontSize: '11px' } } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' }
}).render();

@if($countryStats->count() > 0)
@php
    $cLabels = []; $cValues = [];
    foreach ($countryStats->take(8) as $c) {
        $cLabels[] = $c->country_name;
        $cValues[] = (int)$c->windows;
    }
    if ($countryStats->count() > 8) {
        $cLabels[] = 'Others';
        $cValues[] = (int)$countryStats->slice(8)->sum('windows');
    }
@endphp
new ApexCharts(document.getElementById('statsCountryChart'), {
    series: @json($cValues), labels: @json($cLabels),
    chart: { type: 'donut', height: 280, toolbar: { show: false } },
    colors: ['#01BF63','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899','#9ca3af'],
    plotOptions: { pie: { donut: { size: '60%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, color: '#374151', formatter: w => w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString() } } } } },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '12px' },
    tooltip: { y: { formatter: val => val.toLocaleString() + ' clicks' } }
}).render();
@endif

@if($osBreakdown->count() > 0)
@php
    $osLabels = $osBreakdown->keys()->toArray();
    $osValues = $osBreakdown->values()->map(fn($v) => (int)$v)->toArray();
@endphp
new ApexCharts(document.getElementById('statsOsChart'), {
    series: @json($osValues), labels: @json($osLabels),
    chart: { type: 'donut', height: 280, toolbar: { show: false } },
    colors: ['#3b82f6','#01BF63','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#9ca3af'],
    plotOptions: { pie: { donut: { size: '60%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, color: '#374151', formatter: w => w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString() } } } } },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '12px' },
    tooltip: { y: { formatter: val => val.toLocaleString() + ' clicks' } }
}).render();
@endif
</script>
@endpush
