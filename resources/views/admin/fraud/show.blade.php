@extends('layouts.admin')
@section('title', $user->name . ' — Fraud Alerts')
@section('page-title', 'Fraud Alerts — ' . $user->name)

@section('content')
<div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;flex-wrap:wrap;">
    <a href="{{ route('admin.fraud.index', ['resolved' => $resolved ? '1' : '0']) }}" class="btn btn-ghost btn-sm">← Back to Fraud</a>
    <a href="{{ route('admin.publishers.show', $user) }}" class="btn btn-ghost btn-sm">Manage Publisher</a>
    <a href="{{ route('admin.publishers.stats', $user) }}" class="btn btn-ghost btn-sm" style="color:#3b82f6;border-color:#3b82f6;">📊 Stats</a>
    <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($user->status) }}</span>
</div>

<div class="flex-between mb-4">
    <div style="display:flex;gap:8px;">
        <a href="?resolved=0" class="btn {{ !$resolved ? 'btn-primary' : 'btn-ghost' }} btn-sm">Unresolved</a>
        <a href="?resolved=1" class="btn {{ $resolved ? 'btn-primary' : 'btn-ghost' }} btn-sm">Resolved</a>
    </div>
    @if(!$resolved)
    <form method="POST" action="{{ route('admin.fraud.resolve-all') }}"
        onsubmit="return confirm('Resolve all alerts for all publishers?')">
        @csrf
        <button class="btn btn-success btn-sm">Resolve All</button>
    </form>
    @endif
</div>

<div class="card">
    @php
    $typeColors = [
        'duplicate_ip'   => ['bg'=>'#fef3c7','color'=>'#d97706','label'=>'Duplicate IP'],
        'vpn_detected'   => ['bg'=>'#fee2e2','color'=>'#dc2626','label'=>'VPN Detected'],
        'proxy_detected' => ['bg'=>'#fee2e2','color'=>'#dc2626','label'=>'Proxy Detected'],
        'bot_detected'   => ['bg'=>'#ede9fe','color'=>'#7c3aed','label'=>'Bot / Script'],
        'traffic_spike'  => ['bg'=>'#cffafe','color'=>'#0891b2','label'=>'Traffic Spike'],
    ];
    @endphp

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Alert Type</th>
                    <th>IP Address</th>
                    <th>Country</th>
                    <th>Tracking Link</th>
                    <th>Occurrences</th>
                    <th>Detected</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                @php $meta = $typeColors[$alert->alert_type] ?? ['bg'=>'#f3f4f6','color'=>'#6b7280','label'=>ucwords(str_replace('_',' ',$alert->alert_type))]; @endphp
                <tr style="{{ !$alert->is_resolved && in_array($alert->alert_type, ['vpn_detected','proxy_detected','bot_detected']) ? 'background:#fff5f5;' : '' }}">
                    <td>
                        <span style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }};padding:3px 10px;border-radius:10px;font-size:12px;font-weight:700;">
                            {{ $meta['label'] }}
                        </span>
                    </td>
                    <td><code style="font-size:12px;">{{ $alert->ip_address ?? '—' }}</code></td>
                    <td style="font-size:13px;">{{ $alert->country_code ?? '—' }}</td>
                    <td style="font-size:12px;color:#6b7280;">
                        {{ $alert->trackingLink?->name ?: ($alert->trackingLink?->unique_code ?? '—') }}
                    </td>
                    <td>
                        <span style="font-weight:700;font-size:15px;color:#ef4444;">{{ $alert->occurrences }}×</span>
                    </td>
                    <td style="font-size:12px;color:#9ca3af;">
                        {{ $alert->created_at->format('M d, Y H:i') }}<br>
                        <span style="font-size:11px;">{{ $alert->created_at->diffForHumans() }}</span>
                    </td>
                    <td>
                        @if(!$alert->is_resolved)
                            <form method="POST" action="{{ route('admin.fraud.resolve', $alert) }}">
                                @csrf
                                <button class="btn btn-ghost btn-sm">Resolve</button>
                            </form>
                        @else
                            <span style="font-size:12px;color:#9ca3af;">Resolved</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:#9ca3af;">
                        No {{ $resolved ? 'resolved' : 'unresolved' }} fraud alerts for this publisher.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $alerts->links() }}
</div>
@endsection
