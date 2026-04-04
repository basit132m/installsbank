<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->publisherProfile;
        $contract = $user->activeContract;
        $divider = $user->clickDivider;

        // Publisher sees divided clicks (not actual)
        $todayEarning = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();

        // Fixed rate publishers are paid externally — hide per-click earnings in UI
        $showEarnings = $profile->payment_enabled && !$profile->isFixedRate();

        $stats = [
            'clicks_today' => $todayEarning?->valid_clicks ?? 0,
            'earnings_today' => $showEarnings ? ($todayEarning?->earnings ?? 0) : null,
            'balance' => $showEarnings ? $profile->balance : null,
            'total_earnings' => $showEarnings ? $profile->total_earnings : null,
            'clicks_this_week' => $this->getWeeklyClicks($user->id),
            'clicks_this_month' => $this->getMonthlyClicks($user->id),
        ];

        // Chart data - last 7 days (publisher sees divided clicks)
        $clicksChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $de = DailyEarning::where('user_id', $user->id)->where('date', $date->toDateString())->first();
            $clicksChart[] = [
                'date' => $date->format('M d'),
                'clicks' => $de?->valid_clicks ?? 0,
                'earnings' => $showEarnings ? ($de?->earnings ?? 0) : 0,
            ];
        }

        // Country breakdown today
        $countryBreakdown = null;
        if ($todayEarning && $todayEarning->country_breakdown) {
            $countryBreakdown = collect($todayEarning->country_breakdown)
                ->sortByDesc('clicks')
                ->take(10);
        }

        $pendingContract = $user->contracts()->where('status', 'pending')->latest()->first();
        $hasTestRunning = $profile->test_status === 'running';

        return view('publisher.dashboard', compact(
            'user', 'profile', 'contract', 'divider',
            'stats', 'clicksChart', 'countryBreakdown',
            'pendingContract', 'hasTestRunning', 'showEarnings'
        ));
    }

    private function getWeeklyClicks(int $userId): int
    {
        return DailyEarning::where('user_id', $userId)
            ->whereBetween('date', [now()->startOfWeek(), now()])
            ->sum('valid_clicks');
    }

    private function getMonthlyClicks(int $userId): int
    {
        return DailyEarning::where('user_id', $userId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('valid_clicks');
    }
}
