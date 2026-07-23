<?php

namespace App\Services;

use App\Models\Click;
use App\Models\PortalAccount;
use App\Models\PortalDailyStat;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Computes the CONTROLLED, display-only Windows-click numbers for a portal
 * account. Numbers are: real Windows clicks on the assigned tracking link,
 * divided by the portal's own divider, then clamped into [min, max]. Past
 * days are locked (stored) once finalized; today refreshes live.
 *
 * This is completely separate from the real Installs Bank panel — it never
 * writes to clicks/daily_earnings and its divider/min/max are the portal's own.
 */
class PortalStatsService
{
    /**
     * Resolve shown stats for every day in [$start, $end] as a map keyed by
     * Y-m-d => ['shown' => int, 'countries' => array].
     */
    public function resolveRange(PortalAccount $account, Carbon $start, Carbon $end): array
    {
        $start = $start->copy()->startOfDay();
        $end   = $end->copy()->startOfDay();

        $existing = PortalDailyStat::where('portal_account_id', $account->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($r) => $r->date->toDateString());

        $today = today();
        $map   = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key     = $d->toDateString();
            $isToday = $d->isSameDay($today);
            $row     = $existing->get($key);

            if ($row && $row->finalized && !$isToday) {
                $map[$key] = ['shown' => (int) $row->shown_clicks, 'countries' => $row->country_breakdown ?? []];
                continue;
            }

            $computed = $this->compute($account, $d);
            PortalDailyStat::updateOrCreate(
                ['portal_account_id' => $account->id, 'date' => $key],
                [
                    'shown_clicks'      => $computed['shown'],
                    'country_breakdown' => $computed['countries'],
                    'finalized'         => !$isToday, // lock everything but today
                ]
            );
            $map[$key] = $computed;
        }

        return $map;
    }

    /** Compute one day's controlled numbers from real Windows clicks. */
    private function compute(PortalAccount $account, Carbon $date): array
    {
        $divider = $account->effectiveDivider();

        $rows = collect();
        if ($account->tracking_link_id) {
            $rows = Click::where('tracking_link_id', $account->tracking_link_id)
                ->whereDate('created_at', $date->toDateString())
                ->where('is_counted', true)
                ->where('is_windows', true)
                ->selectRaw("COALESCE(NULLIF(country_code, ''), 'XX') as cc,
                             COALESCE(NULLIF(country_name, ''), 'Unknown') as cn,
                             COUNT(*) as c")
                ->groupBy('cc', 'cn')->get();
        }

        $realTotal = (int) $rows->sum('c');
        $shown     = (int) floor($realTotal / $divider);

        // Clamp into [min, max]
        if ($account->min_clicks !== null && $shown < $account->min_clicks) {
            $shown = (int) $account->min_clicks;
        }
        if ($account->max_clicks !== null && $shown > $account->max_clicks) {
            $shown = (int) $account->max_clicks;
        }

        // Per-country divided values, then scaled so they sum to the shown total
        $countries = $rows->map(fn($r) => [
            'code'  => strtolower($r->cc),
            'name'  => $r->cn,
            'value' => (int) floor((int) $r->c / $divider),
        ])->all();

        $countries = $this->scaleTo($countries, $shown);

        return ['shown' => $shown, 'countries' => $countries];
    }

    /** Largest-remainder scale of country rows so they sum exactly to $target. */
    private function scaleTo(array $rows, int $target): array
    {
        if ($target <= 0) return [];
        if (empty($rows)) return [['code' => '', 'name' => 'Other', 'value' => $target]];

        $cur = array_sum(array_column($rows, 'value'));

        if ($cur <= 0) {
            $base = intdiv($target, count($rows));
            $rem  = $target - $base * count($rows);
            foreach ($rows as $i => &$r) { $r['value'] = $base + ($i < $rem ? 1 : 0); }
            unset($r);
        } else {
            $floors = []; $rema = []; $sum = 0;
            foreach ($rows as $r) {
                $exact = $r['value'] * $target / $cur;
                $f = (int) floor($exact);
                $floors[] = $f; $rema[] = $exact - $f; $sum += $f;
            }
            $left  = $target - $sum;
            $order = collect($rema)->map(fn($v, $i) => [$v, $i])->sortByDesc(0)->values();
            for ($k = 0; $k < $left; $k++) {
                $floors[$order[$k % $order->count()][1]]++;
            }
            foreach ($rows as $i => &$r) { $r['value'] = $floors[$i]; }
            unset($r);
        }

        usort($rows, fn($a, $b) => $b['value'] <=> $a['value']);
        return array_values(array_filter($rows, fn($r) => $r['value'] > 0));
    }

    /** Totals + country breakdown for a selected period (today / yesterday / 7days). */
    public function periodAggregate(PortalAccount $account, string $period): array
    {
        $today = today();
        [$from, $to, $label] = match ($period) {
            'today'     => [$today->copy(),              $today->copy(),          'Today'],
            'yesterday' => [$today->copy()->subDay(),    $today->copy()->subDay(),'Yesterday'],
            default     => [$today->copy()->subDays(6),  $today->copy(),          'Last 7 Days'],
        };

        $map   = $this->resolveRange($account, $from, $to);
        $total = 0;
        $agg   = [];

        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key    = $d->toDateString();
            $total += $map[$key]['shown'] ?? 0;
            foreach (($map[$key]['countries'] ?? []) as $c) {
                $code = $c['code'] ?? '';
                if (!isset($agg[$code])) $agg[$code] = ['code' => $code, 'name' => $c['name'] ?? 'Other', 'value' => 0];
                $agg[$code]['value'] += (int) ($c['value'] ?? 0);
            }
        }

        return [
            'label'     => $label,
            'total'     => $total,
            'countries' => collect($agg)->sortByDesc('value')->values()->all(),
        ];
    }

    /**
     * Chart series for the selected period: daily points for "7days",
     * hourly points (scaled to the day's shown total) for today/yesterday.
     */
    public function chartFor(PortalAccount $account, string $period): array
    {
        $today = today();

        if ($period === '7days') {
            $from = $today->copy()->subDays(6);
            $map  = $this->resolveRange($account, $from, $today);
            $points = [];
            for ($d = $from->copy(); $d->lte($today); $d->addDay()) {
                $points[] = ['label' => $d->format('M d'), 'clicks' => $map[$d->toDateString()]['shown'] ?? 0];
            }
            return ['title' => 'Last 7 Days', 'points' => $points];
        }

        // today / yesterday → hourly, real hourly distribution scaled to the shown total
        $day   = $period === 'yesterday' ? $today->copy()->subDay() : $today->copy();
        $map   = $this->resolveRange($account, $day, $day);
        $shown = $map[$day->toDateString()]['shown'] ?? 0;

        $hourly = array_fill(0, 24, 0);
        if ($account->tracking_link_id) {
            $rows = Click::where('tracking_link_id', $account->tracking_link_id)
                ->whereDate('created_at', $day->toDateString())
                ->where('is_counted', true)
                ->where('is_windows', true)
                ->selectRaw('HOUR(created_at) as h, COUNT(*) as c')
                ->groupBy('h')->pluck('c', 'h');
            foreach ($rows as $h => $c) { $hourly[(int) $h] = (int) $c; }
        }

        // Only show up to the current hour for "today"; full day for "yesterday"
        $maxHour = $period === 'yesterday' ? 23 : (int) now()->format('G');
        $slice   = array_slice($hourly, 0, $maxHour + 1);
        $scaled  = $this->scaleValues($slice, $shown);

        $points = [];
        foreach ($scaled as $h => $v) {
            $points[] = ['label' => sprintf('%02d:00', $h), 'clicks' => $v];
        }

        return ['title' => $period === 'yesterday' ? 'Yesterday (hourly)' : 'Today (hourly)', 'points' => $points];
    }

    /** Largest-remainder scale of a flat int array, preserving order and length. */
    private function scaleValues(array $vals, int $target): array
    {
        $n = count($vals);
        if ($n === 0) return [];
        if ($target <= 0) return array_fill(0, $n, 0);

        $cur = array_sum($vals);
        if ($cur <= 0) {
            $base = intdiv($target, $n);
            $rem  = $target - $base * $n;
            $out  = [];
            for ($i = 0; $i < $n; $i++) $out[] = $base + ($i < $rem ? 1 : 0);
            return $out;
        }

        $floors = []; $rema = []; $sum = 0;
        foreach ($vals as $v) {
            $exact = $v * $target / $cur;
            $f = (int) floor($exact);
            $floors[] = $f; $rema[] = $exact - $f; $sum += $f;
        }
        $left = $target - $sum;
        $idx  = range(0, $n - 1);
        usort($idx, fn($a, $b) => $rema[$b] <=> $rema[$a]);
        for ($k = 0; $k < $left; $k++) $floors[$idx[$k % $n]]++;

        return $floors;
    }

    /** Build everything the dashboard needs. */
    public function dashboard(PortalAccount $account): array
    {
        $today = today();
        // Bound the all-time window so the loop stays cheap
        $start = Carbon::parse($account->created_at)->startOfDay();
        if ($start->lt($today->copy()->subDays(365))) {
            $start = $today->copy()->subDays(365);
        }

        $map = $this->resolveRange($account, $start, $today);
        $sumBetween = function (Carbon $from, Carbon $to) use ($map) {
            $t = 0;
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $t += $map[$d->toDateString()]['shown'] ?? 0;
            }
            return $t;
        };

        $chart = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i);
            $chart[] = ['date' => $d->format('M d'), 'clicks' => $map[$d->toDateString()]['shown'] ?? 0];
        }

        // Country aggregate over the last 30 days
        $countryAgg = [];
        for ($i = 29; $i >= 0; $i--) {
            $key = $today->copy()->subDays($i)->toDateString();
            foreach (($map[$key]['countries'] ?? []) as $c) {
                $code = $c['code'] ?? '';
                if (!isset($countryAgg[$code])) $countryAgg[$code] = ['code' => $code, 'name' => $c['name'] ?? 'Other', 'value' => 0];
                $countryAgg[$code]['value'] += (int) ($c['value'] ?? 0);
            }
        }
        $countries = collect($countryAgg)->sortByDesc('value')->values()->all();

        return [
            'today'      => $map[$today->toDateString()]['shown'] ?? 0,
            'week'       => $sumBetween($today->copy()->startOfWeek(), $today),
            'month'      => $sumBetween($today->copy()->startOfMonth(), $today),
            'all_time'   => array_sum(array_map(fn($m) => $m['shown'], $map)),
            'chart'      => $chart,
            'countries'  => $countries,
        ];
    }
}
