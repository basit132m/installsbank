<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\ContractChangeRequest;
use Illuminate\Http\Request;

class ContractChangeRequestController extends Controller
{
    public function store(Request $request)
    {
        $user    = auth()->user();
        $profile = $user->publisherProfile;

        if (!$profile || !$profile->contract_type) {
            return back()->with('error', 'You do not have an active contract to change.');
        }

        // Block if there is already a pending request
        $existing = ContractChangeRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have a pending contract change request. Please wait for admin to respond.');
        }

        $data = $request->validate([
            'requested_type' => 'required|in:per_click,fixed,installs_base',
            'reason'         => 'nullable|string|max:1000',
        ]);

        if ($data['requested_type'] === $profile->contract_type) {
            return back()->with('error', 'You are already on this contract type.');
        }

        ContractChangeRequest::create([
            'user_id'        => $user->id,
            'current_type'   => $profile->contract_type,
            'requested_type' => $data['requested_type'],
            'reason'         => $data['reason'] ?? null,
            'status'         => 'pending',
        ]);

        return back()->with('success', 'Your contract change request has been submitted. Admin will review it shortly.');
    }
}
