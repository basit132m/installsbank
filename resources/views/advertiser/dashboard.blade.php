@extends('layouts.advertiser')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@if(session('registered'))
<div class="alert alert-info mb-6" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #bfdbfe;color:#1e40af;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:12px;">
    <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div><strong>Welcome to Installs Bank!</strong> Your advertiser account is ready. Create your first campaign to start driving installs.</div>
</div>
@endif

{{-- ═══════════════════════════════════ TOP STAT CARDS ═══════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">

    {{-- Live clicks today --}}
    <div class="stat-card" style="border:1px solid #dbeafe;background:linear-gradient(135deg,#eff6ff,#fff);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div style="width:40px;height:40px;background:#dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
            </div>
            <div style="display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#10b981;">
                <span style="width:7px;height:7px;background:#10b981;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite;"></span>
                LIVE
            </div>
        </div>
        <div id="clicks-today" class="stat-value" style="color:#3b82f6;">{{ number_format($todayClicks) }}</div>
        <div class="stat-label">Clicks Today</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Last hour: <span id="clicks-hour" style="font-weight:600;color:#6b7280;">—</span></div>
    </div>

    {{-- Total delivered --}}
    <div class="stat-card" style="border:1px solid #d1fae5;background:linear-gradient(135deg,#f0fdf4,#fff);">
        <div style="width:40px;height:40px;background:#d1fae5;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
            <svg width="20" height="20" fill="none" stroke="#10b981" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-value" style="color:#059669;">{{ number_format($totalDelivered) }}</div>
        <div class="stat-label">Total Delivered</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">All campaigns</div>
    </div>

    {{-- Total spent --}}
    <div class="stat-card" style="border:1px solid #fde68a;background:linear-gradient(135deg,#fffbeb,#fff);">
        <div style="width:40px;height:40px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
            <svg width="20" height="20" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-value" style="color:#d97706;">${{ number_format($totalSpent, 2) }}</div>
        <div class="stat-label">Total Spent</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">All time</div>
    </div>

    {{-- Balance --}}
    <div class="stat-card" style="border:1px solid #e9d5ff;background:linear-gradient(135deg,#faf5ff,#fff);">
        <div style="width:40px;height:40px;background:#ede9fe;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
            <svg width="20" height="20" fill="none" stroke="#8b5cf6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <div class="stat-value" style="color:#7c3aed;">${{ number_format($profile->balance ?? 0, 2) }}</div>
        <div class="stat-label">Account Balance</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Available credit</div>
    </div>

    {{-- Active campaigns --}}
    <div class="stat-card" style="border:1px solid #e5e7eb;">
        <div style="width:40px;height:40px;background:#f3f4f6;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
            <svg width="20" height="20" fill="none" stroke="#6b7280" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div class="stat-value">{{ $campaigns->count() }}</div>
        <div class="stat-label">Total Campaigns</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">{{ $campaigns->where('status','active')->count() }} active</div>
    </div>

</div>

{{-- ════════════════════════ ACTIVE CAMPAIGN CARD ════════════════════════ --}}
@if($activeCampaign)
<div class="card mb-6" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%);border:none;color:white;padding:28px;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#93c5fd;margin-bottom:6px;">
                <span style="width:7px;height:7px;background:#10b981;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite;margin-right:5px;"></span>
                Active Campaign
            </div>
            <div style="font-size:20px;font-weight:800;color:white;">{{ $activeCampaign->name }}</div>
            <div style="font-size:12px;color:#94a3b8;margin-top:4px;word-break:break-all;">{{ $activeCampaign->destination_url }}</div>
        </div>
        <a href="{{ route('advertiser.campaigns.show', $activeCampaign) }}" style="background:rgba(255,255,255,0.12);color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,0.2);white-space:nowrap;">
            View Details →
        </a>
    </div>

    {{-- Stat mini-boxes --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
        <div style="background:rgba(255,255,255,0.08);border-radius:10px;padding:14px;text-align:center;">
            <div id="live-delivered" style="font-size:22px;font-weight:800;color:#60a5fa;">{{ number_format($activeCampaign->delivered_clicks) }}</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:3px;">Delivered</div>
        </div>
        <div style="background:rgba(255,255,255,0.08);border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:white;">{{ number_format($activeCampaign->target_clicks) }}</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:3px;">Target</div>
        </div>
        <div style="background:rgba(255,255,255,0.08);border-radius:10px;padding:14px;text-align:center;">
            <div id="live-progress" style="font-size:22px;font-weight:800;color:#34d399;">{{ $activeCampaign->progressPercent() }}%</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:3px;">Progress</div>
        </div>
        <div style="background:rgba(255,255,255,0.08);border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#fbbf24;">{{ number_format($activeCampaign->remainingClicks()) }}</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:3px;">Remaining</div>
        </div>
    </div>

    {{-- Progress bar --}}
    <div>
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#94a3b8;margin-bottom:6px;">
            <span>Campaign Progress</span>
            <span id="live-progress-text">{{ $activeCampaign->progressPercent() }}% of {{ number_format($activeCampaign->target_clicks) }} clicks</span>
        </div>
        <div style="background:rgba(255,255,255,0.1);border-radius:999px;height:10px;overflow:hidden;">
            <div id="live-progress-bar" style="height:100%;width:{{ $activeCampaign->progressPercent() }}%;background:linear-gradient(90deg,#3b82f6,#10b981);border-radius:999px;transition:width 1s ease;"></div>
        </div>
    </div>
</div>

{{-- ════════════ CHARTS + FLAGS (only show if data exists) ════════════ --}}
@if(($countryBreakdown && $countryBreakdown->count() > 0) || $osBreakdown->count() > 0)
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="charts-row">
    @if($countryBreakdown && $countryBreakdown->count() > 0)
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
            <div class="card-title">Clicks by Country</div>
            <span style="font-size:11px;color:#9ca3af;background:#f3f4f6;padding:2px 8px;border-radius:20px;">All time</span>
        </div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:12px;">Active campaign traffic</div>
        <div id="countryChart"></div>
    </div>
    @endif
    @if($osBreakdown->count() > 0)
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
            <div class="card-title">Clicks by OS</div>
            <span style="font-size:11px;color:#9ca3af;background:#f3f4f6;padding:2px 8px;border-radius:20px;">All time</span>
        </div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:12px;">Operating system breakdown</div>
        <div id="osChart"></div>
    </div>
    @endif
</div>

@if($countryBreakdown && $countryBreakdown->count() > 0)
<div class="card mb-6">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div class="card-title">Traffic by Country</div>
        <span style="font-size:12px;color:#9ca3af;">{{ $countryBreakdown->count() }} countries</span>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;">
        @php $totalBreakdownClicks = $countryBreakdown->sum(); @endphp
        @foreach($countryBreakdown->sortByDesc(fn($v)=>$v) as $code => $clicks)
        <div style="display:flex;flex-direction:column;align-items:center;gap:5px;width:62px;">
            <div style="position:relative;">
                <img src="https://flagcdn.com/48x36/{{ strtolower($code) }}.png"
                     title="{{ $countryNames[$code] ?? strtoupper($code) }}"
                     style="width:48px;height:36px;border-radius:5px;object-fit:cover;box-shadow:0 2px 8px rgba(0,0,0,0.12);border:1px solid #e5e7eb;"
                     onerror="this.style.display='none'">
            </div>
            <span style="font-size:12px;font-weight:800;color:#111827;">{{ number_format($clicks) }}</span>
            <span style="font-size:10px;color:#6b7280;font-weight:600;">{{ strtoupper($code) }}</span>
            <span style="font-size:10px;color:#9ca3af;">{{ $totalBreakdownClicks > 0 ? round($clicks/$totalBreakdownClicks*100,1) : 0 }}%</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endif

@else
{{-- ═══════════════ NO ACTIVE CAMPAIGN ═══════════════ --}}
<div class="card mb-6" style="text-align:center;padding:56px 24px;border:2px dashed #e5e7eb;">
    <div style="width:72px;height:72px;background:#eff6ff;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
        <svg width="36" height="36" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    </div>
    <div style="font-size:20px;font-weight:800;color:#111827;margin-bottom:8px;">No Active Campaign</div>
    <div style="font-size:14px;color:#6b7280;max-width:360px;margin:0 auto 28px;line-height:1.6;">Create a campaign and our team will set up your rates and tracking links to start driving installs.</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-blue">
        <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Create Campaign
    </a>
</div>
@endif

{{-- ══════════════════════ ALL CAMPAIGNS TABLE ══════════════════════ --}}
@if($campaigns->count() > 0)
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <div class="card-title">All Campaigns</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ $campaigns->count() }} total · {{ $campaigns->where('status','active')->count() }} active</div>
        </div>
        <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-blue btn-sm">+ New</a>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Campaign</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Contract</th>
                    <th>Spent</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $c)
                @php
                    $sc = ['draft'=>'badge-gray','pending_payment'=>'badge-warning','active'=>'badge-success','paused'=>'badge-info','completed'=>'badge-gray','cancelled'=>'badge-danger'];
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;color:#111827;">{{ $c->name }}</div>
                        <div style="font-size:11px;color:#9ca3af;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $c->destination_url }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $sc[$c->status] ?? 'badge-gray' }}">{{ ucfirst(str_replace('_',' ',$c->status)) }}</span>
                    </td>
                    <td style="min-width:160px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px;">
                            <span style="font-weight:600;">{{ number_format($c->delivered_clicks) }}</span>
                            <span style="color:#9ca3af;">/ {{ number_format($c->target_clicks) }}</span>
                        </div>
                        <div style="background:#f3f4f6;border-radius:4px;height:5px;overflow:hidden;">
                            <div style="height:100%;width:{{ $c->progressPercent() }}%;background:{{ $c->status==='active' ? '#3b82f6' : '#9ca3af' }};border-radius:4px;"></div>
                        </div>
                        <div style="font-size:10px;color:#9ca3af;margin-top:3px;">{{ $c->progressPercent() }}% complete</div>
                    </td>
                    <td>
                        <span style="font-size:12px;background:#f3f4f6;color:#374151;padding:3px 8px;border-radius:20px;font-weight:500;">
                            {{ $c->contract_type === 'fixed_rate' ? 'Fixed Rate' : 'Per Click' }}
                        </span>
                    </td>
                    <td>
                        @if($c->total_value > 0)
                            <div style="font-size:13px;font-weight:600;color:#374151;">${{ number_format($c->total_paid, 2) }}</div>
                            <div style="font-size:10px;color:#9ca3af;">of ${{ number_format($c->total_value, 2) }}</div>
                        @else
                            <span style="color:#9ca3af;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('advertiser.campaigns.show', $c) }}" class="btn btn-ghost btn-sm">View →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
@keyframes pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.5; transform:scale(.85); }
}
@media(max-width:700px){
    .charts-row { grid-template-columns:1fr !important; }
}
</style>
@endpush

@push('scripts')
<script>
{{-- Country donut chart --}}
@if(isset($activeCampaign) && $activeCampaign && $countryBreakdown && $countryBreakdown->count() > 0)
@php
    $cLabels = []; $cValues = [];
    foreach($countryBreakdown->sortByDesc(fn($v)=>$v)->take(8) as $code=>$clicks) {
        $cLabels[] = $countryNames[$code] ?? strtoupper($code);
        $cValues[] = (int)$clicks;
    }
    if($countryBreakdown->count() > 8) {
        $cLabels[] = 'Others';
        $cValues[] = (int)$countryBreakdown->sortByDesc(fn($v)=>$v)->slice(8)->sum();
    }
@endphp
new ApexCharts(document.getElementById('countryChart'), {
    series: @json($cValues),
    labels: @json($cLabels),
    chart: { type: 'donut', height: 300, toolbar: { show: false } },
    colors: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899','#9ca3af'],
    plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, color: '#374151', formatter: w => w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString() } } } } },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '12px', fontFamily: 'Inter, sans-serif' },
    tooltip: { y: { formatter: val => val.toLocaleString() + ' clicks' } },
    stroke: { width: 2 }
}).render();
@endif

{{-- OS donut chart --}}
@if(isset($osBreakdown) && $osBreakdown->count() > 0)
@php
    $osLabels = $osBreakdown->keys()->toArray();
    $osValues = $osBreakdown->values()->map(fn($v)=>(int)$v)->toArray();
@endphp
new ApexCharts(document.getElementById('osChart'), {
    series: @json($osValues),
    labels: @json($osLabels),
    chart: { type: 'donut', height: 300, toolbar: { show: false } },
    colors: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#9ca3af'],
    plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, color: '#374151', formatter: w => w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString() } } } } },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '12px', fontFamily: 'Inter, sans-serif' },
    tooltip: { y: { formatter: val => val.toLocaleString() + ' clicks' } },
    stroke: { width: 2 }
}).render();
@endif

{{-- Live polling every 8 seconds --}}
function refreshLive() {
    fetch('{{ route('advertiser.live-stats') }}')
        .then(r => r.json())
        .then(d => {
            document.getElementById('clicks-today').textContent = d.clicks_today.toLocaleString();
            document.getElementById('clicks-hour').textContent = d.clicks_last_hour.toLocaleString();
            if (d.delivered_clicks !== null) {
                const deliveredEl = document.getElementById('live-delivered');
                const progressEl  = document.getElementById('live-progress');
                const barEl       = document.getElementById('live-progress-bar');
                const textEl      = document.getElementById('live-progress-text');
                if (deliveredEl) deliveredEl.textContent = d.delivered_clicks.toLocaleString();
                if (progressEl)  progressEl.textContent  = d.progress_percent + '%';
                if (barEl)       barEl.style.width        = d.progress_percent + '%';
                if (textEl)      textEl.textContent       = d.progress_percent + '% of ' + d.target_clicks.toLocaleString() + ' clicks';
            }
        })
        .catch(() => {});
}
setInterval(refreshLive, 8000);
refreshLive();
</script>
@endpush
