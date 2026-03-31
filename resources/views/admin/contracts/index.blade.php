@extends('layouts.admin')
@section('title', 'Contracts')
@section('page-title', 'Contracts')

@section('content')
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Publisher</th><th>Type</th><th>Rate</th><th>Test Clicks</th><th>Status</th><th>Offered</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($contracts as $c)
                <tr>
                    <td><a href="{{ route('admin.publishers.show', $c->publisher) }}" style="color:#01BF63;font-weight:600;">{{ $c->publisher->name }}</a></td>
                    <td><span class="badge {{ $c->type === 'per_click' ? 'badge-primary' : 'badge-info' }}">{{ $c->type === 'per_click' ? 'Per 1K Clicks' : 'Fixed Daily' }}</span></td>
                    <td><strong>${{ number_format($c->rate, 4) }}</strong></td>
                    <td>{{ $c->test_total_clicks ? number_format($c->test_total_clicks) : '—' }}</td>
                    <td>
                        @if($c->status === 'accepted')<span class="badge badge-success">Accepted</span>
                        @elseif($c->status === 'pending')<span class="badge badge-warning">Pending</span>
                        @elseif($c->status === 'rejected')<span class="badge badge-danger">Rejected</span>
                        @else<span class="badge badge-gray">Expired</span>@endif
                    </td>
                    <td style="color:#9ca3af;font-size:13px;">{{ $c->offered_at?->format('M d, Y') ?? '—' }}</td>
                    <td>
                        @if($c->status === 'pending')
                            <form method="POST" action="{{ route('admin.contracts.expire', $c) }}" style="display:inline;">@csrf<button class="btn btn-ghost btn-sm">Expire</button></form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:24px;color:#9ca3af;">No contracts yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $contracts->links() }}
</div>
@endsection
