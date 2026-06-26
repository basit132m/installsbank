<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;

class TrafficTierController extends Controller
{
    private const TIER1 = ['US', 'GB', 'CA', 'AU', 'DE', 'FR', 'NL', 'SE', 'NO', 'DK'];
    private const TIER2 = ['ES', 'IT', 'PT', 'PL', 'CZ', 'HU', 'RO', 'GR', 'TR', 'AE'];

    public function index()
    {
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

        return view('admin.traffic-tiers.index', compact('tiers', 'totalClicks', 'totalEarnings'));
    }
}
