<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use Illuminate\Support\Facades\DB;

class TrafficTierController extends Controller
{
    private const TIER1 = ['US', 'GB', 'CA', 'AU', 'DE', 'FR', 'NL', 'SE', 'NO', 'DK'];
    private const TIER2 = ['ES', 'IT', 'PT', 'PL', 'CZ', 'HU', 'RO', 'GR', 'TR', 'AE'];

    public function index()
    {
        // Single query — conditional aggregation for all 4 periods at once
        $periodRows = Click::query()
            ->where('is_counted', true)
            ->where('is_windows', true)
            ->groupBy('country_code')
            ->selectRaw("
                country_code,
                COUNT(*) as all_time,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 ELSE 0 END) as yesterday,
                SUM(CASE WHEN created_at >= DATE_SUB(CURDATE(), INTERVAL 27 DAY) THEN 1 ELSE 0 END) as last28
            ")
            ->get()
            ->keyBy('country_code');

        // Build period summary [tier][period] = total clicks
        $periods = ['today', 'yesterday', 'last28', 'all_time'];
        $periodTotals = [];
        foreach ([1, 2, 3] as $t) {
            foreach ($periods as $p) {
                $periodTotals[$t][$p] = 0;
            }
        }
        foreach ($periodRows as $code => $row) {
            $tier = in_array($code, self::TIER1) ? 1 : (in_array($code, self::TIER2) ? 2 : 3);
            foreach ($periods as $p) {
                $periodTotals[$tier][$p] += (int) $row->$p;
            }
        }

        // Full per-country breakdown for the tables (all time)
        $rows = Click::query()
            ->where('is_counted', true)
            ->where('is_windows', true)
            ->groupBy('country_code', 'country_name')
            ->selectRaw('country_code, country_name, COUNT(*) as clicks, SUM(click_value) as earnings')
            ->orderByDesc('clicks')
            ->get();

        $tiers = [
            1 => ['countries' => [], 'clicks' => 0, 'earnings' => 0.0],
            2 => ['countries' => [], 'clicks' => 0, 'earnings' => 0.0],
            3 => ['countries' => [], 'clicks' => 0, 'earnings' => 0.0],
        ];

        foreach ($rows as $row) {
            $tier = in_array($row->country_code, self::TIER1) ? 1
                  : (in_array($row->country_code, self::TIER2) ? 2 : 3);

            $tiers[$tier]['countries'][] = $row;
            $tiers[$tier]['clicks']      += $row->clicks;
            $tiers[$tier]['earnings']    += (float) $row->earnings;
        }

        $totalClicks   = $rows->sum('clicks');
        $totalEarnings = $rows->sum('earnings');

        return view('admin.traffic-tiers.index', compact('tiers', 'totalClicks', 'totalEarnings', 'periodTotals'));
    }
}
