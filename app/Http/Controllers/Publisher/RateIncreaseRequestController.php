<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\RateIncreaseRequest;
use Illuminate\Http\Request;

class RateIncreaseRequestController extends Controller
{
    public function store(Request $request)
    {
        $user    = auth()->user();
        $profile = $user->publisherProfile;

        if (!$profile || $profile->contract_type !== 'fixed') {
            return back()->with('error', 'Rate increase requests are only available for Fixed Daily Rate publishers.');
        }

        if (!$profile->fixed_daily_rate) {
            return back()->with('error', 'No fixed daily rate is currently set on your account.');
        }

        $hasPending = RateIncreaseRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'You already have a pending rate increase request. Please wait for admin to respond.');
        }

        $data = $request->validate([
            'requested_rate'  => 'required|numeric|min:0.0001|max:9999',
            'justification'   => 'nullable|string|max:2000',
            'screenshots.*'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ((float) $data['requested_rate'] <= (float) $profile->fixed_daily_rate) {
            return back()->with('error', 'Requested rate must be higher than your current rate of $' . number_format($profile->fixed_daily_rate, 4) . '/day.');
        }

        $paths = [];
        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $paths[] = $file->store('stat-screenshots', 'public');
            }
        }

        RateIncreaseRequest::create([
            'user_id'          => $user->id,
            'current_rate'     => $profile->fixed_daily_rate,
            'requested_rate'   => $data['requested_rate'],
            'justification'    => $data['justification'] ?? null,
            'stats_screenshots'=> $paths ?: null,
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Rate increase request submitted. Admin will review and respond.');
    }
}
