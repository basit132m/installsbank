@extends('layouts.admin')
@section('title', 'Fraud Alerts')
@section('page-title', 'Fraud Alerts')

@section('content')
<div class="flex-between mb-4">
    <div style="display:flex;gap:8px;">
        <a href="?resolved=0" class="btn {{ !$resolved ? 'btn-primary' : 'btn-ghost' }} btn-sm">Unresolved</a>
        <a href="?resolved=1" class="btn {{ $resolved ? 'btn-primary' : 'btn-ghost' }} btn-sm">Resolved</a>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        <span style="font-size:12px;color:#9ca3af;">Auto-purge: alerts &gt;24h deleted hourly</span>
        @if(!$resolved)
        <form method="POST" action="{{ route('admin.fraud.resolve-all') }}" onsubmit="return confirm('Resolve all alerts?')">
            @csrf<button class="btn btn-success btn-sm">Resolve All</button>
        </form>
        @endif
        <form method="POST" action="{{ route('admin.fraud.purge-old') }}" onsubmit="return confirm('Delete all fraud alerts older than 24 hours?')">
            @csrf<button class="btn btn-danger btn-sm">Purge Old (&gt;24h)</button>
        </form>
    </div>
</div>

<div class="card">
    @php
    $typeColors = [
        'duplicate_ip'   => ['bg'=>'#fef3c7','color'=>'#d97706','label'=>'Duplicate IP'],
        'vpn_detected'   => ['bg'=>'#fee2e2','color'=>'#dc2626','label'=>'VPN'],
        'proxy_detected' => ['bg'=>'#fee2e2','color'=>'#dc2626','label'=>'Proxy'],
        'bot_detected'   => ['bg'=>'#ede9fe','color'=>'#7c3aed','label'=>'Bot'],
        'traffic_spike'  => ['bg'=>'#cffafe','color'=>'#0891b2','label'=>'Traffic Spike'],
    ];
    @endphp

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Publisher</th>
                    <th>Alert Types</th>
                    <th>Total Alerts</th>
                    <th>Total Occurrences</th>
                    <th>Last Alert</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($publishers as $publisher)
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:14px;color:#111827;">{{ $publisher->name }}</div>
                        <div style="font-size:12px;color:#9ca3af;">{{ $publisher->email }}</div>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                            @foreach($publisher->fraud_types as $type => $count)
                                @php $meta = $typeColors[$type] ?? ['bg'=>'#f3f4f6','color'=>'#6b7280','label'=>ucwords(str_replace('_',' ',$type))]; @endphp
                                <span style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }};padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">
                                    {{ $meta['label'] }} ({{ $count }})
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <span style="font-size:18px;font-weight:800;color:#ef4444;">{{ number_format($publisher->fraud_total) }}</span>
                    </td>
                    <td>
                        <span style="font-size:18px;font-weight:800;color:#f59e0b;">{{ number_format($publisher->fraud_occurrences) }}</span>
                        <div style="font-size:11px;color:#9ca3af;">total hits</div>
                    </td>
                    <td style="font-size:13px;color:#9ca3af;">
                        {{ $publisher->last_alert_at ? \Carbon\Carbon::parse($publisher->last_alert_at)->diffForHumans() : '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.fraud.show', [$publisher, 'resolved' => $resolved ? '1' : '0']) }}"
                                class="btn btn-primary btn-sm">View Details</a>
                            <a href="{{ route('admin.publishers.stats', $publisher) }}"
                                class="btn btn-ghost btn-sm">Stats</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:#9ca3af;">
                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;color:#01BF63;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $resolved ? 'No resolved alerts' : 'No unresolved fraud alerts' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
