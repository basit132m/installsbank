<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->paginate(10);
        $profile = $user->publisherProfile;
        $threshold = (float) Setting::get('withdrawal_threshold', 10);
        $isWeekend = $this->isWithdrawalOpen();
        $withdrawalDaysLabel = self::withdrawalDaysLabel();
        return view('publisher.withdrawals.index', compact('withdrawals', 'profile', 'threshold', 'isWeekend', 'withdrawalDaysLabel'));
    }

    public function saveAddress(Request $request)
    {
        $data = $request->validate([
            'payment_network' => 'required|in:trc20,bep20',
            'payment_address' => 'required|string|max:200',
        ]);

        auth()->user()->publisherProfile->update($data);
        return back()->with('success', 'Payment address saved.');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $profile = $user->publisherProfile;

        if (!$profile->payment_enabled) {
            return back()->with('error', 'Withdrawals are not enabled for your account yet.');
        }

        if (!$this->isWithdrawalOpen()) {
            $days = self::withdrawalDaysLabel();
            return back()->with('error', "Withdrawals are only available on: {$days} (USA Eastern Time).");
        }

        $threshold = (float) Setting::get('withdrawal_threshold', 10);

        // Test-period payout: bypass threshold once for publishers who completed the 48h test
        if (!$profile->test_payout_eligible && $profile->balance < $threshold) {
            return back()->with('error', "Minimum withdrawal amount is \${$threshold}.");
        }

        if (!$profile->payment_address || !$profile->payment_network) {
            return back()->with('error', 'Please save your payment address first.');
        }

        // Check for pending withdrawal
        if (Withdrawal::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return back()->with('error', 'You already have a pending withdrawal request.');
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

        // Move balance to pending; clear the one-time threshold bypass flag
        $profile->update([
            'pending_balance'      => $profile->pending_balance + $amount,
            'balance'              => 0,
            'test_payout_eligible' => false,
        ]);

        return back()->with('success', 'Withdrawal request submitted. Your balance is now pending. Payments are processed before the end of Sunday.');
    }

    private function isWithdrawalOpen(): bool
    {
        $days = json_decode(Setting::get('withdrawal_days', '[0,6]'), true);
        $dayOfWeek = now()->setTimezone('America/New_York')->dayOfWeek;
        return in_array($dayOfWeek, $days);
    }

    public static function withdrawalDaysLabel(): string
    {
        $days = json_decode(Setting::get('withdrawal_days', '[0,6]'), true);
        $names = [0=>'Sunday',1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday'];
        if (empty($days)) return 'No days configured';
        return implode(', ', array_map(fn($d) => $names[$d] ?? $d, $days));
    }
}
