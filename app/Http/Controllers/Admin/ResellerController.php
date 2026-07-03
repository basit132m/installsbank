<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\ClickDivider;
use App\Models\DailyEarning;
use App\Models\PublisherWebsite;
use App\Models\TrackingLink;
use App\Models\User;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'reseller');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $resellers    = $query->latest()->paginate(20);
        $pendingCount = User::where('role', 'reseller')->where('status', 'pending')->count();

        // Today's clicks per reseller for the list
        $todayClicks = DailyEarning::whereDate('date', today())
            ->whereIn('user_id', $resellers->pluck('id'))
            ->pluck('valid_clicks', 'user_id');

        return view('admin.resellers.index', compact('resellers', 'pendingCount', 'todayClicks'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'reseller', 404);

        $user->load('clickDivider');
        $divider = $user->clickDivider ?: ClickDivider::firstOrCreate(
            ['user_id' => $user->id],
            ['divider_value' => 1, 'is_enabled' => false]
        );

        $dailyEarningToday = DailyEarning::where('user_id', $user->id)->whereDate('date', today())->first();

        $clickStats = [
            'today'         => Click::where('user_id', $user->id)->whereDate('created_at', today())->where('is_counted', true)->count(),
            'today_shown'   => (int)($dailyEarningToday?->valid_clicks ?? 0),
            'today_windows' => (int)($dailyEarningToday?->windows_clicks_divided ?? 0),
            'today_mac'     => (int)($dailyEarningToday?->mac_clicks_divided ?? 0),
            'today_fraud'   => Click::where('user_id', $user->id)->whereDate('created_at', today())->where('is_fraud', true)->count(),
            'this_week'     => Click::where('user_id', $user->id)->whereBetween('created_at', [now()->startOfWeek(), now()])->where('is_counted', true)->count(),
            'total'         => Click::where('user_id', $user->id)->where('is_counted', true)->count(),
        ];

        // 14-day chart from DailyEarning
        $clicksChart = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $de   = DailyEarning::where('user_id', $user->id)->whereDate('date', $date)->first();
            $clicksChart[] = [
                'date'   => now()->subDays($i)->format('M d'),
                'clicks' => (int)($de?->valid_clicks ?? 0),
            ];
        }

        $websites = PublisherWebsite::with('trackingLink.trackingDomain')
            ->where('user_id', $user->id)->latest()->get();

        $trackingLinks = TrackingLink::with('trackingDomain')
            ->where('user_id', $user->id)->latest()->get();

        return view('admin.resellers.show', compact(
            'user', 'divider', 'clickStats', 'clicksChart', 'websites', 'trackingLinks'
        ));
    }

    public function activate(User $user)
    {
        abort_unless($user->role === 'reseller', 404);

        $user->update(['status' => 'active']);

        \App\Models\PublisherNotification::create([
            'user_id' => $user->id,
            'type'    => 'account_approved',
            'message' => 'Your reseller account has been approved! Add your website(s) to receive your ad code and start tracking.',
        ]);

        return back()->with('success', 'Reseller activated successfully.');
    }

    public function suspend(User $user)
    {
        abort_unless($user->role === 'reseller', 404);

        $user->update(['status' => 'suspended']);

        // Also pause their tracking links so traffic stops immediately
        TrackingLink::where('user_id', $user->id)->update(['is_active' => false]);

        return back()->with('success', 'Reseller suspended and tracking links paused.');
    }
}
