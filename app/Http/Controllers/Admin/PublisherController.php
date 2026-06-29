<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClickDivider;
use App\Models\Click;
use App\Models\Contract;
use App\Models\DailyEarning;
use App\Models\FraudAlert;
use App\Models\MacInstallCountryRate;
use App\Models\MacPublisherInstall;
use App\Models\PublisherInstall;
use App\Models\PublisherProfile;
use App\Models\PublisherTag;
use App\Models\TrackingLink;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PublisherController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'publisher')->with('publisherProfile', 'trackingLinks');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $publishers = $query->latest()->paginate(20);
        return view('admin.publishers.index', compact('publishers'));
    }

    public function show(User $user)
    {
        // Load core relationships — tables that have existed since early migrations
        $user->load(['publisherProfile', 'trackingLinks', 'contracts', 'clickDivider']);

        // Load publisher tags defensively — table created in a later migration
        try {
            $user->load('publisherTags');
        } catch (\Throwable $e) {
            $user->setRelation('publisherTags', collect());
            \Illuminate\Support\Facades\Log::warning('publisherTags load failed (migration pending?): ' . $e->getMessage());
        }

        // Load publisher websites defensively — table created in a later migration
        try {
            $publisherWebsites = \App\Models\PublisherWebsite::where('user_id', $user->id)
                ->with('trackingLink')->latest()->get();
        } catch (\Throwable $e) {
            $publisherWebsites = collect();
            \Illuminate\Support\Facades\Log::warning('publisherWebsites load failed (migration pending?): ' . $e->getMessage());
        }

        $dailyEarningToday = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();

        $clickStats = [
            'today'                => Click::where('user_id', $user->id)->whereDate('created_at', today())->where('is_counted', true)->count(),
            'today_windows'        => (int)($dailyEarningToday?->windows_clicks_divided ?? 0),
            'today_mac'            => (int)($dailyEarningToday?->mac_clicks_divided ?? 0),
            'today_fraud'          => Click::where('user_id', $user->id)->whereDate('created_at', today())->where('is_fraud', true)->count(),
            'this_week'            => Click::where('user_id', $user->id)->whereBetween('created_at', [now()->startOfWeek(), now()])->where('is_counted', true)->count(),
            'this_month'           => Click::where('user_id', $user->id)->whereMonth('created_at', now()->month)->where('is_counted', true)->count(),
            'total'                => Click::where('user_id', $user->id)->where('is_counted', true)->count(),
            'total_actual_windows' => (int)($dailyEarningToday?->windows_clicks ?? 0),
            'total_actual_mac'     => (int)($dailyEarningToday?->mac_clicks ?? 0),
        ];

        // Daily clicks chart (last 14 days) — use DailyEarning for divider-accurate valid_clicks
        $dailyEarningsMap = DailyEarning::where('user_id', $user->id)
            ->whereBetween('date', [now()->subDays(13)->toDateString(), now()->toDateString()])
            ->get()
            ->keyBy(fn($r) => $r->date->toDateString());

        // Single query for all raw click counts per day — avoids N*3 per-day queries
        $rawDailyMap = Click::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw("DATE(created_at) as day,
                SUM(CASE WHEN is_counted = 1 AND is_windows = 1 THEN 1 ELSE 0 END) as windows,
                SUM(CASE WHEN is_counted = 1 AND is_mac = 1 THEN 1 ELSE 0 END) as mac,
                SUM(CASE WHEN is_fraud = 1 THEN 1 ELSE 0 END) as fraud")
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $clicksChart = [];
        for ($i = 13; $i >= 0; $i--) {
            $date    = now()->subDays($i)->toDateString();
            $earning = $dailyEarningsMap[$date] ?? null;
            $raw     = $rawDailyMap[$date] ?? null;
            $clicksChart[] = [
                'date'    => now()->subDays($i)->format('M d'),
                'actual'  => (int)($earning?->valid_clicks ?? 0),
                'windows' => (int)($raw?->windows ?? 0),
                'mac'     => (int)($raw?->mac ?? 0),
                'fraud'   => (int)($raw?->fraud ?? 0),
            ];
        }

        $divider = $user->clickDivider ?: ClickDivider::firstOrCreate(['user_id' => $user->id], ['divider_value' => 1, 'is_enabled' => false]);

        // Installs comparison — only for installs_base publishers
        $installStats = null;
        $profile = $user->publisherProfile;
        if ($profile?->contract_type === 'installs_base') {
            try {
                $weekday      = (int) now()->format('w');
                $ratio        = \App\Models\InstallDayRatio::where('weekday', $weekday)->value('ratio') ?? 30;
                $dividerValue = $divider->is_enabled ? max(1, (float)$divider->divider_value) : 1;

                // What the publisher sees (from PublisherInstall — already divider-adjusted)
                $publisherInstalls = PublisherInstall::where('user_id', $user->id)
                    ->where('date', today()->toDateString())
                    ->get();

                // Actual: raw Windows clicks / base ratio per country (no divider applied)
                $rawByCountry = Click::where('user_id', $user->id)
                    ->whereDate('created_at', today())
                    ->where('is_counted', true)
                    ->where('is_windows', true)
                    ->selectRaw('LOWER(country_code) as country_code, COUNT(*) as raw_windows')
                    ->groupBy('country_code')
                    ->get();

                // Rates keyed by lowercase country code
                $rawCodes     = $rawByCountry->pluck('country_code')->filter()->values()->all();
                $installRates = \App\Models\InstallCountryRate::whereIn('country_code', $rawCodes)
                    ->where('is_active', true)
                    ->get()
                    ->mapWithKeys(fn($r) => [strtolower((string)$r->country_code) => (float)$r->rate_usd]);

                $safeRatio = max(1, (int)$ratio);
                $actualByCountry = $rawByCountry->map(fn($r) => [
                    'country_code' => $r->country_code,
                    'installs'     => (int)floor((int)$r->raw_windows / $safeRatio),
                    'earnings'     => (float)floor((int)$r->raw_windows / $safeRatio) * ($installRates[$r->country_code] ?? 0),
                ])->filter(fn($r) => $r['installs'] > 0)->values();

                $installStats = [
                    'publisher_installs'   => (int)$publisherInstalls->sum('install_count'),
                    'publisher_earnings'   => (float)$publisherInstalls->sum('earnings'),
                    'actual_installs'      => (int)$actualByCountry->sum('installs'),
                    'actual_earnings'      => (float)$actualByCountry->sum('earnings'),
                    'divider_value'        => $dividerValue,
                    'ratio'                => (int)$ratio,
                    'publisher_by_country' => $publisherInstalls,
                    'actual_by_country'    => $actualByCountry,
                ];
            } catch (\Throwable $e) {
                // Non-fatal: leave $installStats = null so the page still loads
                \Illuminate\Support\Facades\Log::warning('installStats failed for user '.$user->id.': '.$e->getMessage());
            }
        }

        // Mac Install stats — only for installs_base publishers
        $macInstallStats = null;
        if ($profile?->contract_type === 'installs_base') {
            try {
                $weekday         = (int) now()->format('w');
                $ratio           = \App\Models\InstallDayRatio::where('weekday', $weekday)->value('ratio') ?? 30;
                $macDividerValue = ($divider->mac_divider_enabled ?? false) ? max(1, (float)($divider->mac_divider_value ?? 1)) : 1;

                $macPublisherInstalls = MacPublisherInstall::where('user_id', $user->id)
                    ->where('date', today()->toDateString())
                    ->get();

                $rawMacByCountry = Click::where('user_id', $user->id)
                    ->whereDate('created_at', today())
                    ->where('is_counted', true)
                    ->where('is_mac', true)
                    ->selectRaw('LOWER(country_code) as country_code, COUNT(*) as raw_mac')
                    ->groupBy('country_code')
                    ->get();

                $macRawCodes  = $rawMacByCountry->pluck('country_code')->filter()->values()->all();
                $macInstallRates = MacInstallCountryRate::whereIn('country_code', $macRawCodes)
                    ->where('is_active', true)
                    ->get()
                    ->mapWithKeys(fn($r) => [strtolower((string)$r->country_code) => (float)$r->mac_rate_usd]);

                $safeMacRatio = max(1, (int)$ratio);
                $macActualByCountry = $rawMacByCountry->map(fn($r) => [
                    'country_code' => $r->country_code,
                    'installs'     => (int)floor((int)$r->raw_mac / $safeMacRatio),
                    'earnings'     => (float)floor((int)$r->raw_mac / $safeMacRatio) * ($macInstallRates[$r->country_code] ?? 0),
                ])->filter(fn($r) => $r['installs'] > 0)->values();

                $macInstallStats = [
                    'publisher_installs'   => (int)$macPublisherInstalls->sum('install_count'),
                    'publisher_earnings'   => (float)$macPublisherInstalls->sum('earnings'),
                    'actual_installs'      => (int)$macActualByCountry->sum('installs'),
                    'actual_earnings'      => (float)$macActualByCountry->sum('earnings'),
                    'divider_value'        => $macDividerValue,
                    'ratio'                => (int)$ratio,
                    'publisher_by_country' => $macPublisherInstalls,
                    'actual_by_country'    => $macActualByCountry,
                ];
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('macInstallStats failed for user '.$user->id.': '.$e->getMessage());
            }
        }

        // OS breakdown — last 30 days, counted clicks grouped by OS
        $osBreakdown = Click::where('user_id', $user->id)
            ->where('is_counted', true)
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('os, COUNT(*) as cnt')
            ->groupBy('os')
            ->orderByDesc('cnt')
            ->get()
            ->mapWithKeys(fn($r) => [($r->os ?: 'Unknown') => (int)$r->cnt]);

        return view('admin.publishers.show', compact('user', 'clickStats', 'clicksChart', 'divider', 'installStats', 'macInstallStats', 'publisherWebsites', 'osBreakdown'));
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);

        \App\Models\PublisherNotification::create([
            'user_id' => $user->id,
            'type'    => 'account_approved',
            'message' => 'Your account has been approved! You can now choose your contract type and request your ad code to start earning.',
        ]);

        return back()->with('success', 'Publisher activated successfully.');
    }

    public function suspend(User $user)
    {
        $user->update(['status' => 'suspended']);
        return back()->with('success', 'Publisher suspended.');
    }

    public function updateDivider(Request $request, User $user)
    {
        $data = $request->validate([
            'divider_value' => 'required|numeric|min:1|max:100',
            'is_enabled'    => 'boolean',
        ]);

        // Snapshot today's DailyEarning so the new divider only applies to
        // clicks that arrive AFTER this change, not retroactively to today's old clicks
        $today         = today()->toDateString();
        $dailyEarning  = DailyEarning::where('user_id', $user->id)->whereDate('date', $today)->first();
        if ($dailyEarning) {
            $dailyEarning->update([
                'windows_clicks_base_count'   => $dailyEarning->windows_clicks,
                'windows_clicks_base_divided' => $dailyEarning->windows_clicks_divided,
            ]);
        }

        ClickDivider::updateOrCreate(
            ['user_id' => $user->id],
            ['divider_value' => $data['divider_value'], 'is_enabled' => $request->boolean('is_enabled')]
        );

        return back()->with('success', 'Click divider updated.');
    }

    public function updateMacDivider(Request $request, User $user)
    {
        $data = $request->validate([
            'mac_divider_value'   => 'required|numeric|min:1|max:100',
            'mac_divider_enabled' => 'boolean',
        ]);

        // Snapshot today's DailyEarning so the new Mac divider only applies to
        // clicks that arrive AFTER this change, not retroactively to today's old clicks
        $today        = today()->toDateString();
        $dailyEarning = DailyEarning::where('user_id', $user->id)->whereDate('date', $today)->first();
        if ($dailyEarning) {
            $dailyEarning->update([
                'mac_clicks_base_count'   => $dailyEarning->mac_clicks,
                'mac_clicks_base_divided' => $dailyEarning->mac_clicks_divided,
            ]);
        }

        ClickDivider::updateOrCreate(
            ['user_id' => $user->id],
            [
                'mac_divider_value'   => $data['mac_divider_value'],
                'mac_divider_enabled' => $request->boolean('mac_divider_enabled'),
            ]
        );

        return back()->with('success', 'Mac click divider updated.');
    }

    public function updateSettings(Request $request, User $user)
    {
        $user->publisherProfile->update([
            'enforce_domain_restriction' => $request->boolean('enforce_domain_restriction'),
        ]);
        return back()->with('success', 'Publisher settings updated.');
    }

    public function updateTestResults(Request $request, User $user)
    {
        $data = $request->validate([
            'test_total_clicks' => 'required|integer|min:0',
        ]);

        $user->publisherProfile->update([
            'test_total_clicks' => $data['test_total_clicks'],
            'test_status' => 'completed',
        ]);

        return back()->with('success', 'Test results updated. You can now offer a contract.');
    }

    public function updatePaymentStatus(Request $request, User $user)
    {
        $data = $request->validate(['payment_enabled' => 'boolean']);
        $user->publisherProfile->update(['payment_enabled' => $request->boolean('payment_enabled')]);
        return back()->with('success', 'Payment status updated.');
    }

    public function updateFixedRate(Request $request, User $user)
    {
        $profile = $user->publisherProfile;

        if (!$profile || $profile->contract_type !== 'fixed') {
            return back()->with('error', 'This publisher does not have an active Fixed Daily Rate contract.');
        }

        $data = $request->validate([
            'fixed_daily_rate' => 'required|numeric|min:0.0001',
        ]);

        $oldRate = (float) $profile->fixed_daily_rate;
        $newRate = (float) $data['fixed_daily_rate'];

        if ($oldRate == $newRate) {
            return back()->with('error', 'New rate is the same as the current rate. No changes made.');
        }

        $profile->update(['fixed_daily_rate' => $newRate]);

        $direction = $newRate > $oldRate ? 'increased' : 'decreased';
        $type      = $newRate > $oldRate ? 'rate_increase' : 'rate_decrease';

        \App\Models\PublisherNotification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'message' => "Your Fixed Daily Rate has been {$direction} from \${$oldRate} to \${$newRate} per day, effective immediately.",
        ]);

        return back()->with('success', "Fixed rate updated from \${$oldRate} to \${$newRate} and publisher notified.");
    }

    public function stats(User $user, Request $request)
    {
        $period = $request->get('period', '7');

        // Determine date range
        [$startDate, $endDate] = match($period) {
            '1'      => [today(), today()],
            '7'      => [now()->subDays(6)->startOfDay(), today()],
            'last7'  => [now()->subDays(7)->startOfDay(), yesterday()->endOfDay()],
            '30'     => [now()->subDays(29)->startOfDay(), today()],
            'month'  => [now()->startOfMonth()->startOfDay(), yesterday()->endOfDay()],
            '90'     => [now()->subDays(89)->startOfDay(), today()],
            'all'    => [$user->created_at->startOfDay(), today()],
            default  => [now()->subDays(6)->startOfDay(), today()],
        };

        // Tracking link filter
        $allLinks       = TrackingLink::where('user_id', $user->id)->orderBy('name')->get();
        $selectedLinkId = (int) $request->get('link_id', 0);
        $selectedLink   = $selectedLinkId ? $allLinks->firstWhere('id', $selectedLinkId) : null;

        // Base query — scoped to date range and optionally a single tracking link
        $baseQuery = fn() => Click::where('user_id', $user->id)
            ->when($selectedLink, fn($q) => $q->where('tracking_link_id', $selectedLink->id))
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate->copy()->endOfDay());

        // Summary
        $summary = [
            'total_raw' => ($baseQuery)()->count(),
            'valid'     => ($baseQuery)()->where('is_counted', true)->count(),
            'fraud'     => ($baseQuery)()->where('is_fraud', true)->count(),
            'windows'   => ($baseQuery)()->where('is_windows', true)->where('is_counted', true)->count(),
            'earnings'  => ($baseQuery)()->where('is_counted', true)->sum('click_value'),
        ];
        $summary['fraud_rate'] = $summary['total_raw'] > 0
            ? round(($summary['fraud'] / $summary['total_raw']) * 100, 1) : 0;

        // Build day-by-day range
        $daily = [];
        $current = $startDate->copy()->startOfDay();
        $end     = $endDate->copy()->startOfDay();
        while ($current->lte($end)) {
            $date = $current->toDateString();
            $base = Click::where('user_id', $user->id)
                ->when($selectedLink, fn($q) => $q->where('tracking_link_id', $selectedLink->id))
                ->whereDate('created_at', $date);
            $daily[] = [
                'date'     => $current->format('M d'),
                'date_raw' => $date,
                'valid'    => (clone $base)->where('is_counted', true)->count(),
                'fraud'    => (clone $base)->where('is_fraud', true)->count(),
                'windows'  => (clone $base)->where('is_windows', true)->where('is_counted', true)->count(),
                'earnings' => (clone $base)->where('is_counted', true)->sum('click_value'),
            ];
            $current->addDay();
        }

        // Fraud by type
        $fraudByType = ($baseQuery)()->where('is_fraud', true)
            ->selectRaw('fraud_reason, COUNT(*) as count')
            ->groupBy('fraud_reason')
            ->pluck('count', 'fraud_reason')
            ->toArray();

        // Top countries (valid clicks)
        $topCountries = ($baseQuery)()->where('is_counted', true)
            ->selectRaw('country_code, country_name, COUNT(*) as valid_count,
                SUM(CASE WHEN is_fraud = 1 THEN 1 ELSE 0 END) as fraud_count,
                SUM(click_value) as earnings')
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('valid_count')
            ->limit(15)
            ->get();

        // OS breakdown
        $osByType = ($baseQuery)()->selectRaw('os, COUNT(*) as count')
            ->groupBy('os')->orderByDesc('count')->limit(10)->get();

        // Device breakdown
        $deviceTypes = ($baseQuery)()->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')->orderByDesc('count')->get();

        // Recent 30 clicks
        $recentClicks = ($baseQuery)()->latest()->limit(30)->get();

        // Installs data — only for installs_base, only when not filtered to a single link
        // (publisher_installs is per-user, not per-link)
        $installsData = null;
        if (!$selectedLink && $user->publisherProfile?->contract_type === 'installs_base') {
            // Windows installs
            $installRows = PublisherInstall::where('user_id', $user->id)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->orderBy('date')
                ->get();

            $installsByCountry = $installRows->groupBy('country_code')
                ->map(fn($rows) => [
                    'country_code'  => $rows->first()->country_code,
                    'country_name'  => $rows->first()->country_name ?: $rows->first()->country_code,
                    'install_count' => (int)$rows->sum('install_count'),
                    'earnings'      => (float)$rows->sum('earnings'),
                ])
                ->sortByDesc('install_count')
                ->values();

            $installsByDay = $installRows->groupBy(fn($r) => $r->date->toDateString())
                ->map(fn($rows) => [
                    'install_count' => (int)$rows->sum('install_count'),
                    'earnings'      => (float)$rows->sum('earnings'),
                ]);

            // Mac installs
            $macInstallRows = MacPublisherInstall::where('user_id', $user->id)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->orderBy('date')
                ->get();

            $macInstallsByCountry = $macInstallRows->groupBy('country_code')
                ->map(fn($rows) => [
                    'country_code'  => $rows->first()->country_code,
                    'country_name'  => $rows->first()->country_name ?: $rows->first()->country_code,
                    'install_count' => (int)$rows->sum('install_count'),
                    'earnings'      => (float)$rows->sum('earnings'),
                ])
                ->sortByDesc('install_count')
                ->values();

            $macInstallsByDay = $macInstallRows->groupBy(fn($r) => $r->date->toDateString())
                ->map(fn($rows) => [
                    'install_count' => (int)$rows->sum('install_count'),
                    'earnings'      => (float)$rows->sum('earnings'),
                ]);

            $installsData = [
                'total_installs'     => (int)$installRows->sum('install_count'),
                'total_earnings'     => (float)$installRows->sum('earnings'),
                'by_country'         => $installsByCountry,
                'by_day'             => $installsByDay,
                'mac_total_installs' => (int)$macInstallRows->sum('install_count'),
                'mac_total_earnings' => (float)$macInstallRows->sum('earnings'),
                'mac_by_country'     => $macInstallsByCountry,
                'mac_by_day'         => $macInstallsByDay,
            ];

            $daily = array_map(fn($d) => array_merge($d, [
                'installs'             => $installsByDay->get($d['date_raw'])['install_count'] ?? 0,
                'install_earnings'     => $installsByDay->get($d['date_raw'])['earnings'] ?? 0.0,
                'mac_installs'         => $macInstallsByDay->get($d['date_raw'])['install_count'] ?? 0,
                'mac_install_earnings' => $macInstallsByDay->get($d['date_raw'])['earnings'] ?? 0.0,
            ]), $daily);
        }

        return view('admin.publishers.stats', compact(
            'user', 'period', 'summary', 'daily', 'installsData',
            'fraudByType', 'topCountries', 'osByType', 'deviceTypes', 'recentClicks',
            'allLinks', 'selectedLink'
        ));
    }

    public function exportStats(User $user, Request $request)
    {
        $period = $request->get('period', '7');

        [$startDate, $endDate] = match($period) {
            '1'      => [today(), today()],
            '7'      => [now()->subDays(6)->startOfDay(), today()],
            'last7'  => [now()->subDays(7)->startOfDay(), yesterday()->endOfDay()],
            '30'     => [now()->subDays(29)->startOfDay(), today()],
            'month'  => [now()->startOfMonth()->startOfDay(), yesterday()->endOfDay()],
            '90'     => [now()->subDays(89)->startOfDay(), today()],
            'all'    => [$user->created_at->startOfDay(), today()],
            default  => [now()->subDays(6)->startOfDay(), today()],
        };

        $rows = [];
        $current = $startDate->copy()->startOfDay();
        $end     = $endDate->copy()->startOfDay();
        while ($current->lte($end)) {
            $date = $current->toDateString();
            $rows[] = [
                $date,
                Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_counted', true)->count(),
                Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_fraud', true)->count(),
                Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_windows', true)->where('is_counted', true)->count(),
                number_format(Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_counted', true)->sum('click_value'), 6, '.', ''),
            ];
            $current->addDay();
        }

        $filename = 'publisher-stats-' . $user->id . '-' . $period . '-' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows, $user) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['Publisher: ' . $user->name . ' (' . $user->email . ')']);
            fputcsv($f, ['Date', 'Valid Clicks', 'Fraud Clicks', 'Windows Clicks', 'Earnings (USD)']);
            foreach ($rows as $row) {
                fputcsv($f, $row);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateFraudSettings(Request $request, User $user)
    {
        $data = $request->validate([
            'fraud_country_mismatch'    => 'boolean',
            'fraud_suspicious_referrer' => 'boolean',
            'fraud_headless_browser'    => 'boolean',
            'allowed_countries'         => 'nullable|string',
        ]);

        // Parse comma-separated country codes into an array (or null for all)
        $countries = null;
        if (!empty($data['allowed_countries'])) {
            $countries = array_values(array_filter(
                array_map('trim', explode(',', strtoupper($data['allowed_countries'])))
            ));
            if (empty($countries)) $countries = null;
        }

        $user->publisherProfile->update([
            'fraud_country_mismatch'    => $request->boolean('fraud_country_mismatch'),
            'fraud_suspicious_referrer' => $request->boolean('fraud_suspicious_referrer'),
            'fraud_headless_browser'    => $request->boolean('fraud_headless_browser'),
            'allowed_countries'         => $countries,
        ]);

        return back()->with('success', 'Fraud detection settings updated.');
    }

    public function destroy(User $user)
    {
        if (!$user->isPublisher()) {
            return back()->with('error', 'Can only delete publisher accounts.');
        }

        // Cascade delete all related data
        Click::where('user_id', $user->id)->delete();
        FraudAlert::where('user_id', $user->id)->delete();
        DailyEarning::where('user_id', $user->id)->delete();
        Withdrawal::where('user_id', $user->id)->delete();

        // Delete tracking links and their related clicks (already done above, but clean)
        TrackingLink::where('user_id', $user->id)->delete();

        // Delete profile and settings
        PublisherProfile::where('user_id', $user->id)->delete();
        ClickDivider::where('user_id', $user->id)->delete();
        PublisherTag::where('user_id', $user->id)->delete();
        Contract::where('user_id', $user->id)->delete();

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.publishers.index')
            ->with('success', "Publisher \"{$name}\" and all related data have been permanently deleted.");
    }

    public function recalculateInstalls(User $user)
    {
        $profile = $user->publisherProfile;
        if ($profile?->contract_type !== 'installs_base') {
            return back()->with('error', 'This publisher is not on an installs_base contract.');
        }

        $divider = $user->clickDivider;
        if (!$divider || !$divider->is_enabled) {
            return back()->with('error', 'No active divider set for this publisher. Enable the divider first, then recalculate.');
        }

        $dividerValue = max(1, (float)$divider->divider_value);

        $dayRatios    = \App\Models\InstallDayRatio::pluck('ratio', 'weekday')->toArray();
        $installRates = \App\Models\InstallCountryRate::where('is_active', true)
            ->get()
            ->mapWithKeys(fn($r) => [strtolower($r->country_code) => (float)$r->rate_usd]);

        // Save country names from Click table
        $countryNames = Click::where('user_id', $user->id)
            ->where('is_windows', true)
            ->where('is_counted', true)
            ->selectRaw('LOWER(country_code) as cc, MAX(country_name) as cn')
            ->groupBy('cc')
            ->pluck('cn', 'cc');

        // All counted Windows clicks, grouped by date + country, in date order
        $clickRows = Click::where('user_id', $user->id)
            ->where('is_counted', true)
            ->where('is_windows', true)
            ->selectRaw('DATE(created_at) as day,
                         LOWER(country_code) as country_code,
                         COUNT(*) as raw_clicks,
                         (DAYOFWEEK(created_at) - 1) as weekday')
            ->groupBy('day', 'country_code', 'weekday')
            ->orderBy('day')
            ->get();

        // Simulate correct install history with divider applied
        $pending     = [];
        $correctData = [];

        foreach ($clickRows as $row) {
            $country        = $row->country_code;
            $ratio          = $dayRatios[(int)$row->weekday] ?? 30;
            $effectiveRatio = max(1, (int)round($ratio * $dividerValue));

            $pending[$country] = ($pending[$country] ?? 0) + $row->raw_clicks;
            $installs          = (int)floor($pending[$country] / $effectiveRatio);

            if ($installs > 0) {
                $pending[$country] = $pending[$country] % $effectiveRatio;
                $earnings          = $installs * ($installRates[$country] ?? 0);

                if (!isset($correctData[$row->day][$country])) {
                    $correctData[$row->day][$country] = ['install_count' => 0, 'earnings' => 0.0];
                }
                $correctData[$row->day][$country]['install_count'] += $installs;
                $correctData[$row->day][$country]['earnings']       += $earnings;
            }
        }

        // Current totals before touching anything
        $currentEarnings = (float)PublisherInstall::where('user_id', $user->id)->sum('earnings');
        $correctEarnings = 0.0;
        foreach ($correctData as $countries) {
            foreach ($countries as $d) {
                $correctEarnings += $d['earnings'];
            }
        }
        $earningsDiff = round($currentEarnings - $correctEarnings, 6);

        // Rebuild publisher_installs
        PublisherInstall::where('user_id', $user->id)->delete();
        foreach ($correctData as $date => $countries) {
            foreach ($countries as $country => $data) {
                PublisherInstall::create([
                    'user_id'       => $user->id,
                    'country_code'  => $country,
                    'country_name'  => $countryNames[$country] ?? strtoupper($country),
                    'install_count' => $data['install_count'],
                    'earnings'      => $data['earnings'],
                    'date'          => $date,
                ]);
            }
        }

        // Adjust balance and total_earnings
        if ($earningsDiff > 0) {
            $safeDeduct = min($earningsDiff, $profile->balance);
            $profile->decrement('balance', $safeDeduct);
            $profile->decrement('total_earnings', $earningsDiff);
        } elseif ($earningsDiff < 0) {
            $profile->increment('balance', abs($earningsDiff));
            $profile->increment('total_earnings', abs($earningsDiff));
        }

        // Reset pending clicks to correct state
        $profile->update(['install_pending_clicks' => $pending]);

        $msg = "Installs recalculated with divider ({$dividerValue}×). "
             . "Earnings adjusted by \$" . number_format(abs($earningsDiff), 6)
             . ($earningsDiff > 0 ? ' (over-credit removed)' : ($earningsDiff < 0 ? ' (under-credit added)' : ' (no change)'));

        return back()->with('success', $msg);
    }

    public function recalculateMacInstalls(User $user)
    {
        $profile = $user->publisherProfile;
        if ($profile?->contract_type !== 'installs_base') {
            return back()->with('error', 'This publisher is not on an installs_base contract.');
        }

        $divider = $user->clickDivider;
        $macDividerValue = ($divider && ($divider->mac_divider_enabled ?? false))
            ? max(1, (float)($divider->mac_divider_value ?? 1))
            : 1;

        $dayRatios    = \App\Models\InstallDayRatio::pluck('ratio', 'weekday')->toArray();
        $macRates     = MacInstallCountryRate::where('is_active', true)
            ->get()
            ->mapWithKeys(fn($r) => [strtolower($r->country_code) => (float)$r->mac_rate_usd]);

        $countryNames = Click::where('user_id', $user->id)
            ->where('is_mac', true)
            ->where('is_counted', true)
            ->selectRaw('LOWER(country_code) as cc, MAX(country_name) as cn')
            ->groupBy('cc')
            ->pluck('cn', 'cc');

        $clickRows = Click::where('user_id', $user->id)
            ->where('is_counted', true)
            ->where('is_mac', true)
            ->selectRaw('DATE(created_at) as day,
                         LOWER(country_code) as country_code,
                         COUNT(*) as raw_clicks,
                         (DAYOFWEEK(created_at) - 1) as weekday')
            ->groupBy('day', 'country_code', 'weekday')
            ->orderBy('day')
            ->get();

        $pending     = [];
        $correctData = [];

        foreach ($clickRows as $row) {
            $country        = $row->country_code;
            $ratio          = $dayRatios[(int)$row->weekday] ?? 30;
            $effectiveRatio = max(1, (int)round($ratio * $macDividerValue));

            $pending[$country] = ($pending[$country] ?? 0) + $row->raw_clicks;
            $installs          = (int)floor($pending[$country] / $effectiveRatio);

            if ($installs > 0) {
                $pending[$country] = $pending[$country] % $effectiveRatio;
                $earnings          = $installs * ($macRates[$country] ?? 0);

                if (!isset($correctData[$row->day][$country])) {
                    $correctData[$row->day][$country] = ['install_count' => 0, 'earnings' => 0.0];
                }
                $correctData[$row->day][$country]['install_count'] += $installs;
                $correctData[$row->day][$country]['earnings']       += $earnings;
            }
        }

        $currentEarnings = (float)MacPublisherInstall::where('user_id', $user->id)->sum('earnings');
        $correctEarnings = 0.0;
        foreach ($correctData as $countries) {
            foreach ($countries as $d) {
                $correctEarnings += $d['earnings'];
            }
        }
        $earningsDiff = round($currentEarnings - $correctEarnings, 6);

        MacPublisherInstall::where('user_id', $user->id)->delete();
        foreach ($correctData as $date => $countries) {
            foreach ($countries as $country => $data) {
                MacPublisherInstall::create([
                    'user_id'       => $user->id,
                    'country_code'  => $country,
                    'country_name'  => $countryNames[$country] ?? strtoupper($country),
                    'install_count' => $data['install_count'],
                    'earnings'      => $data['earnings'],
                    'date'          => $date,
                ]);
            }
        }

        if ($earningsDiff > 0) {
            $safeDeduct = min($earningsDiff, $profile->balance);
            $profile->decrement('balance', $safeDeduct);
            $profile->decrement('total_earnings', $earningsDiff);
        } elseif ($earningsDiff < 0) {
            $profile->increment('balance', abs($earningsDiff));
            $profile->increment('total_earnings', abs($earningsDiff));
        }

        $profile->update(['mac_install_pending_clicks' => $pending]);

        $msg = "Mac installs recalculated with Mac divider ({$macDividerValue}×). "
             . "Earnings adjusted by \$" . number_format(abs($earningsDiff), 6)
             . ($earningsDiff > 0 ? ' (over-credit removed)' : ($earningsDiff < 0 ? ' (under-credit added)' : ' (no change)'));

        return back()->with('success', $msg);
    }

    public function addTag(Request $request, User $user)
    {
        $data = $request->validate([
            'tag'   => 'required|string|max:50',
            'color' => 'required|in:green,blue,red,amber,gray,purple',
        ]);

        PublisherTag::firstOrCreate(
            ['user_id' => $user->id, 'tag' => trim($data['tag'])],
            ['color' => $data['color']]
        );

        return back()->with('success', 'Tag added.');
    }

    public function removeTag(Request $request, User $user)
    {
        $request->validate(['tag' => 'required|string']);
        PublisherTag::where('user_id', $user->id)->where('tag', $request->tag)->delete();
        return back()->with('success', 'Tag removed.');
    }

    public function generateAdCode(User $user)
    {
        $link = TrackingLink::where('user_id', $user->id)->first();
        if (!$link) {
            return back()->with('error', 'Publisher has no tracking link yet.');
        }
        return back()->with('success', 'Ad code available in publisher dashboard.');
    }
}
