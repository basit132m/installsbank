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

        return view('portal.dashboard', compact('account', 'data', 'period', 'periodData'));
    }
}
