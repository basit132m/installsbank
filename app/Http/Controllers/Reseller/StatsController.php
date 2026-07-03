<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;
use App\Models\TrackingLink;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $period    = $request->get('period', '7');
        $startDate = match($period) {
            '1'     => today(),
            '7'     => now()->subDays(6),
            '30'    => now()->subDays(29),
            '90'    => now()->subDays(89),
            default => now()->subDays(6),
        };

        $divider         = $user->clickDivider;
        $dividerValue    = ($divider && $divider->is_enabled)          ? max(1, (float)$divider->divider_value)     : 1;
        $macDividerValue = ($divider && $divider->mac_divider_enabled) ? max(1, (float)$divider->mac_divider_value) : 1;

        $allLinks = TrackingLink::where('user_id', $user->id)->get();

        $selectedLinkId = (int)$request->get('link_id', 0);
        $selectedLink   = $selectedLinkId ? $allLinks->firstWhere('id', $selectedLinkId) : null;

        if ($selectedLink) {
            $dailyStats = $this->buildPerLinkDaily($selectedLink->id, $startDate, $dividerValue, $macDividerValue);
        } else {
            $dailyStats = DailyEarning::where('user_id', $user->id)
                ->whereBetween('date', [$startDate->toDateString(), today()->toDateString()])
                ->orderBy('date')
                ->get()
                ->map(fn($d) => (object)[
                    'date_label'   => $d->date->format('M d, Y'),
                    'date_raw'     => $d->date->toDateString(),
                    'valid_clicks' => (int)$d->valid_clicks,
                ]);
        }

        $totals = ['clicks' => $dailyStats->sum('valid_clicks')];

        $countryStats = $this->buildCountryStats($user->id, $startDate, $dividerValue, $macDividerValue, $selectedLink?->id);

        // OS breakdown with dividers applied
        $osQuery = Click::where('user_id', $user->id)
            ->where('is_counted', true)
            ->where('created_at', '>=', $startDate->copy()->startOfDay());
        if ($selectedLink) {
            $osQuery->where('tracking_link_id', $selectedLink->id);
        }
        $osRawRows = $osQuery
            ->selectRaw('os, is_windows, is_mac, COUNT(*) as raw_count')
            ->groupBy('os', 'is_windows', 'is_mac')
            ->get();

        $osMap = [];
        foreach ($osRawRows as $osRow) {
            $key = $osRow->os ?: 'Unknown';
            if ((bool)$osRow->is_windows) {
                $cnt = (int)floor((int)$osRow->raw_count / $dividerValue);
            } elseif ((bool)$osRow->is_mac) {
                $cnt = (int)floor((int)$osRow->raw_count / $macDividerValue);
            } else {
                $cnt = (int)$osRow->raw_count;
            }
            $osMap[$key] = ($osMap[$key] ?? 0) + $cnt;
        }
        arsort($osMap);
        $osBreakdown = collect(array_filter($osMap, fn($c) => $c > 0))
            ->mapWithKeys(fn($c, $os) => [$os => $c]);

        // Performance by link — valid clicks only
        $linkStats = $allLinks->map(function ($link) use ($startDate, $dividerValue, $macDividerValue) {
            $base    = Click::where('tracking_link_id', $link->id)
                ->where('created_at', '>=', $startDate->copy()->startOfDay());
            $windows = (clone $base)->where('is_counted', true)->where('is_windows', true)->count();
            $mac     = (clone $base)->where('is_counted', true)->where('is_mac', true)->count();
            $other   = (clone $base)->where('is_counted', true)->where('is_windows', false)->where('is_mac', false)->count();
            return [
                'id'     => $link->id,
                'name'   => $link->name ?: 'Unnamed Link',
                'code'   => $link->unique_code,
                'valid'  => $other + (int)floor($windows / $dividerValue) + (int)floor($mac / $macDividerValue),
                'active' => $link->is_active,
            ];
        })->sortByDesc('valid')->values();

        return view('reseller.stats', compact(
            'dailyStats', 'totals', 'countryStats', 'osBreakdown', 'period',
            'linkStats', 'selectedLink', 'allLinks'
        ));
    }

    private function buildCountryStats(int $userId, $startDate, float $dividerValue, float $macDividerValue, ?int $linkId = null): \Illuminate\Support\Collection
    {
        $q = Click::where('user_id', $userId)
            ->where('is_counted', true)
            ->where('created_at', '>=', $startDate->copy()->startOfDay())
            ->selectRaw('country_code, country_name,
                SUM(CASE WHEN is_windows = 1 THEN 1 ELSE 0 END) as raw_windows,
                SUM(CASE WHEN is_mac = 1 THEN 1 ELSE 0 END) as raw_mac,
                SUM(CASE WHEN is_windows = 0 AND is_mac = 0 THEN 1 ELSE 0 END) as raw_other')
            ->groupBy('country_code', 'country_name');

        if ($linkId) {
            $q->where('tracking_link_id', $linkId);
        }

        return $q->get()
            ->map(fn($r) => (object)[
                'country_code' => $r->country_code ?: 'XX',
                'country_name' => $r->country_name ?: 'Unknown',
                'valid_clicks' => (int)floor($r->raw_windows / $dividerValue)
                                + (int)floor($r->raw_mac / $macDividerValue)
                                + (int)$r->raw_other,
            ])
            ->filter(fn($r) => $r->valid_clicks >= 1)
            ->sortByDesc('valid_clicks')
            ->values();
    }

    private function buildPerLinkDaily(int $linkId, $startDate, float $dividerValue, float $macDividerValue): \Illuminate\Support\Collection
    {
        $days    = collect();
        $current = $startDate->copy()->startOfDay();
        $end     = today();

        while ($current->lte($end)) {
            $dateStr = $current->toDateString();
            $base    = Click::where('tracking_link_id', $linkId)
                ->whereDate('created_at', $dateStr)
                ->where('is_counted', true);
            $windows = (clone $base)->where('is_windows', true)->count();
            $mac     = (clone $base)->where('is_mac', true)->count();
            $other   = (clone $base)->where('is_windows', false)->where('is_mac', false)->count();

            $days->push((object)[
                'date_label'   => $current->format('M d, Y'),
                'date_raw'     => $dateStr,
                'valid_clicks' => $other + (int)floor($windows / $dividerValue) + (int)floor($mac / $macDividerValue),
            ]);

            $current->addDay();
        }

        return $days;
    }
}
