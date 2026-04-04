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
        $user = auth()->user();
        $profile = $user->publisherProfile;
        $showEarnings = $profile?->payment_enabled && !$profile?->isFixedRate();

        $todayEarning = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();

        $stats = [
            'clicks_today'      => $todayEarning?->valid_clicks ?? 0,
            'clicks_this_week'  => DailyEarning::where('user_id', $user->id)->whereBetween('date', [now()->startOfWeek(), now()])->sum('valid_clicks'),
            'clicks_this_month' => DailyEarning::where('user_id', $user->id)->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('valid_clicks'),
            'earnings_today'    => $showEarnings ? (float) ($todayEarning?->earnings ?? 0) : null,
            'balance'           => $showEarnings ? (float) ($profile?->balance ?? 0) : null,
            'pending_balance'   => $showEarnings ? (float) ($profile?->pending_balance ?? 0) : null,
        ];

        // Last 7 days chart
        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $de = DailyEarning::where('user_id', $user->id)->where('date', $date->toDateString())->first();
            $chart[] = [
                'date'     => $date->format('M d'),
                'clicks'   => $de?->valid_clicks ?? 0,
                'earnings' => $showEarnings ? (float) ($de?->earnings ?? 0) : 0,
            ];
        }

        // Country breakdown
        $countryBreakdown = [];
        if ($todayEarning?->country_breakdown) {
            $countryBreakdown = collect($todayEarning->country_breakdown)
                ->sortByDesc('clicks')->take(8)
                ->map(fn($d, $code) => ['country' => $code, 'clicks' => $d['clicks'] ?? 0, 'earnings' => $showEarnings ? ($d['earnings'] ?? 0) : null])
                ->values()->all();
        }

        $contract = $user->activeContract;
        $pendingContract = $user->contracts()->where('status', 'pending')->latest()->first();

        return response()->json([
            'stats'            => $stats,
            'chart'            => $chart,
            'country_breakdown'=> $countryBreakdown,
            'show_earnings'    => $showEarnings,
            'contract'         => $contract ? ['type' => $contract->type, 'rate' => $contract->rate] : null,
            'pending_contract' => $pendingContract ? ['id' => $pendingContract->id, 'type' => $pendingContract->type, 'rate' => $pendingContract->rate] : null,
            'has_test_running' => $profile?->test_status === 'running',
            'account_status'   => $user->status,
        ]);
    }

    public function liveStats()
    {
        $user = auth()->user();

        $clicksToday    = Click::where('user_id', $user->id)->whereDate('created_at', today())->where('is_counted', true)->count();
        $clicksLastHour = Click::where('user_id', $user->id)->where('created_at', '>=', now()->subHour())->where('is_counted', true)->count();

        $total30 = Click::where('user_id', $user->id)->where('created_at', '>=', now()->subDays(30))->count();
        $fraud30 = Click::where('user_id', $user->id)->where('created_at', '>=', now()->subDays(30))->where('is_fraud', true)->count();
        $fraudRate = $total30 > 0 ? round(($fraud30 / $total30) * 100, 1) : 0;

        $badge = match(true) {
            $total30 < 50    => ['label' => 'Building History',     'color' => '#6b7280'],
            $fraudRate < 5   => ['label' => 'Top Quality Traffic',  'color' => '#01BF63'],
            $fraudRate < 15  => ['label' => 'Good Traffic',         'color' => '#3b82f6'],
            $fraudRate < 30  => ['label' => 'Average Traffic',      'color' => '#f59e0b'],
            default          => ['label' => 'Review Needed',        'color' => '#ef4444'],
        };

        return response()->json([
            'clicks_today'     => $clicksToday,
            'clicks_last_hour' => $clicksLastHour,
            'badge'            => $badge,
        ]);
    }
}
