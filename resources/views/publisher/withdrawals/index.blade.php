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

@if($profile->test_payout_eligible)
<div style="background:linear-gradient(135deg,#065f46,#059669);border-radius:14px;padding:18px 22px;margin-bottom:20px;display:flex;align-items:flex-start;gap:14px;">
    <div style="width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div>
        <div style="font-size:15px;font-weight:700;color:white;margin-bottom:4px;">Test Period Payment Ready</div>
        <div style="font-size:13px;color:rgba(255,255,255,0.85);line-height:1.6;">
            Your 2-day traffic test payment has been credited to your balance. You can withdraw it <strong>right now</strong> — the minimum withdrawal threshold does not apply to this payment.
        </div>
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
                <div style="font-size:13px;color:#059669;font-weight:600;">✓ Withdrawals are open today</div>
            @else
                <div style="font-size:13px;color:#f59e0b;font-weight:600;">⏸ Withdrawals are currently closed</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Available on: {{ $withdrawalDaysLabel }} (USA Eastern Time)</div>
            @endif
        </div>
        @if($isWeekend && ($profile->balance >= $threshold || $profile->test_payout_eligible) && $profile->balance > 0 && $profile->payment_address && !Withdrawal_pending($profile))
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
                            <span style="display:inline-flex;align-items:center;gap:6px;background:#d1fae5;color:#065f46;font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Paid
                            </span>
                        @elseif($w->status === 'pending')
                            <span style="display:inline-flex;align-items:center;gap:6px;background:#fef3c7;color:#92400e;font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Pending
                            </span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:6px;background:#fee2e2;color:#991b1b;font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Rejected
                            </span>
                        @endif
                    </td>
                    <td style="font-size:13px;">
                        @if($w->status === 'paid')
                            @if($w->receipt_hash)
                                <button onclick="showTxProof('{{ $w->receipt_hash }}','{{ $w->networkLabel() }}','{{ $w->method ?? $w->network }}','{{ number_format($w->amount,2) }}')"
                                    style="display:inline-flex;align-items:center;gap:5px;background:#f0fdf4;color:#059669;border:1px solid #86efac;border-radius:7px;padding:5px 11px;font-size:12px;font-weight:700;cursor:pointer;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    View Proof
                                </button>
                            @else
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

<!-- Transaction Proof Modal -->
<div id="txProofModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:2000;justify-content:center;align-items:center;padding:16px;">
    <div style="background:white;border-radius:16px;padding:28px 28px 24px;max-width:500px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.25);">
        <!-- Header -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:38px;height:38px;background:#d1fae5;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="#059669" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <div style="font-size:16px;font-weight:700;">Payment Proof</div>
                    <div style="font-size:12px;color:#6b7280;">Verified blockchain transaction</div>
                </div>
            </div>
            <button onclick="document.getElementById('txProofModal').style.display='none'" style="background:#f3f4f6;border:none;border-radius:8px;width:32px;height:32px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;color:#6b7280;">×</button>
        </div>

        <!-- Amount + Network badges -->
        <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
            <div style="flex:1;min-width:120px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 16px;text-align:center;">
                <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Amount Paid</div>
                <div style="font-size:22px;font-weight:800;color:#059669;" id="txAmount">—</div>
            </div>
            <div style="flex:1;min-width:120px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 16px;text-align:center;">
                <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Network</div>
                <div style="font-size:15px;font-weight:700;color:#1d4ed8;" id="txNetwork">—</div>
            </div>
        </div>

        <!-- Transaction Hash -->
        <div style="margin-bottom:20px;">
            <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Transaction Hash</div>
            <div style="display:flex;gap:8px;align-items:center;">
                <div id="txHash" style="flex:1;font-family:monospace;font-size:12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;word-break:break-all;color:#111827;line-height:1.5;">—</div>
                <button onclick="copyTxHash()" id="copyBtn" style="flex-shrink:0;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;cursor:pointer;font-size:12px;font-weight:600;color:#374151;display:flex;align-items:center;gap:5px;transition:all .15s;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    Copy
                </button>
            </div>
        </div>

        <!-- Hash warning -->
        <div id="txHashWarning" style="display:none;background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:16px;line-height:1.6;">
            ⚠️ <strong>This may not be a valid transaction hash.</strong> It looks like a wallet address was recorded instead. Please contact support to get the correct transaction ID.
        </div>

        <!-- Explorer link -->
        <a id="txExplorerLink" href="#" target="_blank" rel="noopener noreferrer"
            style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;background:#0f172a;color:white;border-radius:10px;font-size:14px;font-weight:700;text-decoration:none;transition:opacity .15s;"
            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            <span id="txExplorerLabel">View on Block Explorer</span>
        </a>

        <div style="text-align:center;margin-top:12px;font-size:11px;color:#9ca3af;">
            This transaction was recorded on the blockchain and cannot be altered.
        </div>
    </div>
</div>

<script>
const explorerMap = {
    'usdt_bep20': { url: 'https://bscscan.com/tx/', label: 'View on BscScan (BEP20)' },
    'bep20':      { url: 'https://bscscan.com/tx/', label: 'View on BscScan (BEP20)' },
    'usdt_trc20': { url: 'https://tronscan.org/#/transaction/', label: 'View on TronScan (TRC20)' },
    'trc20':      { url: 'https://tronscan.org/#/transaction/', label: 'View on TronScan (TRC20)' },
    'usdt_erc20': { url: 'https://etherscan.io/tx/', label: 'View on Etherscan (ERC20)' },
    'erc20':      { url: 'https://etherscan.io/tx/', label: 'View on Etherscan (ERC20)' },
    'btc':        { url: 'https://blockstream.info/tx/', label: 'View on Blockstream (BTC)' },
};

let currentHash = '';

function showTxProof(hash, networkLabel, networkKey, amount) {
    currentHash = hash;
    document.getElementById('txHash').textContent = hash;
    document.getElementById('txAmount').textContent = '$' + amount;
    document.getElementById('txNetwork').textContent = networkLabel;

    // Detect if hash looks like a wallet address instead of a tx hash
    const isWalletAddr = /^0x[0-9a-fA-F]{40}$/.test(hash);
    const txWarnEl = document.getElementById('txHashWarning');
    txWarnEl.style.display = isWalletAddr ? 'block' : 'none';

    const explorer = explorerMap[networkKey] || { url: 'https://bscscan.com/tx/', label: 'View on Block Explorer' };
    document.getElementById('txExplorerLink').href = explorer.url + hash;
    document.getElementById('txExplorerLabel').textContent = explorer.label;
    document.getElementById('copyBtn').innerHTML = '<svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg> Copy';
    document.getElementById('txProofModal').style.display = 'flex';
}

function copyTxHash() {
    navigator.clipboard.writeText(currentHash).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.innerHTML = '<svg width="13" height="13" fill="none" stroke="#059669" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Copied!';
        btn.style.color = '#059669';
        btn.style.borderColor = '#86efac';
        setTimeout(() => {
            btn.innerHTML = '<svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg> Copy';
            btn.style.color = '#374151';
            btn.style.borderColor = '#e5e7eb';
        }, 2000);
    });
}

document.getElementById('txProofModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>

@endsection
