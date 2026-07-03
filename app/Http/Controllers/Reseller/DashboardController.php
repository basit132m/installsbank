<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;
use App\Models\PublisherWebsite;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $divider = $user->clickDivider;

        $todayEarning = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();

        $stats = [
            'clicks_today'      => (int)($todayEarning?->valid_clicks ?? 0),
            'clicks_this_week'  => (int)DailyEarning::where('user_id', $user->id)
                ->whereBetween('date', [now()->startOfWeek(), now()])->sum('valid_clicks'),
            'clicks_this_month' => (int)DailyEarning::where('user_id', $user->id)
                ->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('valid_clicks'),
            'clicks_total'      => (int)DailyEarning::where('user_id', $user->id)->sum('valid_clicks'),
        ];

        // 7-day chart
        $clicksChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $de   = DailyEarning::where('user_id', $user->id)->where('date', $date->toDateString())->first();
            $clicksChart[] = [
                'date'   => $date->format('M d'),
                'clicks' => (int)($de?->valid_clicks ?? 0),
            ];
        }

        // Per-country clicks today — same divider math as publishers, clicks only
        $dividerVal    = ($divider && $divider->is_enabled)          ? max(1, (float)$divider->divider_value)     : 1;
        $macDividerVal = ($divider && $divider->mac_divider_enabled) ? max(1, (float)$divider->mac_divider_value) : 1;

        $clicksByCountry = Click::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('is_counted', true)
            ->selectRaw('country_code, country_name,
                SUM(CASE WHEN is_windows = 1 THEN 1 ELSE 0 END) as raw_windows,
                SUM(CASE WHEN is_mac = 1 THEN 1 ELSE 0 END) as raw_mac,
                SUM(CASE WHEN is_windows = 0 AND is_mac = 0 THEN 1 ELSE 0 END) as raw_other')
            ->groupBy('country_code', 'country_name')
            ->get()
            ->map(fn($r) => (object)[
                'country_code' => $r->country_code ?: 'XX',
                'country_name' => $r->country_name ?: 'Unknown',
                'valid_clicks' => (int)floor($r->raw_windows / $dividerVal)
                                + (int)floor($r->raw_mac / $macDividerVal)
                                + (int)$r->raw_other,
            ])
            ->filter(fn($r) => $r->valid_clicks >= 1)
            ->sortByDesc('valid_clicks')
            ->values();

        // OS breakdown today
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
        $osBreakdown = collect(array_filter($osMap, fn($c) => $c > 0));

        $websites = PublisherWebsite::where('user_id', $user->id)->latest()->get();

        $notifications = \App\Models\PublisherNotification::where('user_id', $user->id)
            ->whereNull('read_at')->latest()->get();

        return view('reseller.dashboard', compact(
            'user', 'stats', 'clicksChart', 'clicksByCountry', 'osBreakdown', 'websites', 'notifications'
        ));
    }
}
