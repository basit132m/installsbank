<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\PublisherProfile;
use App\Models\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with('publisher')
            ->latest()
            ->paginate(20);
        return view('admin.contracts.index', compact('contracts'));
    }

    public function offer(Request $request, User $user)
    {
        $data = $request->validate([
            'type' => 'required|in:per_click,fixed',
            'rate' => 'required|numeric|min:0.0001',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $profile = $user->publisherProfile;

        Contract::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'rate' => $data['rate'],
            'status' => 'pending',
            'test_total_clicks' => $profile->test_total_clicks,
            'test_started_at' => $profile->test_started_at,
            'test_ended_at' => $profile->test_ended_at,
            'admin_note' => $data['admin_note'] ?? null,
            'offered_at' => now(),
        ]);

        return back()->with('success', 'Contract offered to publisher.');
    }

    public function expire(Contract $contract)
    {
        $contract->update(['status' => 'expired']);
        return back()->with('success', 'Contract marked as expired.');
    }
}
