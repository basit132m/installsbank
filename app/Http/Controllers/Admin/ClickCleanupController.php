<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\FraudAlert;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClickCleanupController extends Controller
{
    public function index()
    {
        // Clicks grouped by month (reads the created_at index)
        $months = Click::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as cnt, MIN(created_at) as first_at, MAX(created_at) as last_at")
            ->groupBy('ym')
            ->orderByDesc('ym')
            ->get();

        $currentMonth = now()->format('Y-m');

        return view('admin.clicks-cleanup.index', compact('months', 'currentMonth'));
    }

    public function destroy(Request $request, string $month)
    {
        // month = YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return back()->with('error', 'Invalid month.');
        }

        try {
            $start = Carbon::createFromFormat('Y-m-d', $month . '-01')->startOfMonth();
        } catch (\Throwable $e) {
            return back()->with('error', 'Invalid month.');
        }
        $end = $start->copy()->addMonth();

        @set_time_limit(0);
        $deadline = microtime(true) + 20; // stay within a safe request budget

        // Delete clicks in batches so a huge month never times out / locks
        $deletedClicks = 0;
        do {
            $n = Click::where('created_at', '>=', $start)
                ->where('created_at', '<', $end)
                ->limit(3000)->delete();
            $deletedClicks += $n;
        } while ($n > 0 && microtime(true) < $deadline);

        // Delete that month's fraud alerts (click-derived) in batches
        $deletedAlerts = 0;
        do {
            $n = FraudAlert::where('created_at', '>=', $start)
                ->where('created_at', '<', $end)
                ->limit(3000)->delete();
            $deletedAlerts += $n;
        } while ($n > 0 && microtime(true) < $deadline);

        $remaining = Click::where('created_at', '>=', $start)->where('created_at', '<', $end)->count();

        $label = $start->format('F Y');
        if ($remaining > 0) {
            return back()->with('success',
                "Deleted " . number_format($deletedClicks) . " clicks and " . number_format($deletedAlerts) .
                " fraud alerts for {$label}. " . number_format($remaining) .
                " clicks still remain — click Delete again to continue clearing this month.");
        }

        return back()->with('success',
            "{$label} fully cleared — deleted " . number_format($deletedClicks) . " clicks and " .
            number_format($deletedAlerts) . " fraud alerts. Earnings, balances and withdrawals are unaffected.");
    }
}
