<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DomainChangedMailable;
use App\Models\AdButton;
use App\Models\AdPreset;
use App\Models\Campaign;
use App\Models\PublisherNotification;
use App\Models\TrackingDomain;
use App\Models\TrackingLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TrackingLinkController extends Controller
{
    public function index()
    {
        $links      = TrackingLink::with(['user', 'trackingDomain'])->latest()->paginate(20);
        $publishers = User::where('role', 'publisher')->orderBy('name')->get(['id', 'name', 'email']);
        $domains    = TrackingDomain::where('is_active', true)->orderBy('domain')->get();
        return view('admin.tracking.index', compact('links', 'publishers', 'domains'));
    }

    public function swapDomain(Request $request, TrackingLink $trackingLink)
    {
        $data = $request->validate([
            'tracking_domain_id' => 'nullable|exists:tracking_domains,id',
        ]);

        $oldDomain = $trackingLink->trackingDomain?->domain ?? 'default';
        $trackingLink->update(['tracking_domain_id' => $data['tracking_domain_id'] ?: null]);

        $newDomainModel = $data['tracking_domain_id']
            ? TrackingDomain::find($data['tracking_domain_id'])
            : null;
        $newDomain      = $newDomainModel?->domain ?? 'default';
        $newTrackingUrl = $trackingLink->fresh()->tracking_url;

        // Notify publisher
        $publisher = $trackingLink->user;
        if ($publisher) {
            $message = "Your tracking link domain has been updated.\n\nLink Code: {$trackingLink->unique_code}\nPrevious Domain: {$oldDomain}\nNew Domain: {$newDomain}\n\nPlease update your website immediately with the new tracking URL:\n{$newTrackingUrl}";

            PublisherNotification::create([
                'user_id' => $publisher->id,
                'type'    => 'domain_changed',
                'message' => $message,
            ]);

            try {
                Mail::to($publisher->email)->send(new DomainChangedMailable(
                    publisherName:  $publisher->name,
                    uniqueCode:     $trackingLink->unique_code,
                    oldDomain:      $oldDomain,
                    newDomain:      $newDomain,
                    newTrackingUrl: $newTrackingUrl,
                ));
            } catch (\Throwable $e) {
                // Email failure shouldn't block the domain swap
                \Log::warning('DomainChanged email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Domain for link «{$trackingLink->unique_code}» changed from {$oldDomain} to {$newDomain}. Publisher notified.");
    }

    public function reassign(Request $request, TrackingLink $trackingLink)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $oldPublisher = $trackingLink->user->name ?? 'Unknown';
        $trackingLink->update(['user_id' => $data['user_id']]);
        $newPublisher = User::find($data['user_id'])->name;

        return back()->with('success', "Link «{$trackingLink->unique_code}» reassigned from {$oldPublisher} to {$newPublisher}.");
    }

    public function create()
    {
        $publishers = User::where('role', 'publisher')->where('status', 'active')->get();
        $advertisers = User::where('role', 'advertiser')->where('status', 'active')
            ->with(['campaigns' => function ($q) {
                $q->whereNotIn('status', ['completed', 'cancelled'])->latest()->limit(1);
            }])->get();
        $domains = TrackingDomain::where('is_active', true)->get();
        return view('admin.tracking.create', compact('publishers', 'advertisers', 'domains'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'link_owner'         => 'required|in:publisher,advertiser',
            'user_id'            => 'required_if:link_owner,publisher|nullable|exists:users,id',
            'advertiser_id'      => 'required_if:link_owner,advertiser|nullable|exists:users,id',
            'tracking_domain_id' => 'nullable|exists:tracking_domains,id',
            'name'               => 'nullable|string|max:100',
            'original_url'       => 'required|url',
            'url_windows'        => 'nullable|url',
            'url_android'        => 'nullable|url',
            'url_mac'            => 'nullable|url',
            'url_other'          => 'nullable|url',
        ]);

        $linkData = [
            'tracking_domain_id' => $data['tracking_domain_id'] ?? null,
            'name'               => $data['name'] ?? null,
            'original_url'       => $data['original_url'],
            'url_windows'        => $data['url_windows'] ?? null,
            'url_android'        => $data['url_android'] ?? null,
            'url_mac'            => $data['url_mac'] ?? null,
            'url_other'          => $data['url_other'] ?? null,
        ];

        if ($data['link_owner'] === 'advertiser' && !empty($data['advertiser_id'])) {
            $campaign = Campaign::where('user_id', $data['advertiser_id'])
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->latest()
                ->first();
            $linkData['user_id'] = $data['advertiser_id'];
            $linkData['campaign_id'] = $campaign?->id;
        } else {
            $linkData['user_id'] = $data['user_id'];
        }

        $link = TrackingLink::create($linkData);
        return redirect()->route('admin.tracking.index')
            ->with('success', 'Tracking link created. Code: ' . $link->unique_code);
    }

    public function edit(TrackingLink $trackingLink, Request $request)
    {
        $domains = TrackingDomain::where('is_active', true)->get();

        // Windows redirect stats — how many Windows clicks went to each destination URL
        $period = $request->get('stats', 'today');
        [$statsStart, $statsLabel] = match ($period) {
            '7d'   => [now()->subDays(6)->startOfDay(), 'Last 7 days'],
            '30d'  => [now()->subDays(29)->startOfDay(), 'Last 30 days'],
            'all'  => [null, 'All time'],
            default => [now()->startOfDay(), 'Today'],
        };

        $winStatsQuery = \App\Models\Click::where('tracking_link_id', $trackingLink->id)
            ->where('is_windows', true);
        if ($statsStart) {
            $winStatsQuery->where('created_at', '>=', $statsStart);
        }

        // Counts of Windows clicks grouped by the exact URL they were redirected to
        $windowsRedirectStats = (clone $winStatsQuery)
            ->selectRaw('redirect_url, COUNT(*) as total, SUM(CASE WHEN is_counted = 1 THEN 1 ELSE 0 END) as counted')
            ->groupBy('redirect_url')
            ->get()
            ->keyBy('redirect_url');

        $windowsTotalClicks = (int) (clone $winStatsQuery)->count();

        // Current-activation counts — clicks during only the slot's current/most-recent
        // window occurrence, so the number resets to 0 each time the window restarts.
        $slotCurrentCounts = [];
        foreach ($trackingLink->windows_schedules ?? [] as $i => $slot) {
            $range = $trackingLink->currentWindowRange($slot);
            if (!$range || empty($slot['url'])) {
                $slotCurrentCounts[$i] = ['count' => 0, 'active' => false, 'start' => null, 'end' => null];
                continue;
            }
            $count = \App\Models\Click::where('tracking_link_id', $trackingLink->id)
                ->where('is_windows', true)
                ->where('redirect_url', $slot['url'])
                ->where('created_at', '>=', $range['start'])
                ->where('created_at', '<', $range['end'])
                ->count();
            $slotCurrentCounts[$i] = [
                'count'  => (int) $count,
                'active' => $range['active'],
                'start'  => $range['start'],
                'end'    => $range['end'],
            ];
        }

        return view('admin.tracking.edit', compact(
            'trackingLink', 'domains',
            'windowsRedirectStats', 'windowsTotalClicks', 'period', 'statsLabel',
            'slotCurrentCounts'
        ));
    }

    public function windowsHistory(TrackingLink $trackingLink)
    {
        // Last 30 days of Windows redirect clicks, grouped by day and destination URL
        $start = now()->subDays(29)->startOfDay();

        $rows = \App\Models\Click::where('tracking_link_id', $trackingLink->id)
            ->where('is_windows', true)
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, redirect_url,
                COUNT(*) as total,
                SUM(CASE WHEN is_counted = 1 THEN 1 ELSE 0 END) as counted')
            ->groupBy('day', 'redirect_url')
            ->orderByDesc('day')
            ->get();

        // Map each URL to a friendly label from the saved slots (Timer N + note)
        $urlLabels = [];
        foreach ($trackingLink->windows_schedules ?? [] as $i => $slot) {
            if (!empty($slot['url'])) {
                $urlLabels[$slot['url']] = 'Timer ' . ($i + 1) . (!empty($slot['note']) ? ' — ' . $slot['note'] : '');
            }
        }
        if ($trackingLink->url_windows) {
            $urlLabels[$trackingLink->url_windows] = 'Default Windows URL';
        }

        $byDay = $rows->groupBy('day');

        return view('admin.tracking.windows-history', compact('trackingLink', 'byDay', 'urlLabels'));
    }

    public function update(Request $request, TrackingLink $trackingLink)
    {
        $data = $request->validate([
            'tracking_domain_id' => 'nullable|exists:tracking_domains,id',
            'name'               => 'nullable|string|max:100',
            'original_url'       => 'required|url',
            'url_windows'        => 'nullable|url',
            'url_android'        => 'nullable|url',
            'url_mac'            => 'nullable|url',
            'url_other'          => 'nullable|url',
            'windows_schedule_enabled' => 'boolean',
            'schedule_start'     => 'nullable|array|max:3',
            'schedule_start.*'   => 'nullable|date_format:H:i',
            'schedule_end'       => 'nullable|array|max:3',
            'schedule_end.*'     => 'nullable|date_format:H:i',
            'schedule_url'       => 'nullable|array|max:3',
            'schedule_url.*'     => 'nullable|url',
            'schedule_note'      => 'nullable|array|max:3',
            'schedule_note.*'    => 'nullable|string|max:150',
        ]);

        // Build schedule slots — keep only rows where all three fields are filled
        $schedules = [];
        foreach ($data['schedule_start'] ?? [] as $i => $start) {
            $end  = $data['schedule_end'][$i] ?? null;
            $url  = $data['schedule_url'][$i] ?? null;
            $note = trim((string)($data['schedule_note'][$i] ?? ''));
            if ($start && $end && $url) {
                $schedules[] = ['start' => $start, 'end' => $end, 'url' => $url, 'note' => $note ?: null];
            }
        }

        $enabled = $request->boolean('windows_schedule_enabled');
        if ($enabled && empty($schedules)) {
            return back()->withInput()
                ->with('error', 'Auto redirect timer is ON but no complete slot was provided. Fill start time, end time and URL for at least one slot, or turn the timer off.');
        }

        $trackingLink->update([
            'tracking_domain_id' => $data['tracking_domain_id'] ?? null,
            'name'               => $data['name'] ?? null,
            'original_url'       => $data['original_url'],
            'url_windows'        => $data['url_windows'] ?? null,
            'url_android'        => $data['url_android'] ?? null,
            'url_mac'            => $data['url_mac'] ?? null,
            'url_other'          => $data['url_other'] ?? null,
            'windows_schedule_enabled' => $enabled,
            'windows_schedules'  => $schedules ?: null,
        ]);

        // Stay on the edit page so the admin can immediately verify the SERVER CHECK box
        return redirect()->route('admin.tracking.edit', $trackingLink)
            ->with('success', 'Tracking link updated successfully.');
    }

    public function toggleSchedule(TrackingLink $trackingLink)
    {
        if (!$trackingLink->windows_schedule_enabled && empty($trackingLink->windows_schedules)) {
            return back()->with('error', 'Set up at least one timer slot before enabling the auto redirect.');
        }

        $trackingLink->update(['windows_schedule_enabled' => !$trackingLink->windows_schedule_enabled]);

        return back()->with('success', 'Auto Windows redirect timer '
            . ($trackingLink->windows_schedule_enabled ? 'enabled' : 'disabled') . '.');
    }

    public function destroy(TrackingLink $trackingLink)
    {
        $trackingLink->delete();
        return back()->with('success', 'Tracking link deleted.');
    }

    public function toggle(TrackingLink $trackingLink)
    {
        $trackingLink->update(['is_active' => !$trackingLink->is_active]);
        return back()->with('success', 'Status updated.');
    }
}
