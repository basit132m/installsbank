@extends('layouts.admin')
@section('title', 'Fraud Alerts')
@section('page-title', 'Fraud Alerts')

@section('content')
<div class="flex-between mb-4">
    <div style="display:flex;gap:8px;">
        <a href="?resolved=0" class="btn {{ request('resolved', '0') === '0' ? 'btn-primary' : 'btn-ghost' }} btn-sm">Unresolved</a>
        <a href="?resolved=1" class="btn {{ request('resolved') === '1' ? 'btn-primary' : 'btn-ghost' }} btn-sm">Resolved</a>
    </div>
    <form method="POST" action="{{ route('admin.fraud.resolve-all') }}" onsubmit="return confirm('Resolve all alerts?')">
        @csrf<button class="btn btn-success btn-sm">Resolve All</button>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Publisher</th><th>Alert Type</th><th>IP</th><th>Country</th><th>Count</th><th>Detected</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr>
                    <td><a href="{{ route('admin.publishers.show', $alert->publisher) }}" style="color:#01BF63;font-weight:600;">{{ $alert->publisher->name }}</a></td>
                    <td>
                        @php $typeColors = ['duplicate_ip'=>'badge-warning','vpn_detected'=>'badge-danger','proxy_detected'=>'badge-danger','bot_detected'=>'badge-danger','traffic_spike'=>'badge-warning','fingerprint_spoof'=>'badge-danger','suspicious_pattern'=>'badge-warning']; @endphp
                        <span class="badge {{ $typeColors[$alert->alert_type] ?? 'badge-gray' }}">{{ str_replace('_',' ',ucfirst($alert->alert_type)) }}</span>
                    </td>
                    <td style="font-family:monospace;font-size:12px;">{{ $alert->ip_address ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $alert->country_code ?? '—' }}</td>
                    <td><strong>{{ $alert->occurrences }}x</strong></td>
                    <td style="color:#9ca3af;font-size:13px;">{{ $alert->created_at->diffForHumans() }}</td>
                    <td>
                        @if(!$alert->is_resolved)
                            <form method="POST" action="{{ route('admin.fraud.resolve', $alert) }}">@csrf<button class="btn btn-ghost btn-sm">Resolve</button></form>
                        @else
                            <span style="color:#9ca3af;font-size:12px;">Resolved</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:48px;color:#9ca3af;">
                    @if(request('resolved') !== '1')
                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;color:#01BF63;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        No unresolved fraud alerts
                    @else
                        No resolved alerts
                    @endif
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $alerts->links() }}
</div>
@endsection
