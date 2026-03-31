@extends('layouts.admin')
@section('title', 'Withdrawals')
@section('page-title', 'Withdrawal Requests')

@section('content')
<!-- Filter -->
<div class="card mb-4">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;">
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-control form-select">
                <option value="">All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Publisher</th><th>Amount</th><th>Method</th><th>Wallet</th><th>Status</th><th>Requested</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($withdrawals as $w)
                <tr>
                    <td><a href="{{ route('admin.publishers.show', $w->user) }}" style="color:#01BF63;font-weight:600;">{{ $w->user->name }}</a></td>
                    <td><strong>${{ number_format($w->amount, 4) }}</strong></td>
                    <td><span class="badge badge-info">{{ strtoupper($w->method) }}</span></td>
                    <td style="font-family:monospace;font-size:12px;color:#6b7280;max-width:120px;overflow:hidden;text-overflow:ellipsis;">{{ $w->wallet_address }}</td>
                    <td>
                        @if($w->status === 'paid')<span class="badge badge-success">Paid</span>
                        @elseif($w->status === 'pending')<span class="badge badge-warning">Pending</span>
                        @elseif($w->status === 'approved')<span class="badge badge-info">Approved</span>
                        @else<span class="badge badge-danger">Rejected</span>@endif
                    </td>
                    <td style="color:#9ca3af;font-size:13px;">{{ $w->requested_at->format('M d, Y') }}</td>
                    <td>
                        @if($w->status === 'pending')
                        <div style="display:flex;gap:6px;">
                            <button onclick="document.getElementById('approve-{{ $w->id }}').style.display='flex'" class="btn btn-success btn-sm">Approve</button>
                            <button onclick="document.getElementById('reject-{{ $w->id }}').style.display='flex'" class="btn btn-danger btn-sm">Reject</button>
                        </div>
                        @else
                        <span style="color:#9ca3af;font-size:13px;">{{ $w->processed_at?->format('M d') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:24px;color:#9ca3af;">No withdrawal requests</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $withdrawals->links() }}
</div>

@foreach($withdrawals as $w)
@if($w->status === 'pending')
<!-- Approve Modal -->
<div id="approve-{{ $w->id }}" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;">
    <div style="background:white;border-radius:16px;padding:32px;max-width:400px;width:90%;">
        <div style="font-size:18px;font-weight:700;margin-bottom:16px;">Approve Withdrawal</div>
        <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}">
            @csrf
            <div class="form-group"><label class="form-label">Transaction Hash (Optional)</label><input type="text" name="transaction_hash" class="form-control" placeholder="TxHash..."></div>
            <div class="form-group"><label class="form-label">Note (Optional)</label><input type="text" name="admin_note" class="form-control"></div>
            <div style="display:flex;gap:8px;"><button type="submit" class="btn btn-success">Mark as Paid</button><button type="button" onclick="document.getElementById('approve-{{ $w->id }}').style.display='none'" class="btn btn-ghost">Cancel</button></div>
        </form>
    </div>
</div>
<!-- Reject Modal -->
<div id="reject-{{ $w->id }}" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;">
    <div style="background:white;border-radius:16px;padding:32px;max-width:400px;width:90%;">
        <div style="font-size:18px;font-weight:700;margin-bottom:16px;">Reject Withdrawal</div>
        <form method="POST" action="{{ route('admin.withdrawals.reject', $w) }}">
            @csrf
            <div class="form-group"><label class="form-label">Reason (Required)</label><textarea name="admin_note" class="form-control" rows="3" required></textarea></div>
            <div style="display:flex;gap:8px;"><button type="submit" class="btn btn-danger">Reject & Refund</button><button type="button" onclick="document.getElementById('reject-{{ $w->id }}').style.display='none'" class="btn btn-ghost">Cancel</button></div>
        </form>
    </div>
</div>
@endif
@endforeach
@endsection
