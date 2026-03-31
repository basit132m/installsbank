@extends('layouts.admin')
@section('title', 'Tracking Links')
@section('page-title', 'Tracking Links')

@section('content')
<div class="flex-between mb-6">
    <p class="text-muted text-sm">Manage all tracking links across publishers</p>
    <a href="{{ route('admin.tracking.create') }}" class="btn btn-primary">+ Create Link</a>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Publisher</th><th>Name</th><th>Code</th><th>Destination</th><th>Clicks</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($links as $link)
                <tr>
                    <td><a href="{{ route('admin.publishers.show', $link->user) }}" style="color:#01BF63;font-weight:600;">{{ $link->user->name }}</a></td>
                    <td>{{ $link->name ?: '—' }}</td>
                    <td><code style="font-size:12px;background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ $link->unique_code }}</code></td>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#6b7280;font-size:13px;">{{ $link->original_url }}</td>
                    <td><strong>{{ number_format($link->unique_clicks) }}</strong> <span style="color:#ef4444;font-size:12px;">({{ number_format($link->fraud_clicks) }} fraud)</span></td>
                    <td><span class="badge {{ $link->is_active ? 'badge-success' : 'badge-danger' }}">{{ $link->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td style="display:flex;gap:6px;">
                        <form method="POST" action="{{ route('admin.tracking.toggle', $link) }}">@csrf<button class="btn btn-ghost btn-sm">Toggle</button></form>
                        <form method="POST" action="{{ route('admin.tracking.destroy', $link) }}" onsubmit="return confirm('Delete this link?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Del</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:24px;color:#9ca3af;">No tracking links yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $links->links() }}
</div>
@endsection
