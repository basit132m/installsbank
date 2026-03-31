<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClickDivider;
use App\Models\Click;
use App\Models\PublisherProfile;
use App\Models\TrackingLink;
use App\Models\User;
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
        $user->load(['publisherProfile', 'trackingLinks', 'contracts', 'clickDivider']);

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

    public function generateAdCode(User $user)
    {
        $link = TrackingLink::where('user_id', $user->id)->first();
        if (!$link) {
            return back()->with('error', 'Publisher has no tracking link yet.');
        }
        return back()->with('success', 'Ad code available in publisher dashboard.');
    }
}
