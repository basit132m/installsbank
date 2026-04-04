<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClickDivider;
use App\Models\Click;
use App\Models\Contract;
use App\Models\DailyEarning;
use App\Models\FraudAlert;
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
        $query = User::where('role', 'publisher')->with('publisherProfile');

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
        $user->load(['publisherProfile', 'trackingLinks', 'contracts', 'clickDivider', 'publisherTags']);

        $clickStats = [
            'today' => Click::where('user_id', $user->id)->whereDate('created_at', today())->where('is_counted', true)->count(),
            'today_windows' => Click::where('user_id', $user->id)->whereDate('created_at', today())->where('is_counted', true)->where('is_windows', true)->count(),
            'today_fraud' => Click::where('user_id', $user->id)->whereDate('created_at', today())->where('is_fraud', true)->count(),
            'this_week' => Click::where('user_id', $user->id)->whereBetween('created_at', [now()->startOfWeek(), now()])->where('is_counted', true)->count(),
            'this_month' => Click::where('user_id', $user->id)->whereMonth('created_at', now()->month)->where('is_counted', true)->count(),
            'total' => Click::where('user_id', $user->id)->where('is_counted', true)->count(),
            'total_actual_windows' => Click::where('user_id', $user->id)->whereDate('created_at', today())->where('is_windows', true)->where('is_counted', true)->count(),
        ];

        // Daily clicks chart (last 14 days)
        $clicksChart = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $clicksChart[] = [
                'date' => now()->subDays($i)->format('M d'),
                'actual' => Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_counted', true)->count(),
                'windows' => Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_windows', true)->where('is_counted', true)->count(),
                'fraud' => Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_fraud', true)->count(),
            ];
        }

        $divider = $user->clickDivider ?: ClickDivider::firstOrCreate(['user_id' => $user->id], ['divider_value' => 1, 'is_enabled' => false]);

        return view('admin.publishers.show', compact('user', 'clickStats', 'clicksChart', 'divider'));
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
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
            'is_enabled' => 'boolean',
        ]);

        ClickDivider::updateOrCreate(
            ['user_id' => $user->id],
            ['divider_value' => $data['divider_value'], 'is_enabled' => $request->boolean('is_enabled')]
        );

        return back()->with('success', 'Click divider updated.');
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

    public function stats(User $user, Request $request)
    {
        $period = $request->get('period', '7');
        $startDate = match($period) {
            '1'    => today(),
            '7'    => now()->subDays(6),
            '30'   => now()->subDays(29),
            '90'   => now()->subDays(89),
            'all'  => now()->subYears(10),
            default => now()->subDays(6),
        };

        $baseQuery = fn() => Click::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate->startOfDay());

        // Summary
        $summary = [
            'total_raw'    => ($baseQuery)()->count(),
            'valid'        => ($baseQuery)()->where('is_counted', true)->count(),
            'fraud'        => ($baseQuery)()->where('is_fraud', true)->count(),
            'windows'      => ($baseQuery)()->where('is_windows', true)->where('is_counted', true)->count(),
            'earnings'     => ($baseQuery)()->where('is_counted', true)->sum('click_value'),
        ];
        $summary['fraud_rate'] = $summary['total_raw'] > 0
            ? round(($summary['fraud'] / $summary['total_raw']) * 100, 1) : 0;

        // Daily breakdown (last N days)
        $days = match($period) { '1' => 1, '7' => 7, '30' => 30, '90' => 90, default => 7 };
        $daily = [];
        for ($i = min($days - 1, 89); $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $daily[] = [
                'date'    => now()->subDays($i)->format('M d'),
                'valid'   => Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_counted', true)->count(),
                'fraud'   => Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_fraud', true)->count(),
                'windows' => Click::where('user_id', $user->id)->whereDate('created_at', $date)->where('is_windows', true)->where('is_counted', true)->count(),
            ];
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

        return view('admin.publishers.stats', compact(
            'user', 'period', 'summary', 'daily',
            'fraudByType', 'topCountries', 'osByType', 'deviceTypes', 'recentClicks'
        ));
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
