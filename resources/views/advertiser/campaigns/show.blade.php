@extends('layouts.advertiser')
@section('title', $campaign->name)
@section('page-title', $campaign->name)

@section('content')
@if(session('success'))
<div class="alert alert-success mb-6">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger mb-6">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
@endif

<div style="display:flex;gap:8px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('advertiser.campaigns.index') }}" class="btn btn-ghost btn-sm">← Back</a>
    @php $sc=['draft'=>'badge-gray','pending_payment'=>'badge-warning','active'=>'badge-success','paused'=>'badge-info','completed'=>'badge-gray','cancelled'=>'badge-danger']; @endphp
    <span class="badge {{ $sc[$campaign->status]??'badge-gray' }}">{{ ucfirst(str_replace('_',' ',$campaign->status)) }}</span>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="info-grid">
    <!-- Campaign Info -->
    <div class="card">
        <div class="card-title mb-4">Campaign Information</div>
        <table style="width:100%;">
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;width:45%;">Destination URL</td><td><a href="{{ $campaign->destination_url }}" target="_blank" style="color:#3b82f6;font-size:13px;word-break:break-all;">{{ $campaign->destination_url }}</a></td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Contract Type</td><td style="font-size:13px;font-weight:600;">{{ $campaign->contract_type === 'fixed_rate' ? 'Fixed Rate' : 'Per Click by Country' }}</td></tr>
            @if($campaign->fixed_rate)<tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Rate per Click</td><td style="font-size:13px;font-weight:600;">${{ $campaign->fixed_rate }}</td></tr>@endif
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Target Clicks</td><td style="font-size:13px;font-weight:600;">{{ number_format($campaign->target_clicks) }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Total Value</td><td style="font-size:13px;font-weight:600;">{{ $campaign->total_value > 0 ? '$'.number_format($campaign->total_value,2) : 'Pending admin review' }}</td></tr>
            @if($campaign->advance_amount > 0)<tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Advance Required (50%)</td><td style="font-size:13px;font-weight:600;color:#f59e0b;">${{ number_format($campaign->advance_amount,2) }}</td></tr>@endif
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Total Paid</td><td style="font-size:13px;font-weight:600;color:#01BF63;">${{ number_format($campaign->total_paid,2) }}</td></tr>
            @if($campaign->started_at)<tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Started</td><td style="font-size:13px;">{{ $campaign->started_at->format('M d, Y H:i') }}</td></tr>@endif
            @if($campaign->admin_note)<tr><td style="color:#6b7280;font-size:13px;padding:6px 0;vertical-align:top;">Admin Note</td><td style="font-size:13px;">{{ $campaign->admin_note }}</td></tr>@endif
        </table>
    </div>

    <!-- Progress -->
    <div class="card">
        <div class="card-title mb-4">Campaign Progress</div>
        <div style="text-align:center;margin-bottom:20px;">
            <div style="font-size:48px;font-weight:900;color:#3b82f6;">{{ $campaign->progressPercent() }}%</div>
            <div style="font-size:13px;color:#6b7280;">Complete</div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
            <div style="background:#eff6ff;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:22px;font-weight:800;color:#3b82f6;">{{ number_format($campaign->delivered_clicks) }}</div>
                <div style="font-size:12px;color:#6b7280;">Delivered</div>
            </div>
            <div style="background:#f9fafb;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:22px;font-weight:800;">{{ number_format($campaign->remainingClicks()) }}</div>
                <div style="font-size:12px;color:#6b7280;">Remaining</div>
            </div>
        </div>
        <div style="background:#f3f4f6;border-radius:8px;height:12px;overflow:hidden;">
            <div style="height:100%;width:{{ $campaign->progressPercent() }}%;background:linear-gradient(90deg,#3b82f6,#01BF63);border-radius:8px;"></div>
        </div>
    </div>
</div>

<!-- Payment Section -->
@if($campaign->status === 'pending_payment')
<div class="card mb-6" style="border:2px solid #fcd34d;">
    <div style="font-size:16px;font-weight:700;color:#92400e;margin-bottom:4px;">💳 Payment Required</div>
    <div style="font-size:13px;color:#78350f;margin-bottom:20px;">
        Submit your advance payment of <strong>${{ number_format($campaign->advance_amount,2) }}</strong> to activate this campaign.
        Please transfer the amount to our payment address and enter your transaction details below.
    </div>
    <form method="POST" action="{{ route('advertiser.campaigns.payment', $campaign) }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Payment Method *</label>
                <select name="payment_method" class="form-control" required>
                    <option value="">Select method...</option>
                    <option value="USDT TRC20">USDT TRC20</option>
                    <option value="USDT ERC20">USDT ERC20</option>
                    <option value="Bitcoin">Bitcoin</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Other Crypto">Other Crypto</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Amount Paid (USD) *</label>
                <input type="number" name="amount" class="form-control" value="{{ $campaign->advance_amount }}" step="0.01" min="0.01" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Transaction ID / Hash *</label>
            <input type="text" name="transaction_id" class="form-control" required placeholder="Enter your transaction hash or ID">
        </div>
        <div class="form-group">
            <label class="form-label">Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Any notes about the payment..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Payment for Confirmation</button>
    </form>
</div>
@endif

<!-- Payment History -->
@if($payments->count() > 0)
<div class="card mb-6">
    <div class="card-title mb-4">Payment History</div>
    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Transaction ID</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($payments as $p)
                <tr>
                    <td style="font-size:13px;color:#6b7280;">{{ $p->created_at->format('M d, Y') }}</td>
                    <td><strong>${{ number_format($p->amount,2) }}</strong></td>
                    <td style="font-size:13px;">{{ $p->payment_method ?? '—' }}</td>
                    <td style="font-family:monospace;font-size:11px;color:#6b7280;">{{ $p->transaction_id ? substr($p->transaction_id,0,24).'...' : '—' }}</td>
                    <td>
                        @if($p->status==='confirmed')<span style="display:inline-flex;align-items:center;gap:5px;background:#d1fae5;color:#065f46;font-size:12px;font-weight:700;padding:4px 10px;border-radius:12px;"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Confirmed</span>
                        @elseif($p->status==='pending')<span style="display:inline-flex;align-items:center;gap:5px;background:#fef3c7;color:#92400e;font-size:12px;font-weight:700;padding:4px 10px;border-radius:12px;"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Pending</span>
                        @else<span style="display:inline-flex;align-items:center;gap:5px;background:#fee2e2;color:#991b1b;font-size:12px;font-weight:700;padding:4px 10px;border-radius:12px;">Rejected</span>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Click Chart -->
@if(count($clicksByDay) > 0)
<div class="card mb-6">
    <div class="card-title mb-4">Daily Click Performance</div>
    <div id="campaignChart"></div>
</div>
@endif

<!-- Country + OS Charts -->
@if($countryBreakdown->count() > 0 || $osBreakdown->count() > 0)
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="charts-row">
    @if($countryBreakdown->count() > 0)
    <div class="card">
        <div class="card-title mb-1">Clicks by Country</div>
        <div id="countryChart"></div>
    </div>
    @endif
    @if($osBreakdown->count() > 0)
    <div class="card">
        <div class="card-title mb-1">Clicks by OS</div>
        <div id="osChart"></div>
    </div>
    @endif
</div>

@if($countryBreakdown->count() > 0)
<div class="card mb-6">
    <div class="card-title mb-4">Country Breakdown</div>
    <div style="display:flex;flex-wrap:wrap;gap:14px;padding:16px;border-radius:12px;border:1px solid #f3f4f6;">
        @foreach($countryBreakdown->sortByDesc(fn($v)=>$v) as $code=>$clicks)
        <div style="display:flex;flex-direction:column;align-items:center;gap:5px;width:66px;">
            <img src="https://flagcdn.com/48x36/{{ strtolower($code) }}.png" title="{{ $countryNames[$code]??$code }}" style="width:48px;height:36px;border-radius:5px;object-fit:cover;box-shadow:0 1px 6px rgba(0,0,0,0.15);" onerror="this.style.display='none'">
            <span style="font-size:11px;font-weight:800;color:#111827;">{{ number_format($clicks) }}</span>
            <span style="font-size:9px;color:#9ca3af;font-weight:600;">{{ strtoupper($code) }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endif
@endsection

@push('styles')
<style>
@media(max-width:700px){.charts-row,.info-grid{grid-template-columns:1fr!important;}}
</style>
@endpush

@push('scripts')
<script>
@if(count($clicksByDay) > 0)
new ApexCharts(document.getElementById('campaignChart'),{
    series:[{name:'Clicks',data:@json(array_column($clicksByDay,'clicks'))}],
    chart:{type:'area',height:220,toolbar:{show:false}},
    stroke:{curve:'smooth',width:2},
    fill:{type:'gradient',gradient:{opacityFrom:0.4,opacityTo:0.05}},
    colors:['#3b82f6'],
    xaxis:{categories:@json(array_column($clicksByDay,'date')),labels:{style:{fontSize:'11px'}}},
    dataLabels:{enabled:false},grid:{borderColor:'#f3f4f6'}
}).render();
@endif

@if($countryBreakdown->count() > 0)
@php $cL=[]; $cV=[]; foreach($countryBreakdown->sortByDesc(fn($v)=>$v)->take(8) as $c=>$v){ $cL[]=$countryNames[$c]??strtoupper($c); $cV[]=(int)$v; } if($countryBreakdown->count()>8){$cL[]='Others';$cV[]=(int)$countryBreakdown->sortByDesc(fn($v)=>$v)->slice(8)->sum();} @endphp
new ApexCharts(document.getElementById('countryChart'),{series:@json($cV),labels:@json($cL),chart:{type:'donut',height:260,toolbar:{show:false}},colors:['#3b82f6','#01BF63','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899','#9ca3af'],plotOptions:{pie:{donut:{size:'60%',labels:{show:true,total:{show:true,label:'Total',fontSize:'13px',fontWeight:700,color:'#374151',formatter:w=>w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString()}}}}},dataLabels:{enabled:false},legend:{position:'bottom',fontSize:'12px'},tooltip:{y:{formatter:val=>val.toLocaleString()+' clicks'}}}).render();
@endif

@if($osBreakdown->count() > 0)
@php $oL=$osBreakdown->keys()->toArray(); $oV=$osBreakdown->values()->map(fn($v)=>(int)$v)->toArray(); @endphp
new ApexCharts(document.getElementById('osChart'),{series:@json($oV),labels:@json($oL),chart:{type:'donut',height:260,toolbar:{show:false}},colors:['#3b82f6','#01BF63','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#9ca3af'],plotOptions:{pie:{donut:{size:'60%',labels:{show:true,total:{show:true,label:'Total',fontSize:'13px',fontWeight:700,color:'#374151',formatter:w=>w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString()}}}}},dataLabels:{enabled:false},legend:{position:'bottom',fontSize:'12px'},tooltip:{y:{formatter:val=>val.toLocaleString()+' clicks'}}}).render();
@endif
</script>
@endpush
