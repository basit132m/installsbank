@extends('layouts.admin')
@section('title', 'Campaigns')
@section('page-title', 'Campaigns')

@section('content')
@if(session('success'))
<div class="alert" style="background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;margin-bottom:20px;">{{ session('success') }}</div>
@endif

<!-- Filter tabs -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    @foreach([''=>'All','draft'=>'Draft','pending_payment'=>'Pending Payment','active'=>'Active','paused'=>'Paused','completed'=>'Completed','cancelled'=>'Cancelled'] as $val=>$label)
    <a href="{{ route('admin.campaigns.index', $val?['status'=>$val]:[]) }}"
       class="btn {{ request('status')===$val ? 'btn-primary' : 'btn-ghost' }} btn-sm">
        {{ $label }}
        @if($val==='draft' && $pendingCount>0)<span style="background:rgba(255,255,255,0.3);padding:1px 6px;border-radius:10px;font-size:11px;margin-left:4px;">{{ $pendingCount }}</span>@endif
        @if($val==='' && $pendingPayments>0)<span style="background:rgba(255,255,255,0.3);padding:1px 6px;border-radius:10px;font-size:11px;margin-left:4px;">{{ $pendingPayments }} payments</span>@endif
    </a>
    @endforeach
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>Campaign</th><th>Advertiser</th><th>Contract</th><th>Progress</th><th>Value</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($campaigns as $c)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $c->name }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ Str::limit($c->destination_url,40) }}</div>
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:600;">{{ $c->user->name }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $c->user->email }}</div>
                    </td>
                    <td style="font-size:12px;">{{ $c->contract_type==='fixed_rate'?'Fixed Rate':'Per Click' }}</td>
                    <td>
                        <div style="font-size:12px;font-weight:600;">{{ number_format($c->delivered_clicks) }} / {{ number_format($c->target_clicks) }}</div>
                        <div style="background:#f3f4f6;border-radius:3px;height:4px;margin-top:4px;overflow:hidden;">
                            <div style="height:100%;width:{{ $c->progressPercent() }}%;background:#3b82f6;border-radius:3px;"></div>
                        </div>
                    </td>
                    <td style="font-size:13px;font-weight:600;">{{ $c->total_value>0?'$'.number_format($c->total_value,2):'—' }}</td>
                    <td><span class="badge {{ ['draft'=>'badge-gray','pending_payment'=>'badge-warning','active'=>'badge-success','paused'=>'badge-info','completed'=>'badge-gray','cancelled'=>'badge-danger'][$c->status]??'badge-gray' }}">{{ ucfirst(str_replace('_',' ',$c->status)) }}</span></td>
                    <td><a href="{{ route('admin.campaigns.show', $c) }}" class="btn btn-ghost btn-sm">Manage</a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:#9ca3af;">No campaigns found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($campaigns->hasPages())
    <div style="margin-top:16px;">{{ $campaigns->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
