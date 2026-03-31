<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve(Request $request, Withdrawal $withdrawal)
    {
        $data = $request->validate(['transaction_hash' => 'nullable|string', 'admin_note' => 'nullable|string']);
        $withdrawal->update([
            'status' => 'paid',
            'transaction_hash' => $data['transaction_hash'] ?? null,
            'admin_note' => $data['admin_note'] ?? null,
            'processed_at' => now(),
        ]);
        $withdrawal->user->publisherProfile?->decrement('total_withdrawn', -$withdrawal->amount);
        return back()->with('success', 'Withdrawal approved and marked as paid.');
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $data = $request->validate(['admin_note' => 'required|string']);
        $withdrawal->update(['status' => 'rejected', 'admin_note' => $data['admin_note'], 'processed_at' => now()]);
        // Refund balance
        $withdrawal->user->publisherProfile?->increment('balance', $withdrawal->amount);
        return back()->with('success', 'Withdrawal rejected and balance refunded.');
    }
}
