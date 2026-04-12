<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;

class LiveStatsController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $profile = $user->publisherProfile;

        // Today's valid clicks — use DailyEarning so divider is already applied
        $todayEarning    = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();
        $clicksToday     = (int)($todayEarning?->valid_clicks ?? 0);
        $windowsToday    = (int)($todayEarning?->windows_clicks_divided ?? 0);
        $earningsToday   = (float)($todayEarning?->earnings ?? 0);

        // Last-hour clicks — apply divider to windows portion only
        $divider      = $user->clickDivider;
        $dividerValue = ($divider && $divider->is_enabled) ? max(1, (float)$divider->divider_value) : 1;

        $lastHourWindows    = Click::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->where('is_counted', true)
            ->where('is_windows', true)
            ->count();
        $lastHourNonWindows = Click::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->where('is_counted', true)
            ->where('is_windows', false)
            ->count();
        $clicksLastHour = $lastHourNonWindows + (int)floor($lastHourWindows / $dividerValue);

        // Performance badge based on 30-day fraud rate
        $total30 = Click::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $fraud30 = Click::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->where('is_fraud', true)
            ->count();

        $fraudRate = $total30 > 0 ? round(($fraud30 / $total30) * 100, 1) : 0;

        $badge = match(true) {
            $total30 < 50              => ['label' => 'Building History', 'color' => '#6b7280'],
            $fraudRate < 5             => ['label' => 'Top Quality Traffic', 'color' => '#01BF63'],
            $fraudRate < 15            => ['label' => 'Good Traffic',        'color' => '#3b82f6'],
            $fraudRate < 30            => ['label' => 'Average Traffic',     'color' => '#f59e0b'],
            default                    => ['label' => 'Review Needed',       'color' => '#ef4444'],
        };

        // Only expose earnings/windows for non-fixed-rate publishers
        $showEarnings = $profile?->payment_enabled && !($profile?->isFixedRate() ?? false);

        return response()->json([
            'clicks_today'     => $clicksToday,
            'clicks_last_hour' => $clicksLastHour,
            'windows_today'    => $windowsToday,
            'earnings_today'   => $showEarnings ? round($earningsToday, 4) : null,
            'badge'            => $badge,
        ]);
    }
}
