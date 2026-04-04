@extends('layouts.publisher')
@section('title', 'Withdrawals')
@section('page-title', 'Withdrawals')

@section('content')

@if(!$profile->payment_enabled)
<div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:20px 24px;margin-bottom:24px;display:flex;gap:14px;align-items:flex-start;">
    <div style="font-size:22px;margin-top:1px;">⏳</div>
    <div>
        <div style="font-weight:700;color:#92400e;margin-bottom:4px;">Withdrawals Not Yet Enabled</div>
        <div style="font-size:14px;color:#78350f;">Payments are not yet enabled for your account. Once your account is verified and you have an active contract, our team will enable withdrawal access. Contact support if you have questions.</div>
    </div>
</div>
@endif

<!-- Balance Overview -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
    <div class="card" style="border:2px solid #01BF63;text-align:center;padding:24px 16px;">
        <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">Available Balance</div>
        <div style="font-size:30px;font-weight:800;color:#01BF63;">${{ number_format($profile->balance, 2) }}</div>
        @if($profile->balance < $threshold)
            <div style="font-size:12px;color:#f59e0b;margin-top:6px;">Need ${{ number_format($threshold - $profile->balance, 2) }} more to withdraw</div>
        @endif
    </div>
    <div class="card" style="text-align:center;padding:24px 16px;">
        <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">Pending Payout</div>
        <div style="font-size:30px;font-weight:800;color:#f59e0b;">${{ number_format($profile->pending_balance ?? 0, 2) }}</div>
        @if(($profile->pending_balance ?? 0) > 0)
            <div style="font-size:12px;color:#9ca3af;margin-top:6px;">Being processed</div>
        @endif
    </div>
    <div class="card" style="text-align:center;padding:24px 16px;">
        <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">Total Paid Out</div>
        <div style="font-size:30px;font-weight:800;color:#374151;">${{ number_format($profile->total_withdrawn, 2) }}</div>
    </div>
</div>

<!-- Payment Address Card -->
<div class="card mb-6">
    <div class="card-title mb-4">Payment Address</div>
    <form method="POST" action="{{ route('publisher.withdrawals.save-address') }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Network</label>
                <select name="payment_network" class="form-control form-select" required>
                    <option value="">Select network</option>
                    <option value="trc20" {{ $profile->payment_network === 'trc20' ? 'selected' : '' }}>USDT — TRC20 (Tron)</option>
                    <option value="bep20" {{ $profile->payment_network === 'bep20' ? 'selected' : '' }}>USDT — BEP20 (BSC)</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">USDT Wallet Address</label>
                <input type="text" name="payment_address" class="form-control" placeholder="Your USDT wallet address" value="{{ $profile->payment_address ?? '' }}" required style="font-family:monospace;">
            </div>
            <button type="submit" class="btn btn-primary">Save Address</button>
        </div>
        <div style="font-size:12px;color:#9ca3af;margin-top:10px;">
            Double-check your wallet address before saving. Payments sent to an incorrect address cannot be recovered.
        </div>
    </form>
</div>

<!-- Withdrawal Request Section -->
@if($profile->payment_enabled)
<div class="card mb-6" style="{{ $isWeekend ? 'border:2px solid #01BF63;' : '' }}">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
        <div>
            <div class="card-title mb-1">Request Withdrawal</div>
            @if($isWeekend)
                <div style="font-size:13px;color:#059669;font-weight:600;">✓ Withdrawals are open today (Weekend)</div>
            @else
                @php
                    $nyNow = now()->setTimezone('America/New_York');
                    $daysToSat = (6 - $nyNow->dayOfWeek + 7) % 7 ?: 7;
                    $satDate = $nyNow->copy()->addDays($daysToSat)->format('M j');
                @endphp
                <div style="font-size:13px;color:#f59e0b;font-weight:600;">⏸ Withdrawals open on weekends only (Sat–Sun, USA Eastern Time)</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Next window opens Saturday, {{ $satDate }}</div>
            @endif
        </div>
        @if($isWeekend && $profile->balance >= $threshold && $profile->payment_address && !Withdrawal_pending($profile))
        <button onclick="document.getElementById('withdrawModal').style.display='flex'" class="btn btn-primary">
            Request Withdrawal
        </button>
        @endif
    </div>

    <div style="margin-top:16px;padding:14px 18px;background:#f0fdf7;border-radius:10px;font-size:13px;color:#065f46;line-height:1.7;">
        <strong>How it works:</strong><br>
        1. Your full available balance is submitted as a single withdrawal request.<br>
        2. Your balance moves to <em>Pending</em> while the request is being processed.<br>
        3. Payments are sent to your saved USDT address before the end of Sunday (USA Eastern Time).<br>
        4. Once paid, you'll see the transaction receipt here.
    </div>

    @php
        $hasPending = \App\Models\Withdrawal::where('user_id', auth()->id())->where('status', 'pending')->exists();
    @endphp

    @if($hasPending)
    <div style="margin-top:14px;background:#fff7ed;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;font-weight:600;">
        ⏳ You have a pending withdrawal request. New requests cannot be submitted until it's processed.
    </div>
    @elseif(!$profile->payment_address)
    <div style="margin-top:14px;background:#fff7ed;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;">
        Please save your payment address above before requesting a withdrawal.
    </div>
    @elseif($profile->balance < $threshold)
    <div style="margin-top:14px;background:#f9fafb;border-radius:8px;padding:12px 16px;font-size:13px;color:#6b7280;">
        Minimum withdrawal threshold is <strong>${{ number_format($threshold, 2) }}</strong>. Your current balance is <strong>${{ number_format($profile->balance, 2) }}</strong>.
    </div>
    @endif
</div>
@endif

<!-- Withdrawal History -->
<div class="card">
    <div class="card-title mb-4">Withdrawal History</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Network</th>
                    <th>Status</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                <tr>
                    <td style="color:#6b7280;font-size:13px;">{{ $w->requested_at?->format('M d, Y') ?? $w->created_at->format('M d, Y') }}</td>
                    <td><strong>${{ number_format($w->amount, 2) }}</strong></td>
                    <td><span class="badge badge-info">{{ $w->networkLabel() }}</span></td>
                    <td>
                        @if($w->status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($w->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </td>
                    <td style="font-size:13px;">
                        @if($w->status === 'paid')
                            @if($w->receipt_hash)
                                <span style="font-family:monospace;color:#01BF63;" title="{{ $w->receipt_hash }}">
                                    {{ substr($w->receipt_hash, 0, 16) }}...
                                </span>
                            @endif
                            @if($w->receipt_note)
                                <div style="color:#6b7280;font-size:12px;margin-top:2px;">{{ $w->receipt_note }}</div>
                            @endif
                            @if(!$w->receipt_hash && !$w->receipt_note)
                                <span style="color:#9ca3af;">—</span>
                            @endif
                        @elseif($w->status === 'rejected' && $w->admin_note)
                            <span style="color:#ef4444;font-size:12px;">{{ $w->admin_note }}</span>
                        @else
                            <span style="color:#9ca3af;">—</span>
                        @endif
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

<!-- Withdrawal Confirmation Modal -->
<div id="withdrawModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;">
    <div style="background:white;border-radius:16px;padding:32px;max-width:420px;width:90%;">
        <div style="font-size:18px;font-weight:700;margin-bottom:8px;">Confirm Withdrawal Request</div>
        <div style="font-size:14px;color:#6b7280;margin-bottom:20px;">
            Your full balance of <strong style="color:#01BF63;">${{ number_format($profile->balance, 2) }}</strong> will be submitted for withdrawal.<br>
            <span style="font-size:13px;margin-top:8px;display:block;">
                Network: <strong>{{ $profile->payment_network ? strtoupper($profile->payment_network) : '—' }}</strong><br>
                Address: <span style="font-family:monospace;word-break:break-all;">{{ $profile->payment_address ?? '—' }}</span>
            </span>
        </div>
        <form method="POST" action="{{ route('publisher.withdrawals.store') }}">
            @csrf
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Submit Request</button>
                <button type="button" onclick="document.getElementById('withdrawModal').style.display='none'" class="btn btn-ghost">Cancel</button>
            </div>
        </form>
    </div>
</div>

@php
function Withdrawal_pending($profile) {
    return \App\Models\Withdrawal::where('user_id', $profile->user_id)->where('status', 'pending')->exists();
}
@endphp

@endsection
