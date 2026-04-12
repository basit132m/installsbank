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

        // Daily stats — per-link uses Click table; aggregate uses DailyEarning
        if ($selectedLink) {
            $dailyData = $this->buildPerLinkDaily($selectedLink->id, $startDate, $dividerValue, $showEarnings);
        } else {
            $dailyData = DailyEarning::where('user_id', $user->id)
                ->whereBetween('date', [$startDate->toDateString(), today()->toDateString()])
                ->orderBy('date')->get()
                ->map(fn($d) => [
                    'date'     => $d->date->format('M d'),
                    'clicks'   => (int)$d->valid_clicks,
                    'earnings' => $showEarnings ? (float)$d->earnings : 0,
                ])->values()->all();
        }

        // Country breakdown — Windows clicks with divider applied
        $countryBreakdown = $this->buildCountryStats(
            $user->id, $startDate, $dividerValue, $selectedLink?->id
        )->map(fn($c) => [
            'country'      => $c->country_code,
            'country_name' => $c->country_name,
            'clicks'       => $c->windows,
            'earnings'     => $showEarnings ? $c->earnings : null,
        ])->values()->all();

        // Per-link stats with divider applied to windows clicks
        $linkStats = $allLinks->map(function ($link) use ($startDate, $showEarnings, $dividerValue) {
            $base       = Click::where('tracking_link_id', $link->id)
                ->where('created_at', '>=', $startDate->copy()->startOfDay());
            $windows    = (clone $base)->where('is_counted', true)->where('is_windows', true)->count();
            $nonWindows = (clone $base)->where('is_counted', true)->where('is_windows', false)->count();
            return [
                'id'       => $link->id,
                'name'     => $link->name ?: 'Unnamed Link',
                'code'     => $link->unique_code,
                'clicks'   => $nonWindows + (int)floor($windows / $dividerValue),
                'earnings' => $showEarnings ? (float)(clone $base)->where('is_counted', true)->sum('click_value') : null,
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
     * Per-country Windows clicks with divider applied.
     */
    private function buildCountryStats(int $userId, $startDate, float $dividerValue, ?int $linkId = null): \Illuminate\Support\Collection
    {
        $q = Click::where('user_id', $userId)
            ->where('is_counted', true)
            ->where('is_windows', true)
            ->where('created_at', '>=', $startDate->copy()->startOfDay())
            ->selectRaw('country_code, country_name, COUNT(*) as raw_windows, SUM(click_value) as earnings')
            ->groupBy('country_code', 'country_name');

        if ($linkId) {
            $q->where('tracking_link_id', $linkId);
        }

        return $q->orderByDesc('raw_windows')->get()
            ->map(fn($r) => (object)[
                'country_code' => $r->country_code ?: 'XX',
                'country_name' => $r->country_name ?: 'Unknown',
                'windows'      => (int)floor($r->raw_windows / $dividerValue),
                'earnings'     => (float)$r->earnings,
            ])
            ->filter(fn($r) => $r->windows >= 1 || $r->earnings > 0)
            ->sortByDesc('windows')
            ->values();
    }

    /**
     * Build daily stats from Click table for a specific tracking link.
     */
    private function buildPerLinkDaily(int $linkId, $startDate, float $dividerValue, bool $showEarnings): array
    {
        $days    = [];
        $current = $startDate->copy()->startOfDay();
        $end     = today();

        while ($current->lte($end)) {
            $dateStr    = $current->toDateString();
            $base       = Click::where('tracking_link_id', $linkId)
                ->whereDate('created_at', $dateStr)
                ->where('is_counted', true);
            $windows    = (clone $base)->where('is_windows', true)->count();
            $nonWindows = (clone $base)->where('is_windows', false)->count();
            $earnings   = (clone $base)->sum('click_value');

            $days[] = [
                'date'     => $current->format('M d'),
                'clicks'   => $nonWindows + (int)floor($windows / $dividerValue),
                'earnings' => $showEarnings ? (float)$earnings : 0,
            ];

            $current->addDay();
        }

        return $days;
    }
}
