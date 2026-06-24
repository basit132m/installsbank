<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyEarning;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        };

        $rows = DailyEarning::query()
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->select(
                'user_id',
                DB::raw('SUM(valid_clicks) as total_valid'),
                DB::raw('SUM(windows_clicks_divided) as total_windows'),
                DB::raw('SUM(valid_clicks - windows_clicks_divided) as total_other'),
                DB::raw('SUM(earnings) as total_earnings'),
            )
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total_valid')
            ->get();

        $totals = [
            'valid'    => $rows->sum('total_valid'),
            'windows'  => $rows->sum('total_windows'),
            'other'    => $rows->sum('total_other'),
            'earnings' => $rows->sum('total_earnings'),
        ];

        return view('admin.publishers.overview', compact('rows', 'totals', 'period', 'label'));
    }
}
