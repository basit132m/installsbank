@extends('layouts.publisher')
@section('title', 'Withdrawals')
@section('page-title', 'Withdrawals')

@section('content')
@if(!$profile->payment_enabled)
    <div class="alert alert-warning">Withdrawals are not enabled for your account yet. Contact support or wait for admin to enable payments.</div>
@else
<!-- Balance Card -->
<div class="card mb-6" style="border:2px solid #01BF63;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
        <div>
            <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">Available Balance</div>
            <div style="font-size:32px;font-weight:800;color:#01BF63;">${{ number_format($profile->balance, 4) }}</div>
            <div style="font-size:13px;color:#9ca3af;margin-top:4px;">Total Earned: ${{ number_format($profile->total_earnings, 4) }} · Withdrawn: ${{ number_format($profile->total_withdrawn, 4) }}</div>
        </div>
        <button onclick="document.getElementById('withdrawModal').style.display='flex'" class="btn btn-primary" {{ $profile->balance < 10 ? 'disabled' : '' }}>
            Request Withdrawal
        </button>
    </div>
    @if($profile->balance < 10)
        <div style="font-size:12px;color:#f59e0b;margin-top:8px;">Minimum withdrawal amount is $10.00</div>
    @endif
</div>
@endif

<!-- History -->
<div class="card">
    <div class="card-title mb-4">Withdrawal History</div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Wallet</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($withdrawals as $w)
                <tr>
                    <td>{{ $w->requested_at->format('M d, Y') }}</td>
                    <td><strong>${{ number_format($w->amount, 4) }}</strong></td>
                    <td><span class="badge badge-info">{{ strtoupper($w->method) }}</span></td>
                    <td style="font-family:monospace;font-size:12px;color:#6b7280;">{{ substr($w->wallet_address, 0, 15) }}...</td>
                    <td>
                        @if($w->status === 'paid')<span class="badge badge-success">Paid</span>
                        @elseif($w->status === 'pending')<span class="badge badge-warning">Pending</span>
                        @elseif($w->status === 'approved')<span class="badge badge-info">Approved</span>
                        @else<span class="badge badge-danger">Rejected</span>@endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:24px;color:#9ca3af;">No withdrawal history</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $withdrawals->links() }}</div>
</div>

<!-- Withdrawal Modal -->
<div id="withdrawModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;">
    <div style="background:white;border-radius:16px;padding:32px;max-width:440px;width:90%;">
        <div style="font-size:18px;font-weight:700;margin-bottom:16px;">Request Withdrawal</div>
        <form method="POST" action="{{ route('publisher.withdrawals.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Amount (USD)</label>
                <input type="number" name="amount" class="form-control" placeholder="Min $10.00" min="10" step="0.01" max="{{ $profile->balance }}" required>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Available: ${{ number_format($profile->balance, 2) }}</div>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Method</label>
                <select name="method" class="form-control" required>
                    <option value="usdt_trc20">USDT (TRC20)</option>
                    <option value="usdt_erc20">USDT (ERC20)</option>
                    <option value="btc">Bitcoin (BTC)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Wallet Address</label>
                <input type="text" name="wallet_address" class="form-control" placeholder="Your wallet address" required>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Submit Request</button>
                <button type="button" onclick="document.getElementById('withdrawModal').style.display='none'" class="btn btn-ghost">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
