<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;

class LiveStatsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $clicksToday = Click::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('is_counted', true)
            ->count();

        $clicksLastHour = Click::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->where('is_counted', true)
            ->count();

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

        return response()->json([
            'clicks_today'     => $clicksToday,
            'clicks_last_hour' => $clicksLastHour,
            'badge'            => $badge,
        ]);
    }
}
