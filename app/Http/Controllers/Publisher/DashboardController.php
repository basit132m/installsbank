<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Click;
use App\Models\DailyEarning;
use App\Models\MacPublisherInstall;
use App\Models\PublisherInstall;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $profile  = $user->publisherProfile;
        $contract = $user->activeContract;
        $divider  = $user->clickDivider;

        $todayEarning = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();

        $showEarnings = !$profile->isFixedRate();
        $canWithdraw  = $profile->payment_enabled && !$profile->isFixedRate();

        $stats = [
            'clicks_today'      => $todayEarning?->valid_clicks ?? 0,
            'earnings_today'    => $showEarnings ? ($todayEarning?->earnings ?? 0) : null,
            'balance'           => $showEarnings ? $profile->balance : null,
            'total_earnings'    => $showEarnings ? $profile->total_earnings : null,
            'clicks_this_week'  => $this->getWeeklyClicks($user->id),
            'clicks_this_month' => $this->getMonthlyClicks($user->id),
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

        // Country breakdown today (from DailyEarning snapshot)
        $countryBreakdown = null;
        if ($todayEarning && $todayEarning->country_breakdown) {
            $countryBreakdown = collect($todayEarning->country_breakdown)
                ->sortByDesc('clicks')
                ->take(10);
        }

        // Per-country click breakdown — Windows + Mac + other, each with its own divider applied
        $dividerVal    = ($divider && $divider->is_enabled)        ? max(1, (float)$divider->divider_value)     : 1;
        $macDividerVal = ($divider && $divider->mac_divider_enabled) ? max(1, (float)$divider->mac_divider_value) : 1;

        $clicksByCountry = Click::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('is_counted', true)
            ->selectRaw('country_code, country_name,
                SUM(CASE WHEN is_windows = 1 THEN 1 ELSE 0 END) as raw_windows,
                SUM(CASE WHEN is_mac = 1 THEN 1 ELSE 0 END) as raw_mac,
                SUM(CASE WHEN is_windows = 0 AND is_mac = 0 THEN 1 ELSE 0 END) as raw_other,
                SUM(click_value) as earnings')
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('raw_windows')
            ->get()
            ->map(fn($r) => (object)[
                'country_code' => $r->country_code ?: 'XX',
                'country_name' => $r->country_name ?: 'Unknown',
                'valid_clicks' => (int)floor($r->raw_windows / $dividerVal)
                                + (int)floor($r->raw_mac / $macDividerVal)
                                + (int)$r->raw_other,
                'earnings'     => (float)$r->earnings,
            ])
            ->filter(fn($r) => $r->valid_clicks >= 1 || $r->earnings > 0)
            ->sortByDesc('valid_clicks')
            ->values();

        // OS breakdown today — apply respective divider per OS type
        $osRawData = Click::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('is_counted', true)
            ->selectRaw('os, is_windows, is_mac, COUNT(*) as raw_count')
            ->groupBy('os', 'is_windows', 'is_mac')
            ->get();
        $osMap = [];
        foreach ($osRawData as $osRow) {
            $osKey = $osRow->os ?: 'Unknown';
            if ((bool)$osRow->is_windows) {
                $cnt = (int)floor($osRow->raw_count / $dividerVal);
            } elseif ((bool)$osRow->is_mac) {
                $cnt = (int)floor($osRow->raw_count / $macDividerVal);
            } else {
                $cnt = (int)$osRow->raw_count;
            }
            $osMap[$osKey] = ($osMap[$osKey] ?? 0) + $cnt;
        }
        arsort($osMap);
        $osBreakdown = collect(array_filter($osMap, fn($c) => $c > 0))
            ->map(fn($c) => ['clicks' => $c]);

        $pendingContracts = $user->contracts()->where('status', 'pending')->latest()->get();
        $pendingContract  = $pendingContracts->first();
        $hasTestRunning   = $profile->test_status === 'running';
        $announcements    = Announcement::where('is_active', true)->latest()->get();

        $publisherNotifications = \App\Models\PublisherNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->get();

        $installsToday    = null;
        $macInstallsToday = null;
        if ($profile->contract_type === 'installs_base') {
            $installsToday = PublisherInstall::where('user_id', $user->id)
                ->where('date', today()->toDateString())
                ->orderByDesc('install_count')
                ->get();
            $macInstallsToday = MacPublisherInstall::where('user_id', $user->id)
                ->where('date', today()->toDateString())
                ->orderByDesc('install_count')
                ->get();
        }

        return view('publisher.dashboard', compact(
            'user', 'profile', 'contract', 'divider',
            'stats', 'clicksChart', 'countryBreakdown', 'osBreakdown',
            'pendingContract', 'pendingContracts', 'hasTestRunning', 'showEarnings', 'canWithdraw',
            'announcements', 'installsToday', 'macInstallsToday', 'publisherNotifications',
            'todayEarning', 'clicksByCountry'
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
