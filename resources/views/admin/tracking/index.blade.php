@extends('layouts.admin')
@section('title', 'Tracking Links')
@section('page-title', 'Tracking Links')

@section('content')
<div class="flex-between mb-6">
    <p class="text-muted text-sm">Manage all tracking links across publishers</p>
    <a href="{{ route('admin.tracking.create') }}" class="btn btn-primary">+ Create Link</a>
</div>

<!-- Ad Code Status Legend -->
<div style="display:flex;gap:16px;align-items:center;margin-bottom:16px;font-size:13px;color:#6b7280;">
    <span style="font-weight:600;">Ad Code Status:</span>
    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#10b981;margin-right:4px;"></span>Active (click within 2h)</span>
    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f59e0b;margin-right:4px;"></span>Idle (2–6h no clicks)</span>
    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ef4444;margin-right:4px;"></span>Inactive (6h+ no clicks)</span>
    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#d1d5db;margin-right:4px;"></span>No clicks yet</span>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Publisher</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Destination URL</th>
                    <th>Clicks</th>
                    <th>Ad Code Status</th>
                    <th>Link Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($links as $link)
                @php
                    $lastClick = $link->last_click_at;
                    $hoursSince = $lastClick ? now()->diffInHours($lastClick) : null;
                    if ($lastClick === null) {
                        $adStatusColor = '#d1d5db';
                        $adStatusLabel = 'No clicks yet';
                        $adStatusBg = '#f9fafb';
                        $adStatusText = '#9ca3af';
                    } elseif ($hoursSince <= 2) {
                        $adStatusColor = '#10b981';
                        $adStatusLabel = 'Active';
                        $adStatusBg = '#d1fae5';
                        $adStatusText = '#065f46';
                    } elseif ($hoursSince <= 6) {
                        $adStatusColor = '#f59e0b';
                        $adStatusLabel = 'Idle (' . $hoursSince . 'h ago)';
                        $adStatusBg = '#fef3c7';
                        $adStatusText = '#92400e';
                    } else {
                        $adStatusColor = '#ef4444';
                        $adStatusLabel = 'Inactive (' . $hoursSince . 'h ago)';
                        $adStatusBg = '#fee2e2';
                        $adStatusText = '#991b1b';
                    }
                @endphp
                <tr>
                    <td><a href="{{ route('admin.publishers.show', $link->user) }}" style="color:#01BF63;font-weight:600;">{{ $link->user->name }}</a></td>
                    <td>{{ $link->name ?: '—' }}</td>
                    <td><code style="font-size:12px;background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ $link->unique_code }}</code></td>
                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <a href="{{ $link->original_url }}" target="_blank" style="color:#6b7280;font-size:12px;" title="{{ $link->original_url }}">{{ $link->original_url }}</a>
                    </td>
                    <td>
                        <strong>{{ number_format($link->unique_clicks) }}</strong>
                        <span style="color:#ef4444;font-size:12px;">({{ number_format($link->fraud_clicks) }} fraud)</span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="width:10px;height:10px;border-radius:50%;background:{{ $adStatusColor }};display:inline-block;flex-shrink:0;"></span>
                            <span style="background:{{ $adStatusBg }};color:{{ $adStatusText }};padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">{{ $adStatusLabel }}</span>
                        </div>
                        @if($lastClick)
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $lastClick->format('M d, H:i') }}</div>
                        @endif
                    </td>
                    <td><span class="badge {{ $link->is_active ? 'badge-success' : 'badge-danger' }}">{{ $link->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('admin.tracking.edit', $link) }}" class="btn btn-ghost btn-sm">Edit URL</a>
                            <form method="POST" action="{{ route('admin.tracking.toggle', $link) }}">@csrf<button class="btn btn-ghost btn-sm">Toggle</button></form>
                            <form method="POST" action="{{ route('admin.tracking.destroy', $link) }}" onsubmit="return confirm('Delete this link?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Del</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:24px;color:#9ca3af;">No tracking links yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $links->links() }}
</div>

@endsection
