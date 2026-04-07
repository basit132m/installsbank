@extends('layouts.admin')
@section('title', 'Withdrawals')
@section('page-title', 'Withdrawal Requests')

@section('content')

<!-- Threshold Settings Card -->
<div class="card mb-4">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
        <div>
            <div style="font-size:15px;font-weight:700;color:#111827;margin-bottom:2px;">Withdrawal Threshold</div>
            <div style="font-size:13px;color:#6b7280;">Minimum balance publishers must reach before they can request a withdrawal.</div>
        </div>
        <form method="POST" action="{{ route('admin.withdrawals.threshold') }}" style="display:flex;gap:10px;align-items:center;">
            @csrf
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:16px;font-weight:700;color:#9ca3af;">$</span>
                <input type="number" name="threshold" class="form-control" value="{{ $threshold }}" min="1" step="0.01" style="width:110px;">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Update</button>
        </form>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
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
            <thead>
                <tr>
                    <th>Publisher</th>
                    <th>Amount</th>
                    <th>Network</th>
                    <th>Wallet Address</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                <tr>
                    <td><a href="{{ route('admin.publishers.show', $w->user) }}" style="color:#01BF63;font-weight:600;">{{ $w->user->name }}</a></td>
                    <td><strong>${{ number_format($w->amount, 2) }}</strong></td>
                    <td>
                        <span class="badge badge-info">{{ $w->networkLabel() }}</span>
                    </td>
                    <td style="font-family:monospace;font-size:12px;color:#6b7280;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $w->wallet_address }}">
                        {{ $w->wallet_address }}
                    </td>
                    <td>
                        @if($w->status === 'paid')<span class="badge badge-success">Paid</span>
                        @elseif($w->status === 'pending')<span class="badge badge-warning">Pending</span>
                        @else<span class="badge badge-danger">Rejected</span>@endif
                    </td>
                    <td style="color:#9ca3af;font-size:13px;">{{ $w->requested_at?->format('M d, Y') }}</td>
                    <td>
                        @if($w->status === 'pending')
                        <div style="display:flex;gap:6px;">
                            <button onclick="document.getElementById('approve-{{ $w->id }}').style.display='flex'" class="btn btn-success btn-sm">Approve</button>
                            <button onclick="document.getElementById('reject-{{ $w->id }}').style.display='flex'" class="btn btn-danger btn-sm">Reject</button>
                        </div>
                        @elseif($w->status === 'paid')
                        <div style="font-size:12px;color:#6b7280;">
                            <div>Paid {{ $w->processed_at?->format('M d, Y') }}</div>
                            @if($w->receipt_hash)
                                <div style="font-family:monospace;margin-top:2px;color:#01BF63;" title="{{ $w->receipt_hash }}">TX ID: {{ substr($w->receipt_hash, 0, 16) }}...</div>
                            @endif
                        </div>
                        @else
                        <span style="color:#9ca3af;font-size:13px;">Rejected {{ $w->processed_at?->format('M d') }}</span>
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
    <div style="background:white;border-radius:16px;padding:32px;max-width:460px;width:90%;">
        <div style="font-size:18px;font-weight:700;margin-bottom:4px;">Approve Withdrawal</div>
        <div style="font-size:13px;color:#6b7280;margin-bottom:20px;">
            <strong>{{ $w->user->name }}</strong> — ${{ number_format($w->amount, 2) }} via {{ $w->networkLabel() }}<br>
            <span style="font-family:monospace;word-break:break-all;color:#374151;">{{ $w->wallet_address }}</span>
        </div>
        <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Transaction ID (optional)</label>
                <input type="text" name="receipt_hash" id="txInput-{{ $w->id }}" class="form-control" placeholder="Paste blockchain transaction ID..." oninput="validateTxHash(this, '{{ $w->id }}')">
                <div id="txWarn-{{ $w->id }}" style="display:none;margin-top:6px;background:#fee2e2;border:1px solid #fca5a5;border-radius:7px;padding:9px 12px;font-size:12px;color:#991b1b;line-height:1.5;">
                    ⚠️ <strong>This looks like a wallet address, not a transaction ID.</strong><br>
                    A BEP20/ERC20 transaction hash is <strong>66 characters</strong> long (starts with <code>0x</code> followed by 64 hex digits).<br>
                    A wallet address is only 42 characters. Please paste the correct TX hash from your exchange or wallet.
                </div>
                <div id="txOk-{{ $w->id }}" style="display:none;margin-top:6px;background:#d1fae5;border:1px solid #86efac;border-radius:7px;padding:7px 12px;font-size:12px;color:#065f46;">
                    ✓ Looks like a valid transaction hash.
                </div>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Publisher will see this as payment proof. Make sure it's a TX hash, not a wallet address.</div>
            </div>
            <div style="display:flex;gap:8px;margin-top:8px;">
                <button type="submit" class="btn btn-success">Mark as Paid</button>
                <button type="button" onclick="document.getElementById('approve-{{ $w->id }}').style.display='none'" class="btn btn-ghost">Cancel</button>
            </div>
        </form>
    </div>
</div>
<!-- Reject Modal -->
<div id="reject-{{ $w->id }}" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;">
    <div style="background:white;border-radius:16px;padding:32px;max-width:420px;width:90%;">
        <div style="font-size:18px;font-weight:700;margin-bottom:4px;">Reject Withdrawal</div>
        <div style="font-size:13px;color:#6b7280;margin-bottom:16px;"><strong>{{ $w->user->name }}</strong> — ${{ number_format($w->amount, 2) }}</div>
        <form method="POST" action="{{ route('admin.withdrawals.reject', $w) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Reason <span style="color:#ef4444;">*</span></label>
                <textarea name="admin_note" class="form-control" rows="3" required placeholder="Reason for rejection..."></textarea>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">This message will be visible to the publisher. Balance will be refunded.</div>
            </div>
            <div style="display:flex;gap:8px;margin-top:8px;">
                <button type="submit" class="btn btn-danger">Reject & Refund</button>
                <button type="button" onclick="document.getElementById('reject-{{ $w->id }}').style.display='none'" class="btn btn-ghost">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach
@endsection

<script>
function validateTxHash(input, id) {
    const val = input.value.trim();
    const warn = document.getElementById('txWarn-' + id);
    const ok   = document.getElementById('txOk-'   + id);
    if (!val) { warn.style.display = 'none'; ok.style.display = 'none'; return; }

    // EVM wallet address: 0x + 40 hex chars = 42 total
    const isWalletAddr = /^0x[0-9a-fA-F]{40}$/.test(val);
    // EVM tx hash: 0x + 64 hex chars = 66 total
    const isEvmTx = /^0x[0-9a-fA-F]{64}$/.test(val);
    // TRC20 tx hash: 64 hex chars (no 0x)
    const isTronTx = /^[0-9a-fA-F]{64}$/.test(val);
    // BTC tx hash: 64 hex chars
    const isBtcTx  = isTronTx;

    if (isWalletAddr) {
        warn.style.display = 'block';
        ok.style.display   = 'none';
        input.style.borderColor = '#ef4444';
    } else if (isEvmTx || isTronTx) {
        warn.style.display = 'none';
        ok.style.display   = 'block';
        input.style.borderColor = '#10b981';
    } else {
        warn.style.display = 'none';
        ok.style.display   = 'none';
        input.style.borderColor = '';
    }
}
</script>
