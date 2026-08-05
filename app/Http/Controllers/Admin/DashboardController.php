<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;
use App\Models\FraudAlert;
use App\Models\PublisherProfile;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $todayStart = today()->startOfDay();
        $tomorrow   = today()->addDay()->startOfDay();
        $chartStart = today()->subDays(6)->startOfDay();

        // ── Today's click counts: one indexed range scan instead of 3 whereDate scans
        $todayAgg = Click::where('created_at', '>=', $todayStart)->where('created_at', '<', $tomorrow)
            ->selectRaw('COUNT(*) as total, SUM(is_counted) as valid, SUM(is_fraud) as fraud')
            ->first();

        $stats = [
            'total_publishers'           => User::where('role', 'publisher')->count(),
            'active_publishers'          => User::where('role', 'publisher')->where('status', 'active')->count(),
            'pending_publishers'         => User::where('role', 'publisher')->where('status', 'pending')->count(),
            'total_clicks_today'         => (int) ($todayAgg->total ?? 0),
            'valid_clicks_today'         => (int) ($todayAgg->valid ?? 0),
            'fraud_clicks_today'         => (int) ($todayAgg->fraud ?? 0),
            'pending_withdrawals'        => Withdrawal::where('status', 'pending')->count(),
            'pending_withdrawals_amount' => Withdrawal::where('status', 'pending')->sum('amount'),
            'open_tickets'               => SupportTicket::where('status', 'open')->count(),
            'unresolved_fraud_alerts'    => FraudAlert::where('is_resolved', false)->count(),
            'total_managers'             => User::where('role', 'manager')->count(),
        ];

        // ── 7-day chart: one grouped range query instead of 14 whereDate scans
        $chartRows = Click::where('created_at', '>=', $chartStart)->where('created_at', '<', $tomorrow)
            ->selectRaw('DATE(created_at) as d, SUM(is_counted) as total, SUM(is_fraud) as fraud')
            ->groupBy('d')
            ->get()
            ->keyBy('d');

        $clicksChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $row  = $chartRows->get($date->toDateString());
            $clicksChart[] = [
                'date'  => $date->format('M d'),
                'total' => (int) ($row->total ?? 0),
                'fraud' => (int) ($row->fraud ?? 0),
            ];
        }

        // ── OS breakdown today (indexed range)
        $osBreakdown = Click::where('created_at', '>=', $todayStart)->where('created_at', '<', $tomorrow)
            ->where('is_counted', true)
            ->selectRaw('os, COUNT(*) as count')
            ->groupBy('os')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // ── Country breakdown today (indexed range)
        $countryBreakdown = Click::where('created_at', '>=', $todayStart)->where('created_at', '<', $tomorrow)
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

        // ── Revenue: money sums come from the small daily_earnings table (one row per
        // publisher per day) — identical totals to summing click_value, but no clicks scan.
        $revenue = [
            'paid_today'      => (float) DailyEarning::whereDate('date', today())->sum('earnings'),
            'paid_this_month' => (float) DailyEarning::whereYear('date', now()->year)->whereMonth('date', now()->month)->sum('earnings'),
            'paid_all_time'   => (float) PublisherProfile::sum('total_earnings'),
            'pending_payouts' => (float) PublisherProfile::sum('balance'),
            'withdrawn_total' => (float) Withdrawal::where('status', 'paid')->sum('amount'),
        ];

        return view('admin.dashboard', compact(
            'stats', 'clicksChart', 'osBreakdown',
            'countryBreakdown', 'fraudAlerts', 'recentPublishers', 'revenue'
        ));
    }
}
