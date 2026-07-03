<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;

class LiveStatsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $todayEarning = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();
        $clicksToday  = (int)($todayEarning?->valid_clicks ?? 0);
        $windowsToday = (int)($todayEarning?->windows_clicks_divided ?? 0);
        $macToday     = (int)($todayEarning?->mac_clicks_divided ?? 0);

        $divider         = $user->clickDivider;
        $dividerValue    = ($divider && $divider->is_enabled)          ? max(1, (float)$divider->divider_value)     : 1;
        $macDividerValue = ($divider && $divider->mac_divider_enabled) ? max(1, (float)$divider->mac_divider_value) : 1;

        $lastHourWindows = Click::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->where('is_counted', true)->where('is_windows', true)->count();
        $lastHourMac = Click::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->where('is_counted', true)->where('is_mac', true)->count();
        $lastHourOther = Click::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->where('is_counted', true)
            ->where('is_windows', false)->where('is_mac', false)->count();

        $clicksLastHour = $lastHourOther
            + (int)floor($lastHourWindows / $dividerValue)
            + (int)floor($lastHourMac / $macDividerValue);

        return response()->json([
            'clicks_today'     => $clicksToday,
            'clicks_last_hour' => $clicksLastHour,
            'windows_today'    => $windowsToday,
            'mac_today'        => $macToday,
        ]);
    }
}
