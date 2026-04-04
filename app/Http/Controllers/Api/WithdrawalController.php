<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user      = auth()->user();
        $profile   = $user->publisherProfile;
        $threshold = (float) Setting::get('withdrawal_threshold', 10);
        $isWeekend = $this->isWithdrawalOpen();

        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->paginate(20);

        return response()->json([
            'balance'          => (float) ($profile?->balance ?? 0),
            'pending_balance'  => (float) ($profile?->pending_balance ?? 0),
            'total_withdrawn'  => (float) ($profile?->total_withdrawn ?? 0),
            'payment_network'  => $profile?->payment_network,
            'payment_address'  => $profile?->payment_address,
            'payment_enabled'  => (bool) ($profile?->payment_enabled ?? false),
            'threshold'        => $threshold,
            'is_weekend'       => $isWeekend,
            'has_pending'      => Withdrawal::where('user_id', $user->id)->where('status', 'pending')->exists(),
            'withdrawals'      => $withdrawals->items(),
            'total'            => $withdrawals->total(),
            'next_page_url'    => $withdrawals->nextPageUrl(),
        ]);
    }

    public function saveAddress(Request $request)
    {
        $data = $request->validate([
            'payment_network' => 'required|in:trc20,bep20',
            'payment_address' => 'required|string|max:200',
        ]);

        auth()->user()->publisherProfile->update($data);
        return response()->json(['message' => 'Payment address saved.']);
    }

    public function store(Request $request)
    {
        $user    = auth()->user();
        $profile = $user->publisherProfile;

        if (!$profile?->payment_enabled) {
            return response()->json(['message' => 'Withdrawals not enabled for your account.'], 403);
        }

        if (!$this->isWithdrawalOpen()) {
            return response()->json(['message' => 'Withdrawals are only available Saturday–Sunday (USA Eastern Time).'], 422);
        }

        $threshold = (float) Setting::get('withdrawal_threshold', 10);
        if ($profile->balance < $threshold) {
            return response()->json(['message' => "Minimum withdrawal is \${$threshold}."], 422);
        }

        if (!$profile->payment_address || !$profile->payment_network) {
            return response()->json(['message' => 'Please save your payment address first.'], 422);
        }

        if (Withdrawal::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return response()->json(['message' => 'You already have a pending withdrawal.'], 422);
        }

        $amount = $profile->balance;

        Withdrawal::create([
            'user_id'        => $user->id,
            'amount'         => $amount,
            'network'        => $profile->payment_network,
            'wallet_address' => $profile->payment_address,
            'method'         => 'usdt_' . $profile->payment_network,
            'status'         => 'pending',
            'requested_at'   => now(),
        ]);

        $profile->update([
            'pending_balance' => $profile->pending_balance + $amount,
            'balance'         => 0,
        ]);

        return response()->json(['message' => 'Withdrawal request submitted. Payments are processed before end of Sunday.']);
    }

    private function isWithdrawalOpen(): bool
    {
        $day = now()->setTimezone('America/New_York')->dayOfWeek;
        return $day === 0 || $day === 6;
    }
}
