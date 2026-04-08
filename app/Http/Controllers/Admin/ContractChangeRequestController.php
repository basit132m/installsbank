<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractChangeRequest;
use App\Models\PublisherContractSnapshot;
use Illuminate\Http\Request;

class ContractChangeRequestController extends Controller
{
    public function index()
    {
        $requests = ContractChangeRequest::with('user')
            ->orderByRaw("FIELD(status,'pending','approved','rejected')")
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.contract-requests.index', compact('requests'));
    }

    public function approve(Request $request, ContractChangeRequest $contractRequest)
    {
        if (!$contractRequest->isPending()) {
            return back()->with('error', 'This request has already been responded to.');
        }

        $data = $request->validate(['admin_note' => 'nullable|string|max:500']);

        $user    = $contractRequest->user;
        $profile = $user->publisherProfile;

        // Snapshot the old contract state before switching
        PublisherContractSnapshot::create([
            'user_id'        => $user->id,
            'contract_type'  => $profile->contract_type ?? $contractRequest->current_type,
            'fixed_daily_rate'=> $profile->fixed_daily_rate,
            'total_clicks'   => \App\Models\Click::where('user_id', $user->id)->count(),
            'total_earnings' => $profile->total_earnings,
            'changed_to'     => $contractRequest->requested_type,
            'changed_at'     => now(),
        ]);

        // Apply new contract type — reset daily rate if not fixed
        $newType = $contractRequest->requested_type;
        $profile->update([
            'contract_type'    => $newType,
            'fixed_daily_rate' => $newType === 'fixed' ? $profile->fixed_daily_rate : null,
            'payment_enabled'  => true,
        ]);

        $contractRequest->update([
            'status'       => 'approved',
            'admin_note'   => $data['admin_note'] ?? null,
            'responded_at' => now(),
        ]);

        return back()->with('success', "Contract changed to {$newType} for {$user->name}.");
    }

    public function reject(Request $request, ContractChangeRequest $contractRequest)
    {
        if (!$contractRequest->isPending()) {
            return back()->with('error', 'This request has already been responded to.');
        }

        $data = $request->validate(['admin_note' => 'nullable|string|max:500']);

        $contractRequest->update([
            'status'       => 'rejected',
            'admin_note'   => $data['admin_note'] ?? null,
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Contract change request rejected.');
    }
}
