<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyEarning;
use Illuminate\Http\Request;

class PublisherOverviewController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        [$dateFrom, $dateTo, $label] = match ($period) {
            'yesterday' => [today()->subDay(), today()->subDay(), 'Yesterday'],
            '7days'     => [today()->subDays(6), today(), 'Last 7 Days'],
            '28days'    => [today()->subDays(27), today(), 'Last 28 Days'],
            default     => [today(), today(), 'Today'],
        ];

        $earningRows = DailyEarning::query()
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->with('user')
            ->get();

        $rows = $earningRows->groupBy('user_id')->map(function ($records) {
            $user    = $records->first()->user;
            $windows = 0;
            $android = 0;
            $mac     = 0;
            $other   = 0;

            foreach ($records as $r) {
                $windows += (int) ($r->windows_clicks ?? 0);

                foreach ($r->os_breakdown ?? [] as $os => $data) {
                    $count   = is_array($data) ? (int)($data['clicks'] ?? 0) : (int)$data;
                    $osLower = strtolower($os);

                    if (str_contains($osLower, 'windows')) {
                        // already counted via windows_clicks (raw); skip
                        continue;
                    } elseif (str_contains($osLower, 'android')) {
                        $android += $count;
                    } elseif (
                        str_contains($osLower, 'mac') ||
                        str_contains($osLower, 'os x') ||
                        str_contains($osLower, 'ios') ||
                        str_contains($osLower, 'iphone') ||
                        str_contains($osLower, 'ipad')
                    ) {
                        $mac += $count;
                    } else {
                        $other += $count;
                    }
                }
            }

            $total = $windows + $android + $mac + $other;

            return compact('user', 'windows', 'android', 'mac', 'other', 'total');
        })
        ->filter(fn($r) => $r['total'] > 0)
        ->sortByDesc('total')
        ->values();

        $totals = [
            'windows' => $rows->sum('windows'),
            'android' => $rows->sum('android'),
            'mac'     => $rows->sum('mac'),
            'other'   => $rows->sum('other'),
            'total'   => $rows->sum('total'),
        ];

        return view('admin.publishers.overview', compact('rows', 'totals', 'period', 'label', 'dateFrom', 'dateTo'));
    }
}
