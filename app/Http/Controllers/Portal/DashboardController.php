<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\PortalStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, PortalStatsService $stats)
    {
        $account = Auth::guard('portal')->user();
        $account->load('trackingLink');

        $period = in_array($request->get('period'), ['today', 'yesterday', '7days'], true)
            ? $request->get('period')
            : '7days';

        $data       = $stats->dashboard($account);
        $periodData = $stats->periodAggregate($account, $period);
        $chart      = $stats->chartFor($account, $period);

        return view('portal.dashboard', compact('account', 'data', 'period', 'periodData', 'chart'));
    }

    /** Live JSON for the dashboard's auto-refresh. */
    public function live(Request $request, PortalStatsService $stats)
    {
        $account = Auth::guard('portal')->user();

        $period = in_array($request->get('period'), ['today', 'yesterday', '7days'], true)
            ? $request->get('period')
            : '7days';

        $data       = $stats->dashboard($account);
        $periodData = $stats->periodAggregate($account, $period);

        return response()->json([
            'today'        => $data['today'],
            'week'         => $data['week'],
            'month'        => $data['month'],
            'all_time'     => $data['all_time'],
            'period_total' => $periodData['total'],
        ]);
    }
}
