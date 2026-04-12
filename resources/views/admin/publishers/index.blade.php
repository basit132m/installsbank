@extends('layouts.admin')
@section('title', 'Publishers')
@section('page-title', 'Publishers')

@section('content')
<div class="flex-between mb-6">
    <div>
        <p class="text-muted text-sm">Manage all registered publishers</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or email...">
        </div>
        <div style="min-width:140px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control form-select">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('admin.publishers.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Publisher</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Contract</th>
                    <th>Balance</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($publishers as $pub)
                <tr>
                    <td>
                        <div style="font-weight:600;color:#111827;">{{ $pub->name }}</div>
                        <div style="font-size:12px;color:#9ca3af;">{{ $pub->website ?? 'No website' }}</div>
                    </td>
                    <td style="color:#6b7280;font-size:13px;">{{ $pub->email }}</td>
                    <td>
                        @if($pub->status === 'active')<span class="badge badge-success">Active</span>
                        @elseif($pub->status === 'pending')<span class="badge badge-warning">Pending</span>
                        @else<span class="badge badge-danger">Suspended</span>@endif
                    </td>
                    <td>
                        @if($pub->publisherProfile)
                            @if($pub->publisherProfile->contract_type === 'per_click')
                                <span class="badge badge-primary">Per Click</span>
                            @elseif($pub->publisherProfile->contract_type === 'fixed')
                                <span class="badge badge-info">Fixed</span>
                            @elseif($pub->publisherProfile->contract_type === 'installs_base')
                                <span class="badge badge-success">Installs</span>
                            @else
                                <span class="badge badge-gray">None</span>
                            @endif
                            @if($pub->publisherProfile->adcode_requested_at)
                                @if($pub->trackingLinks->isNotEmpty())
                                    <span class="badge badge-success" title="Ad code assigned">Ad Assigned</span>
                                @else
                                    <span class="badge badge-warning" title="Ad code requested {{ $pub->publisherProfile->adcode_requested_at->diffForHumans() }}">Adcode Req.</span>
                                @endif
                            @endif
                        @else
                            <span class="badge badge-gray">—</span>
                        @endif
                    </td>
                    <td style="font-weight:600;color:#01BF63;">
                        ${{ number_format($pub->publisherProfile?->balance ?? 0, 2) }}
                    </td>
                    <td style="color:#9ca3af;font-size:13px;">{{ $pub->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.publishers.show', $pub) }}" class="btn btn-primary btn-sm">Manage</a>
                            <a href="{{ route('admin.publishers.stats', $pub) }}" class="btn btn-ghost btn-sm">Stats</a>
                            @if($pub->status === 'pending')
                                <form method="POST" action="{{ route('admin.publishers.activate', $pub) }}">@csrf<button class="btn btn-success btn-sm">Activate</button></form>
                            @elseif($pub->status === 'active')
                                <form method="POST" action="{{ route('admin.publishers.suspend', $pub) }}">@csrf<button class="btn btn-danger btn-sm">Suspend</button></form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:48px;color:#9ca3af;">No publishers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $publishers->withQueryString()->links() }}</div>
</div>
@endsection
