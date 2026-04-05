@extends('layouts.advertiser')
@section('title', 'Dashboard')
@section('page-title', 'Advertiser Dashboard')

@section('content')
@if(session('registered'))
<div class="alert alert-success mb-6">Welcome to Installs Bank Advertiser Panel! Create your first campaign to get started.</div>
@endif

<!-- Stats -->
<div class="stats-grid mb-6">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
        </div>
        <div class="stat-value" style="color:#3b82f6;">{{ number_format($todayClicks) }}</div>
        <div class="stat-label">Clicks Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-value">{{ number_format($totalDelivered) }}</div>
        <div class="stat-label">Total Delivered Clicks</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;">
            <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-value" style="color:#f59e0b;">${{ number_format($totalSpent, 2) }}</div>
        <div class="stat-label">Total Spent</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <div class="stat-value" style="color:#3b82f6;">${{ number_format($profile->balance ?? 0, 2) }}</div>
        <div class="stat-label">Account Balance</div>
    </div>
</div>

<!-- Active Campaign Progress -->
@if($activeCampaign)
<div class="card mb-6">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div>
            <div class="card-title">Active Campaign: {{ $activeCampaign->name }}</div>
            <div style="font-size:12px;color:#9ca3af;">{{ $activeCampaign->destination_url }}</div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <span class="badge badge-success">Active</span>
            <a href="{{ route('advertiser.campaigns.show', $activeCampaign) }}" class="btn btn-ghost btn-sm">View Details →</a>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;margin-bottom:20px;">
        <div style="background:#f9fafb;border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#3b82f6;">{{ number_format($activeCampaign->delivered_clicks) }}</div>
            <div style="font-size:12px;color:#6b7280;">Delivered</div>
        </div>
        <div style="background:#f9fafb;border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#111827;">{{ number_format($activeCampaign->target_clicks) }}</div>
            <div style="font-size:12px;color:#6b7280;">Target</div>
        </div>
        <div style="background:#f9fafb;border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#01BF63;">{{ $activeCampaign->progressPercent() }}%</div>
            <div style="font-size:12px;color:#6b7280;">Progress</div>
        </div>
        <div style="background:#f9fafb;border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#f59e0b;">{{ number_format($activeCampaign->remainingClicks()) }}</div>
            <div style="font-size:12px;color:#6b7280;">Remaining</div>
        </div>
    </div>
    <div style="background:#f3f4f6;border-radius:8px;height:10px;overflow:hidden;">
        <div style="height:100%;width:{{ $activeCampaign->progressPercent() }}%;background:linear-gradient(90deg,#3b82f6,#01BF63);border-radius:8px;transition:width 0.5s;"></div>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:11px;color:#9ca3af;margin-top:6px;">
        <span>0</span>
        <span>{{ $activeCampaign->progressPercent() }}% complete</span>
        <span>{{ number_format($activeCampaign->target_clicks) }}</span>
    </div>
</div>

<!-- Country + OS charts -->
@if(($countryBreakdown && $countryBreakdown->count() > 0) || $osBreakdown->count() > 0)
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="charts-row">
    @if($countryBreakdown && $countryBreakdown->count() > 0)
    @php
        $totalBreakdownClicks = $countryBreakdown->sum();
    @endphp
    <div class="card">
        <div class="card-title mb-1">Clicks by Country</div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:4px;">Active campaign · all time</div>
        <div id="countryChart"></div>
    </div>
    @endif
    @if($osBreakdown->count() > 0)
    <div class="card">
        <div class="card-title mb-1">Clicks by OS</div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:4px;">Active campaign · all time</div>
        <div id="osChart"></div>
    </div>
    @endif
</div>

<!-- Flags -->
@if($countryBreakdown && $countryBreakdown->count() > 0)
<div class="card mb-6">
    <div class="card-title mb-4">Traffic by Country</div>
    <div style="display:flex;flex-wrap:wrap;gap:14px;padding:16px;background:#ffffff;border-radius:12px;border:1px solid #f3f4f6;">
        @foreach($countryBreakdown->sortByDesc(fn($v)=>$v) as $code => $clicks)
        <div style="display:flex;flex-direction:column;align-items:center;gap:5px;width:66px;">
            <img src="https://flagcdn.com/48x36/{{ strtolower($code) }}.png"
                 title="{{ $countryNames[$code] ?? $code }}"
                 style="width:48px;height:36px;border-radius:5px;object-fit:cover;box-shadow:0 1px 6px rgba(0,0,0,0.15);"
                 onerror="this.style.display='none'">
            <span style="font-size:11px;font-weight:800;color:#111827;line-height:1;text-align:center;">{{ number_format($clicks) }}</span>
            <span style="font-size:9px;color:#9ca3af;font-weight:600;line-height:1;">{{ strtoupper($code) }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endif

@else
<!-- No active campaign -->
<div class="card mb-6" style="text-align:center;padding:48px 24px;">
    <div style="width:64px;height:64px;background:#eff6ff;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="32" height="32" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    </div>
    <div style="font-size:18px;font-weight:700;margin-bottom:8px;">No Active Campaign</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:24px;">Create a campaign to start driving installs to your app.</div>
    <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary">+ Create Campaign</a>
</div>
@endif

<!-- All Campaigns -->
@if($campaigns->count() > 0)
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <div class="card-title">All Campaigns</div>
        <a href="{{ route('advertiser.campaigns.create') }}" class="btn btn-primary btn-sm">+ New Campaign</a>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>Campaign</th><th>Status</th><th>Progress</th><th>Contract</th><th></th></tr></thead>
            <tbody>
                @foreach($campaigns as $c)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $c->name }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $c->destination_url }}</div>
                    </td>
                    <td>
                        @php $statusColors = ['draft'=>'badge-gray','pending_payment'=>'badge-warning','active'=>'badge-success','paused'=>'badge-info','completed'=>'badge-gray','cancelled'=>'badge-danger']; @endphp
                        <span class="badge {{ $statusColors[$c->status] ?? 'badge-gray' }}">{{ ucfirst(str_replace('_',' ',$c->status)) }}</span>
                    </td>
                    <td style="min-width:140px;">
                        <div style="font-size:13px;font-weight:600;">{{ number_format($c->delivered_clicks) }} / {{ number_format($c->target_clicks) }}</div>
                        <div style="background:#f3f4f6;border-radius:4px;height:4px;margin-top:4px;overflow:hidden;">
                            <div style="height:100%;width:{{ $c->progressPercent() }}%;background:#3b82f6;border-radius:4px;"></div>
                        </div>
                    </td>
                    <td><span style="font-size:12px;color:#6b7280;">{{ $c->contract_type === 'fixed_rate' ? 'Fixed Rate' : 'Per Click' }}</span></td>
                    <td><a href="{{ route('advertiser.campaigns.show', $c) }}" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>@media(max-width:700px){.charts-row{grid-template-columns:1fr!important;}}</style>
@endpush

@push('scripts')
<script>
@if(isset($activeCampaign) && $activeCampaign && $countryBreakdown && $countryBreakdown->count() > 0)
@php
    $cLabels=[]; $cValues=[];
    foreach($countryBreakdown->sortByDesc(fn($v)=>$v)->take(8) as $code=>$clicks){ $cLabels[]=$countryNames[$code]??strtoupper($code); $cValues[]=(int)$clicks; }
    if($countryBreakdown->count()>8){ $cLabels[]='Others'; $cValues[]=(int)$countryBreakdown->sortByDesc(fn($v)=>$v)->slice(8)->sum(); }
@endphp
new ApexCharts(document.getElementById('countryChart'),{
    series:@json($cValues),labels:@json($cLabels),
    chart:{type:'donut',height:280,toolbar:{show:false}},
    colors:['#3b82f6','#01BF63','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899','#9ca3af'],
    plotOptions:{pie:{donut:{size:'60%',labels:{show:true,total:{show:true,label:'Total',fontSize:'13px',fontWeight:700,color:'#374151',formatter:w=>w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString()}}}}},
    dataLabels:{enabled:false},legend:{position:'bottom',fontSize:'12px'},
    tooltip:{y:{formatter:val=>val.toLocaleString()+' clicks'}}
}).render();
@endif

@if(isset($osBreakdown) && $osBreakdown->count() > 0)
@php $osLabels=$osBreakdown->keys()->toArray(); $osValues=$osBreakdown->values()->map(fn($v)=>(int)$v)->toArray(); @endphp
new ApexCharts(document.getElementById('osChart'),{
    series:@json($osValues),labels:@json($osLabels),
    chart:{type:'donut',height:280,toolbar:{show:false}},
    colors:['#3b82f6','#01BF63','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#9ca3af'],
    plotOptions:{pie:{donut:{size:'60%',labels:{show:true,total:{show:true,label:'Total',fontSize:'13px',fontWeight:700,color:'#374151',formatter:w=>w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString()}}}}},
    dataLabels:{enabled:false},legend:{position:'bottom',fontSize:'12px'},
    tooltip:{y:{formatter:val=>val.toLocaleString()+' clicks'}}
}).render();
@endif
</script>
@endpush
