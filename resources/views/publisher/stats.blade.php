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

@if(count($countryAgg) > 0)
@php
    $sortedCountries = collect($countryAgg)->sortByDesc('clicks');
    $totalClicks = collect($countryAgg)->sum('clicks');
    $countryNames = \App\Models\CountryRate::whereIn('country_code', array_keys($countryAgg))
        ->pluck('country_name', 'country_code');
@endphp
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <div class="card-title">Traffic by Country</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ count($countryAgg) }} countries • {{ number_format($totalClicks) }} total clicks</div>
        </div>
    </div>

    <!-- Flag strip: top 7 countries -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;padding:14px 16px;background:#f9fafb;border-radius:12px;border:1px solid #f3f4f6;">
        @foreach($sortedCountries->take(7) as $code => $data)
        @php $pct = $totalClicks > 0 ? round(($data['clicks'] / $totalClicks) * 100, 1) : 0; @endphp
        <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:52px;">
            <div style="position:relative;">
                <img src="https://flagcdn.com/32x24/{{ strtolower($code) }}.png"
                     alt="{{ $code }}"
                     style="width:32px;height:24px;border-radius:4px;object-fit:cover;box-shadow:0 1px 4px rgba(0,0,0,0.15);"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span style="display:none;font-size:20px;">🌍</span>
            </div>
            <span style="font-size:10px;font-weight:800;color:#111827;">{{ number_format($data['clicks']) }}</span>
            <span style="font-size:9px;color:#9ca3af;font-weight:600;">{{ $pct }}%</span>
        </div>
        @endforeach
        @if(count($countryAgg) > 7)
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;min-width:40px;">
            <div style="width:32px;height:24px;background:#e5e7eb;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#6b7280;">+{{ count($countryAgg) - 7 }}</div>
            <span style="font-size:9px;color:#9ca3af;">more</span>
        </div>
        @endif
    </div>

    <!-- Full list -->
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($sortedCountries as $code => $data)
        @php
            $name = $countryNames[$code] ?? $code;
            $pct  = $totalClicks > 0 ? round(($data['clicks'] / $totalClicks) * 100, 1) : 0;
            $maxC = $sortedCountries->max('clicks');
            $barW = $maxC > 0 ? round(($data['clicks'] / $maxC) * 100) : 0;
        @endphp
        <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:#f9fafb;border-radius:10px;border:1px solid #f3f4f6;">
            <img src="https://flagcdn.com/24x18/{{ strtolower($code) }}.png"
                 alt="{{ $name }}"
                 style="width:26px;height:19px;border-radius:3px;object-fit:cover;box-shadow:0 1px 3px rgba(0,0,0,0.1);flex-shrink:0;"
                 onerror="this.style.display='none'">
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                    <div style="display:flex;align-items:center;gap:7px;min-width:0;">
                        <span style="font-size:13px;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $name }}</span>
                        <span style="font-size:10px;color:#9ca3af;font-weight:600;flex-shrink:0;">{{ strtoupper($code) }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
                        <span style="font-size:13px;font-weight:800;color:#111827;">{{ number_format($data['clicks']) }}<span style="font-size:10px;font-weight:500;color:#9ca3af;"> clicks</span></span>
                        <span style="font-size:11px;color:#6b7280;min-width:32px;text-align:right;">{{ $pct }}%</span>
                        @if($showEarnings)
                            @if(in_array($code, $unratedCountries))
                                <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:8px;font-size:11px;font-weight:600;">Rate Pending</span>
                            @else
                                <span style="font-size:13px;font-weight:700;color:#01BF63;min-width:60px;text-align:right;">${{ number_format($data['earnings'], 4) }}</span>
                            @endif
                        @endif
                    </div>
                </div>
                <div style="height:3px;background:#e5e7eb;border-radius:3px;overflow:hidden;">
                    <div style="height:100%;width:{{ $barW }}%;background:linear-gradient(90deg,#01BF63,#00a354);border-radius:3px;"></div>
                </div>
            </div>
        </div>
        @endforeach
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
