<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\PublisherProfile;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function accept(Contract $contract)
    {
        if ($contract->user_id !== auth()->id() || !$contract->isPending()) {
            abort(403);
        }

        $contract->update(['status' => 'accepted', 'responded_at' => now()]);

        // Update publisher profile
        $profileData = [
            'contract_type'    => $contract->type,
            'fixed_daily_rate' => $contract->type === 'fixed' ? $contract->rate : null,
        ];

        // Fixed-rate publishers get payment_enabled automatically —
        // their balance is managed by the daily credit command, not per-click earnings.
        if ($contract->type === 'fixed') {
            $profileData['payment_enabled'] = true;
        }

        auth()->user()->publisherProfile->update($profileData);

        return back()->with('success', 'Contract accepted! Your ad is now live.');
    }

    public function reject(Contract $contract)
    {
        if ($contract->user_id !== auth()->id() || !$contract->isPending()) {
            abort(403);
        }
        $contract->update(['status' => 'rejected', 'responded_at' => now()]);
        return back()->with('success', 'Contract rejected.');
    }
}
