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

        $todayEarning = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();

        $stats = [
            'clicks_today'      => (int)($todayEarning?->valid_clicks ?? 0),
            'clicks_this_week'  => (int) DailyEarning::where('user_id', $user->id)->whereBetween('date', [now()->startOfWeek(), now()])->sum('valid_clicks'),
            'clicks_this_month' => (int) DailyEarning::where('user_id', $user->id)->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('valid_clicks'),
            'earnings_today'    => $showEarnings ? (float)($todayEarning?->earnings ?? 0) : null,
            'balance'           => $showEarnings ? (float)($profile?->balance ?? 0) : null,
            'pending_balance'   => $showEarnings ? (float)($profile?->pending_balance ?? 0) : null,
        ];

        // Last 7 days chart
        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $de   = DailyEarning::where('user_id', $user->id)->where('date', $date->toDateString())->first();
            $chart[] = [
                'date'     => $date->format('M d'),
                'clicks'   => (int)($de?->valid_clicks ?? 0),
                'earnings' => $showEarnings ? (float)($de?->earnings ?? 0) : 0,
            ];
        }

        // Country breakdown — Windows clicks with divider applied (today only)
        $countryBreakdown = $this->buildCountryStats(
            $user->id,
            today()->startOfDay(),
            $dividerValue
        )->take(8)->map(fn($c) => [
            'country'      => $c->country_code,
            'country_name' => $c->country_name,
            'clicks'       => $c->windows,
            'earnings'     => $showEarnings ? $c->earnings : null,
        ])->values()->all();

        $contract        = $user->activeContract;
        $pendingContract = $user->contracts()->where('status', 'pending')->latest()->first();

        return response()->json([
            'stats'             => $stats,
            'chart'             => $chart,
            'country_breakdown' => $countryBreakdown,
            'show_earnings'     => $showEarnings,
            'can_withdraw'      => $canWithdraw,
            'contract'          => $contract ? ['type' => $contract->type, 'rate' => $contract->rate] : null,
            'pending_contract'  => $pendingContract ? ['id' => $pendingContract->id, 'type' => $pendingContract->type, 'rate' => $pendingContract->rate] : null,
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

        // Today's clicks — use DailyEarning as single source of truth
        $todayEarning  = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();
        $clicksToday   = (int)($todayEarning?->valid_clicks ?? 0);
        $earningsToday = $showEarnings ? (float)($todayEarning?->earnings ?? 0) : null;
        $windowsToday  = (int)($todayEarning?->windows_clicks_divided ?? 0);

        // Last hour — apply divider to windows clicks
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

    /**
     * Per-country Windows clicks with divider applied.
     */
    private function buildCountryStats(int $userId, $startDate, float $dividerValue, ?int $linkId = null): \Illuminate\Support\Collection
    {
        $q = Click::where('user_id', $userId)
            ->where('is_counted', true)
            ->where('is_windows', true)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('country_code, country_name, COUNT(*) as raw_windows, SUM(click_value) as earnings')
            ->groupBy('country_code', 'country_name');

        if ($linkId) {
            $q->where('tracking_link_id', $linkId);
        }

        return $q->orderByDesc('raw_windows')->get()
            ->map(fn($r) => (object)[
                'country_code' => $r->country_code ?: 'XX',
                'country_name' => $r->country_name ?: 'Unknown',
                'windows'      => (int)floor($r->raw_windows / $dividerValue),
                'earnings'     => (float)$r->earnings,
            ])
            ->filter(fn($r) => $r->windows >= 1 || $r->earnings > 0)
            ->sortByDesc('windows')
            ->values();
    }
}
