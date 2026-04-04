<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;
use App\Models\TrackingLink;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $user    = auth()->user();
        $profile = $user->publisherProfile;
        $showEarnings = $profile?->payment_enabled && !$profile?->isFixedRate();

        $period = $request->get('period', '7');
        $startDate = match($period) {
            '1'  => today(),
            '7'  => now()->subDays(6),
            '30' => now()->subDays(29),
            '90' => now()->subDays(89),
            default => now()->subDays(6),
        };

        $daily = DailyEarning::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->toDateString(), today()->toDateString()])
            ->orderBy('date')->get();

        // Country aggregation
        $countryAgg = [];
        foreach ($daily as $day) {
            if ($day->country_breakdown) {
                foreach ($day->country_breakdown as $code => $data) {
                    $countryAgg[$code] = $countryAgg[$code] ?? ['country' => $code, 'clicks' => 0, 'earnings' => 0.0];
                    $countryAgg[$code]['clicks']   += $data['clicks'] ?? 0;
                    $countryAgg[$code]['earnings'] += $showEarnings ? ($data['earnings'] ?? 0) : 0;
                }
            }
        }
        usort($countryAgg, fn($a, $b) => $b['clicks'] - $a['clicks']);
        $countryAgg = array_values($countryAgg);

        // Per-link stats
        $linkStats = TrackingLink::where('user_id', $user->id)->get()->map(function ($link) use ($startDate, $showEarnings) {
            $base = Click::where('tracking_link_id', $link->id)->where('created_at', '>=', $startDate->startOfDay());
            return [
                'name'     => $link->name ?: 'Unnamed Link',
                'code'     => $link->unique_code,
                'clicks'   => (clone $base)->where('is_counted', true)->count(),
                'earnings' => $showEarnings ? (float) (clone $base)->where('is_counted', true)->sum('click_value') : null,
                'active'   => (bool) $link->is_active,
            ];
        })->sortByDesc('clicks')->values()->all();

        return response()->json([
            'period'       => $period,
            'show_earnings'=> $showEarnings,
            'totals' => [
                'clicks'   => $daily->sum('valid_clicks'),
                'earnings' => $showEarnings ? (float) $daily->sum('earnings') : null,
            ],
            'daily'        => $daily->map(fn($d) => [
                'date'     => $d->date->format('M d'),
                'clicks'   => $d->valid_clicks,
                'earnings' => $showEarnings ? (float) $d->earnings : 0,
            ])->values()->all(),
            'country_breakdown' => $countryAgg,
            'link_stats'        => $linkStats,
        ]);
    }
}
