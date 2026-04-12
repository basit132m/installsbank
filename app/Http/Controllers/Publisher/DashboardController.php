<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Click;
use App\Models\DailyEarning;
use App\Models\PublisherInstall;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $profile = $user->publisherProfile;
        $contract = $user->activeContract;
        $divider  = $user->clickDivider;

        $todayEarning = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();

        $showEarnings = !$profile->isFixedRate();
        $canWithdraw  = $profile->payment_enabled && !$profile->isFixedRate();

        $stats = [
            'clicks_today'     => $todayEarning?->valid_clicks ?? 0,
            'earnings_today'   => $showEarnings ? ($todayEarning?->earnings ?? 0) : null,
            'balance'          => $showEarnings ? $profile->balance : null,
            'total_earnings'   => $showEarnings ? $profile->total_earnings : null,
            'clicks_this_week' => $this->getWeeklyClicks($user->id),
            'clicks_this_month'=> $this->getMonthlyClicks($user->id),
        ];

        // Chart data — last 7 days (divider-applied)
        $clicksChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $de   = DailyEarning::where('user_id', $user->id)->where('date', $date->toDateString())->first();
            $clicksChart[] = [
                'date'     => $date->format('M d'),
                'clicks'   => $de?->valid_clicks ?? 0,
                'earnings' => $showEarnings ? ($de?->earnings ?? 0) : 0,
            ];
        }

        // Country breakdown today (flag grid — all valid clicks from DailyEarning)
        $countryBreakdown = null;
        if ($todayEarning && $todayEarning->country_breakdown) {
            $countryBreakdown = collect($todayEarning->country_breakdown)
                ->sortByDesc('clicks')
                ->take(10);
        }

        // Per-country Windows breakdown today — for the Traffic by Country table
        // (per_click and installs_base only; Windows clicks with divider applied)
        $dividerVal = ($divider && $divider->is_enabled) ? max(1, (float)$divider->divider_value) : 1;
        $windowsByCountry = collect();
        if (in_array($profile->contract_type ?? 'none', ['per_click', 'installs_base'])) {
            $windowsByCountry = Click::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->where('is_counted', true)
                ->where('is_windows', true)
                ->selectRaw('country_code, country_name, COUNT(*) as raw_windows, SUM(click_value) as earnings')
                ->groupBy('country_code', 'country_name')
                ->orderByDesc('raw_windows')
                ->get()
                ->map(fn($r) => (object)[
                    'country_code' => $r->country_code ?: 'XX',
                    'country_name' => $r->country_name ?: 'Unknown',
                    'windows'      => (int)floor($r->raw_windows / $dividerVal),
                    'earnings'     => (float)$r->earnings,
                ])
                ->filter(fn($r) => $r->windows >= 1 || $r->earnings > 0)
                ->values();
        }

        // OS breakdown today
        $osBreakdown = Click::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('is_counted', true)
            ->selectRaw('os, COUNT(*) as clicks')
            ->groupBy('os')
            ->orderByDesc('clicks')
            ->get()
            ->mapWithKeys(fn($row) => [$row->os ?: 'Unknown' => ['clicks' => $row->clicks]]);

        $pendingContracts = $user->contracts()->where('status', 'pending')->latest()->get();
        $pendingContract  = $pendingContracts->first();
        $hasTestRunning   = $profile->test_status === 'running';
        $announcements    = Announcement::where('is_active', true)->latest()->get();

        $publisherNotifications = \App\Models\PublisherNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->get();

        $installsToday = null;
        if ($profile->contract_type === 'installs_base') {
            $installsToday = PublisherInstall::where('user_id', $user->id)
                ->where('date', today()->toDateString())
                ->orderByDesc('install_count')
                ->get();
        }

        return view('publisher.dashboard', compact(
            'user', 'profile', 'contract', 'divider',
            'stats', 'clicksChart', 'countryBreakdown', 'osBreakdown',
            'pendingContract', 'pendingContracts', 'hasTestRunning', 'showEarnings', 'canWithdraw',
            'announcements', 'installsToday', 'publisherNotifications', 'todayEarning',
            'windowsByCountry'
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
