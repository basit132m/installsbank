<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $profile = $user->publisherProfile;
        $showEarnings = !($profile?->isFixedRate() ?? false);
        $canWithdraw  = ($profile?->payment_enabled ?? false) && $showEarnings;

        $divider      = $user->clickDivider;
        $dividerValue = ($divider && $divider->is_enabled) ? max(1, (float)$divider->divider_value) : 1;

        // ONE query for all last-7-days data (replaces 7 individual queries)
        $last7 = DailyEarning::where('user_id', $user->id)
            ->whereBetween('date', [now()->subDays(6)->toDateString(), today()->toDateString()])
            ->orderBy('date')->get()->keyBy(fn($d) => $d->date->toDateString());

        $todayEarning = $last7->get(today()->toDateString());

        // Weekly / monthly use separate sums (still lightweight)
        $weekClicks  = (int) DailyEarning::where('user_id', $user->id)
            ->whereBetween('date', [now()->startOfWeek()->toDateString(), today()->toDateString()])
            ->sum('valid_clicks');
        $monthClicks = (int) DailyEarning::where('user_id', $user->id)
            ->whereMonth('date', now()->month)->whereYear('date', now()->year)
            ->sum('valid_clicks');

        $stats = [
            'clicks_today'      => (int)($todayEarning?->valid_clicks ?? 0),
            'clicks_this_week'  => $weekClicks,
            'clicks_this_month' => $monthClicks,
            'earnings_today'    => $showEarnings ? (float)($todayEarning?->earnings ?? 0) : null,
            'balance'           => $showEarnings ? (float)($profile?->balance ?? 0) : null,
            'pending_balance'   => $showEarnings ? (float)($profile?->pending_balance ?? 0) : null,
        ];

        // Chart — built from pre-loaded collection, zero extra queries
        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $d  = Carbon::today()->subDays($i);
            $de = $last7->get($d->toDateString());
            $chart[] = [
                'date'     => $d->format('M d'),
                'clicks'   => (int)($de?->valid_clicks ?? 0),
                'earnings' => $showEarnings ? (float)($de?->earnings ?? 0) : 0,
            ];
        }

        // Country breakdown — use DailyEarning.country_breakdown (fast, no Click scan)
        $countryBreakdown = [];
        if ($todayEarning?->country_breakdown) {
            $countryBreakdown = collect($todayEarning->country_breakdown)
                ->map(fn($d, $code) => [
                    'country'  => $code,
                    'clicks'   => (int)($d['clicks'] ?? 0),
                    'earnings' => $showEarnings ? (float)($d['earnings'] ?? 0) : null,
                ])
                ->sortByDesc('clicks')->take(8)->values()->all();
        }

        // Contract — check formal Contract record first, then fall back to profile contract_type
        $contract        = $user->activeContract;
        $pendingContract = $user->contracts()->where('status', 'pending')->latest()->first();

        $contractInfo = null;
        if ($contract) {
            $contractInfo = ['type' => $contract->type, 'rate' => $contract->rate];
        } elseif ($profile?->contract_type) {
            $contractInfo = [
                'type' => $profile->contract_type,
                'rate' => $profile->contract_type === 'fixed' ? (float)($profile->fixed_daily_rate ?? 0) : null,
            ];
        }

        return response()->json([
            'stats'             => $stats,
            'chart'             => $chart,
            'country_breakdown' => $countryBreakdown,
            'show_earnings'     => $showEarnings,
            'can_withdraw'      => $canWithdraw,
            'contract'          => $contractInfo,
            'pending_contract'  => $pendingContract
                ? ['id' => $pendingContract->id, 'type' => $pendingContract->type, 'rate' => $pendingContract->rate]
                : null,
            'has_test_running'  => $profile?->test_status === 'running',
            'account_status'    => $user->status,
        ]);
    }

    public function liveStats()
    {
        $user    = auth()->user();
        $profile = $user->publisherProfile;

        $showEarnings = !($profile?->isFixedRate() ?? false);

        $divider      = $user->clickDivider;
        $dividerValue = ($divider && $divider->is_enabled) ? max(1, (float)$divider->divider_value) : 1;

        // Today from DailyEarning (single row lookup, very fast)
        $todayEarning  = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();
        $clicksToday   = (int)($todayEarning?->valid_clicks ?? 0);
        $earningsToday = $showEarnings ? (float)($todayEarning?->earnings ?? 0) : null;
        $windowsToday  = (int)($todayEarning?->windows_clicks_divided ?? 0);

        // Last hour — two small indexed queries
        $lastHourBase       = Click::where('user_id', $user->id)->where('created_at', '>=', now()->subHour())->where('is_counted', true);
        $lastHourWindows    = (clone $lastHourBase)->where('is_windows', true)->count();
        $lastHourNonWindows = (clone $lastHourBase)->where('is_windows', false)->count();
        $clicksLastHour     = $lastHourNonWindows + (int)floor($lastHourWindows / $dividerValue);

        return response()->json([
            'clicks_today'     => $clicksToday,
            'clicks_last_hour' => $clicksLastHour,
            'windows_today'    => $windowsToday,
            'earnings_today'   => $earningsToday,
            'show_earnings'    => $showEarnings,
        ]);
    }
}
