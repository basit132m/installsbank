<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->paginate(10);
        $profile = $user->publisherProfile;
        return view('publisher.withdrawals.index', compact('withdrawals', 'profile'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $profile = $user->publisherProfile;

        if (!$profile->payment_enabled) {
            return back()->with('error', 'Withdrawals are not enabled for your account yet.');
        }

        if ($profile->balance < 10) {
            return back()->with('error', 'Minimum withdrawal amount is $10.00.');
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:10|max:' . $profile->balance,
            'method' => 'required|in:usdt_trc20,usdt_erc20,btc',
            'wallet_address' => 'required|string|max:200',
        ]);

        // Check for pending withdrawal
        if (Withdrawal::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return back()->with('error', 'You have a pending withdrawal request.');
        }

        Withdrawal::create(array_merge($data, ['user_id' => $user->id]));
        $profile->decrement('balance', $data['amount']);
        $profile->increment('total_withdrawn', $data['amount']);

        return back()->with('success', 'Withdrawal request submitted.');
    }
}
