<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\PortalStatsService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(PortalStatsService $stats)
    {
        $account = Auth::guard('portal')->user();
        $account->load('trackingLink');

        $data = $stats->dashboard($account);

        return view('portal.dashboard', compact('account', 'data'));
    }
}
