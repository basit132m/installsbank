<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\FraudAlert;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_publishers' => User::where('role', 'publisher')->count(),
            'active_publishers' => User::where('role', 'publisher')->where('status', 'active')->count(),
            'pending_publishers' => User::where('role', 'publisher')->where('status', 'pending')->count(),
            'total_clicks_today' => Click::whereDate('created_at', today())->count(),
            'valid_clicks_today' => Click::whereDate('created_at', today())->where('is_counted', true)->count(),
            'fraud_clicks_today' => Click::whereDate('created_at', today())->where('is_fraud', true)->count(),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
            'pending_withdrawals_amount' => Withdrawal::where('status', 'pending')->sum('amount'),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'unresolved_fraud_alerts' => FraudAlert::where('is_resolved', false)->count(),
            'total_managers' => User::where('role', 'manager')->count(),
        ];

        // Click chart data for last 7 days
        $clicksChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $clicksChart[] = [
                'date' => $date->format('M d'),
                'total' => Click::whereDate('created_at', $date)->where('is_counted', true)->count(),
                'fraud' => Click::whereDate('created_at', $date)->where('is_fraud', true)->count(),
            ];
        }

        // OS breakdown today
        $osBreakdown = Click::whereDate('created_at', today())
            ->where('is_counted', true)
            ->selectRaw('os, COUNT(*) as count')
            ->groupBy('os')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Country breakdown today
        $countryBreakdown = Click::whereDate('created_at', today())
            ->where('is_counted', true)
            ->selectRaw('country_name, country_code, COUNT(*) as count')
            ->groupBy('country_name', 'country_code')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Recent fraud alerts
        $fraudAlerts = FraudAlert::with('publisher')
            ->where('is_resolved', false)
            ->latest()
            ->limit(5)
            ->get();

        // Recent registrations
        $recentPublishers = User::where('role', 'publisher')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'clicksChart', 'osBreakdown',
            'countryBreakdown', 'fraudAlerts', 'recentPublishers'
        ));
    }
}
