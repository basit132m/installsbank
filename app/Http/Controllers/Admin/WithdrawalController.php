<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdrawal::with('user');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $withdrawals = $query->latest()->paginate(20);
        $threshold = Setting::get('withdrawal_threshold', 10);
        return view('admin.withdrawals.index', compact('withdrawals', 'threshold'));
    }

    public function updateThreshold(Request $request)
    {
        $data = $request->validate(['threshold' => 'required|numeric|min:1']);
        Setting::set('withdrawal_threshold', $data['threshold']);
        return back()->with('success', 'Withdrawal threshold updated to $' . number_format($data['threshold'], 2) . '.');
    }

    public function approve(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal is not in pending state.');
        }

        $data = $request->validate([
            'receipt_hash' => 'nullable|string|max:200',
            'receipt_note' => 'nullable|string|max:500',
        ]);

        $withdrawal->update([
            'status'       => 'paid',
            'receipt_hash' => $data['receipt_hash'] ?? null,
            'receipt_note' => $data['receipt_note'] ?? null,
            'processed_at' => now(),
        ]);

        // Move pending_balance to total_withdrawn, keep balance=0
        $profile = $withdrawal->user->publisherProfile;
        if ($profile) {
            $profile->decrement('pending_balance', $withdrawal->amount);
            $profile->increment('total_withdrawn', $withdrawal->amount);
        }

        return back()->with('success', 'Withdrawal approved and marked as paid.');
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal is not in pending state.');
        }

        $data = $request->validate(['admin_note' => 'required|string|max:500']);

        $withdrawal->update([
            'status'       => 'rejected',
            'admin_note'   => $data['admin_note'],
            'processed_at' => now(),
        ]);

        // Refund: move pending_balance back to balance
        $profile = $withdrawal->user->publisherProfile;
        if ($profile) {
            $profile->decrement('pending_balance', $withdrawal->amount);
            $profile->increment('balance', $withdrawal->amount);
        }

        return back()->with('success', 'Withdrawal rejected and balance refunded.');
    }
}
