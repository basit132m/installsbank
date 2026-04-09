<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\PublisherProfile;
use App\Models\RateIncreaseRequest;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $user      = auth()->user();
        $profile   = $user->publisherProfile;
        $contracts = \App\Models\Contract::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        $changeRequests = \App\Models\ContractChangeRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        $snapshots = \App\Models\PublisherContractSnapshot::where('user_id', $user->id)
            ->orderByDesc('changed_at')
            ->get();
        $hasPendingRequest = $changeRequests->where('status', 'pending')->isNotEmpty();

        $rateIncreaseRequests = RateIncreaseRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        $hasPendingRateRequest = $rateIncreaseRequests->where('status', 'pending')->isNotEmpty();

        return view('publisher.contracts', compact('profile', 'contracts', 'changeRequests', 'snapshots', 'hasPendingRequest', 'rateIncreaseRequests', 'hasPendingRateRequest'));
    }

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

        $profile = auth()->user()->publisherProfile;

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

        // If this is a fixed contract offered after a completed 48h test,
        // auto-credit 2 days of the daily rate as test period payment.
        if ($contract->type === 'fixed' && $profile->test_status === 'completed') {
            $testPay = round((float) $contract->rate * 2, 4);
            $profileData['balance']            = $profile->balance + $testPay;
            $profileData['total_earnings']     = $profile->total_earnings + $testPay;
            $profileData['test_payout_eligible'] = true;
        }

        $profile->update($profileData);

        $msg = 'Contract accepted! Your ad is now live.';
        if ($contract->type === 'fixed' && $profile->fresh()->test_payout_eligible) {
            $testPay = round((float) $contract->rate * 2, 4);
            $msg .= " \${$testPay} for your 2-day test period has been added to your balance.";
        }

        return back()->with('success', $msg);
    }

    public function reject(Contract $contract)
    {
        if ($contract->user_id !== auth()->id() || !$contract->isPending()) {
            abort(403);
        }

        $profile = auth()->user()->publisherProfile;

        $contract->update(['status' => 'rejected', 'responded_at' => now()]);

        // If this is a fixed contract offered after a completed 48h test,
        // auto-credit 2 days of the daily rate and allow immediate withdrawal
        // regardless of the normal threshold.
        if ($contract->type === 'fixed' && $profile->test_status === 'completed') {
            $testPay = round((float) $contract->rate * 2, 4);
            $profile->update([
                'balance'              => $profile->balance + $testPay,
                'total_earnings'       => $profile->total_earnings + $testPay,
                'test_payout_eligible' => true,
                'payment_enabled'      => true, // allow them to withdraw
            ]);

            return back()->with('success',
                "Contract declined. \${$testPay} for your 2-day test period has been added to your balance. You can withdraw this amount at any time — the minimum threshold does not apply."
            );
        }

        return back()->with('success', 'Contract rejected.');
    }
}
