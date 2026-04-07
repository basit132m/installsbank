<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudAlert;
use App\Models\User;
use Illuminate\Http\Request;

class FraudAlertController extends Controller
{
    public function index(Request $request)
    {
        $resolved = $request->get('resolved', '0') === '1';

        // Group by publisher — one row per publisher
        $publisherIds = FraudAlert::where('is_resolved', $resolved)
            ->distinct()
            ->pluck('user_id');

        $publishers = User::whereIn('id', $publisherIds)
            ->with(['publisherProfile'])
            ->get()
            ->map(function ($user) use ($resolved) {
                $alerts = FraudAlert::where('user_id', $user->id)
                    ->where('is_resolved', $resolved)
                    ->get();

                $user->fraud_total       = $alerts->count();
                $user->fraud_occurrences = $alerts->sum('occurrences');
                $user->fraud_types       = $alerts->groupBy('alert_type')
                    ->map(fn($g) => $g->count())
                    ->toArray();
                $user->last_alert_at     = $alerts->max('created_at');
                return $user;
            })
            ->sortByDesc('last_alert_at');

        return view('admin.fraud.index', compact('publishers', 'resolved'));
    }

    public function show(User $user, Request $request)
    {
        $resolved = $request->get('resolved', '0') === '1';

        $alerts = FraudAlert::where('user_id', $user->id)
            ->where('is_resolved', $resolved)
            ->with('trackingLink')
            ->latest()
            ->paginate(30);

        return view('admin.fraud.show', compact('user', 'alerts', 'resolved'));
    }

    public function resolve(FraudAlert $fraudAlert)
    {
        $fraudAlert->update(['is_resolved' => true, 'resolved_by' => auth()->id(), 'resolved_at' => now()]);
        return back()->with('success', 'Alert resolved.');
    }

    public function resolveAll(Request $request)
    {
        FraudAlert::where('is_resolved', false)->update([
            'is_resolved' => true,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);
        return back()->with('success', 'All alerts resolved.');
    }

    public function purgeOld()
    {
        $deleted = FraudAlert::where('created_at', '<', now()->subHours(24))->delete();
        return back()->with('success', "Purged {$deleted} fraud alert(s) older than 24 hours.");
    }
}
