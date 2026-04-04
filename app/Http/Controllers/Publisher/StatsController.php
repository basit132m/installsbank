<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\CountryRate;
use App\Models\DailyEarning;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $profile = $user->publisherProfile;

        $period = $request->get('period', '7');
        $startDate = match($period) {
            '1' => today(),
            '7' => now()->subDays(6),
            '30' => now()->subDays(29),
            '90' => now()->subDays(89),
            default => now()->subDays(6),
        };

        $dailyStats = DailyEarning::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->toDateString(), today()->toDateString()])
            ->orderBy('date')
            ->get();

        // Fixed rate publishers are paid externally — hide per-click earnings in UI
        $showEarnings = $profile->payment_enabled && !$profile->isFixedRate();

        $totals = [
            'clicks' => $dailyStats->sum('valid_clicks'),
            'earnings' => $showEarnings ? $dailyStats->sum('earnings') : null,
        ];

        // Country breakdown aggregated
        $countryAgg = [];
        foreach ($dailyStats as $day) {
            if ($day->country_breakdown) {
                foreach ($day->country_breakdown as $code => $data) {
                    $countryAgg[$code] = $countryAgg[$code] ?? ['code' => $code, 'clicks' => 0, 'earnings' => 0];
                    $countryAgg[$code]['clicks'] += $data['clicks'] ?? 0;
                    if ($showEarnings) {
                        $countryAgg[$code]['earnings'] += $data['earnings'] ?? 0;
                    }
                }
            }
        }
        arsort($countryAgg);

        // Countries that have no rate set yet — to show N/A in stats
        $unratedCountries = CountryRate::where('needs_rate_update', true)->pluck('country_code')->toArray();

        return view('publisher.stats', compact('dailyStats', 'totals', 'countryAgg', 'period', 'profile', 'unratedCountries', 'showEarnings'));
    }
}
