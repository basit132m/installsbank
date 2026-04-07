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

        // Reject all other pending contracts for this publisher
        Contract::where('user_id', $contract->user_id)
            ->where('id', '!=', $contract->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected', 'responded_at' => now()]);

        // Update publisher profile
        $profileData = match ($contract->type) {
            'fixed' => [
                'contract_type'    => 'fixed',
                'fixed_daily_rate' => $contract->rate,
                'payment_enabled'  => true,
            ],
            'installs_base' => [
                'contract_type'    => 'installs_base',
                'fixed_daily_rate' => null,
            ],
            default => [
                'contract_type'    => $contract->type,
                'fixed_daily_rate' => null,
            ],
        };

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
