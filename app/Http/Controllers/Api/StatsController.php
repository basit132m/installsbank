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
        $showEarnings = !($profile?->isFixedRate() ?? false);

        $divider      = $user->clickDivider;
        $dividerValue = ($divider && $divider->is_enabled) ? max(1, (float)$divider->divider_value) : 1;

        $period    = $request->get('period', '7');
        $startDate = match($period) {
            '1'  => today(),
            '7'  => now()->subDays(6),
            '30' => now()->subDays(29),
            '90' => now()->subDays(89),
            default => now()->subDays(6),
        };

        // Link filter
        $allLinks       = TrackingLink::where('user_id', $user->id)->get();
        $selectedLinkId = (int)$request->get('link_id', 0);
        $selectedLink   = $selectedLinkId ? $allLinks->firstWhere('id', $selectedLinkId) : null;

        // Daily stats — per-link uses Click table; aggregate uses DailyEarning (fast)
        if ($selectedLink) {
            $dailyData = $this->buildPerLinkDaily($selectedLink->id, $startDate, $dividerValue, $showEarnings);
            $dailyRows = collect(); // not used for country agg in per-link mode
        } else {
            $dailyRows = DailyEarning::where('user_id', $user->id)
                ->whereBetween('date', [$startDate->toDateString(), today()->toDateString()])
                ->orderBy('date')->get();
            $dailyData = $dailyRows->map(fn($d) => [
                'date'     => $d->date->format('M d'),
                'clicks'   => (int)$d->valid_clicks,
                'earnings' => $showEarnings ? (float)$d->earnings : 0,
            ])->values()->all();
        }

        // Country breakdown — aggregate from DailyEarning.country_breakdown (fast, no Click scan)
        // For per-link view, fall back to Click table (narrow query by link + date)
        if ($selectedLink) {
            $countryBreakdown = $this->buildCountryStatsForLink(
                $selectedLink->id, $startDate, $dividerValue, $showEarnings
            );
        } else {
            $countryBreakdown = $this->aggregateCountryFromDailyEarnings($dailyRows, $showEarnings);
        }

        // Per-link stats — ONE aggregated Click query instead of N×2 queries
        $linkAgg = Click::where('user_id', $user->id)
            ->where('is_counted', true)
            ->where('created_at', '>=', $startDate->copy()->startOfDay())
            ->selectRaw('tracking_link_id,
                SUM(CASE WHEN is_windows = 1 THEN 1 ELSE 0 END) as windows_clicks,
                SUM(CASE WHEN is_windows = 0 THEN 1 ELSE 0 END) as non_windows_clicks,
                SUM(click_value) as earnings')
            ->groupBy('tracking_link_id')
            ->get()->keyBy('tracking_link_id');

        $linkStats = $allLinks->map(function ($link) use ($linkAgg, $showEarnings, $dividerValue) {
            $agg        = $linkAgg->get($link->id);
            $windows    = (int)($agg?->windows_clicks ?? 0);
            $nonWindows = (int)($agg?->non_windows_clicks ?? 0);
            return [
                'id'       => $link->id,
                'name'     => $link->name ?: 'Unnamed Link',
                'code'     => $link->unique_code,
                'clicks'   => $nonWindows + (int)floor($windows / $dividerValue),
                'earnings' => $showEarnings ? (float)($agg?->earnings ?? 0) : null,
                'active'   => (bool)$link->is_active,
            ];
        })->sortByDesc('clicks')->values()->all();

        $totals = collect($dailyData);

        return response()->json([
            'period'            => $period,
            'show_earnings'     => $showEarnings,
            'selected_link_id'  => $selectedLink?->id,
            'totals' => [
                'clicks'   => (int)$totals->sum('clicks'),
                'earnings' => $showEarnings ? (float)$totals->sum('earnings') : null,
            ],
            'daily'             => $dailyData,
            'country_breakdown' => $countryBreakdown,
            'link_stats'        => $linkStats,
        ]);
    }

    /**
     * Aggregate country breakdown from DailyEarning.country_breakdown JSON field.
     * Very fast — no Click table scan needed.
     */
    private function aggregateCountryFromDailyEarnings($dailyRows, bool $showEarnings): array
    {
        $agg = [];
        foreach ($dailyRows as $de) {
            foreach (($de->country_breakdown ?? []) as $code => $data) {
                if (!isset($agg[$code])) {
                    $agg[$code] = ['country' => $code, 'clicks' => 0, 'earnings' => 0.0];
                }
                $agg[$code]['clicks']   += (int)($data['clicks'] ?? 0);
                $agg[$code]['earnings'] += (float)($data['earnings'] ?? 0);
            }
        }
        return collect($agg)
            ->sortByDesc('clicks')->take(15)
            ->map(fn($c) => [...$c, 'earnings' => $showEarnings ? $c['earnings'] : null])
            ->values()->all();
    }

    /**
     * Country breakdown from Click table for a specific link (narrow query).
     */
    private function buildCountryStatsForLink(int $linkId, $startDate, float $dividerValue, bool $showEarnings): array
    {
        return Click::where('tracking_link_id', $linkId)
            ->where('is_counted', true)
            ->where('is_windows', true)
            ->where('created_at', '>=', $startDate->copy()->startOfDay())
            ->selectRaw('country_code, COUNT(*) as raw_windows, SUM(click_value) as earnings')
            ->groupBy('country_code')
            ->orderByDesc('raw_windows')
            ->get()
            ->map(fn($r) => [
                'country'  => $r->country_code ?: 'XX',
                'clicks'   => (int)floor($r->raw_windows / $dividerValue),
                'earnings' => $showEarnings ? (float)$r->earnings : null,
            ])
            ->filter(fn($r) => $r['clicks'] >= 1)
            ->values()->all();
    }

    /**
     * Build daily stats from Click table for a specific tracking link.
     * Uses a single query + PHP grouping instead of N separate queries.
     */
    private function buildPerLinkDaily(int $linkId, $startDate, float $dividerValue, bool $showEarnings): array
    {
        // Single query for all days
        $rows = Click::where('tracking_link_id', $linkId)
            ->where('is_counted', true)
            ->where('created_at', '>=', $startDate->copy()->startOfDay())
            ->selectRaw('DATE(created_at) as day,
                SUM(CASE WHEN is_windows = 1 THEN 1 ELSE 0 END) as windows_clicks,
                SUM(CASE WHEN is_windows = 0 THEN 1 ELSE 0 END) as non_windows_clicks,
                SUM(click_value) as earnings')
            ->groupBy('day')
            ->orderBy('day')
            ->get()->keyBy('day');

        $days    = [];
        $current = $startDate->copy()->startOfDay();
        $end     = today();

        while ($current->lte($end)) {
            $dateStr = $current->toDateString();
            $row     = $rows->get($dateStr);
            $windows    = (int)($row?->windows_clicks ?? 0);
            $nonWindows = (int)($row?->non_windows_clicks ?? 0);

            $days[] = [
                'date'     => $current->format('M d'),
                'clicks'   => $nonWindows + (int)floor($windows / $dividerValue),
                'earnings' => $showEarnings ? (float)($row?->earnings ?? 0) : 0,
            ];
            $current->addDay();
        }

        return $days;
    }
}
