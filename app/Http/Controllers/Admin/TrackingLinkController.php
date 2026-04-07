<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdButton;
use App\Models\AdPreset;
use App\Models\Campaign;
use App\Models\TrackingDomain;
use App\Models\TrackingLink;
use App\Models\User;
use Illuminate\Http\Request;

class TrackingLinkController extends Controller
{
    public function index()
    {
        $links = TrackingLink::with('user')->latest()->paginate(20);
        return view('admin.tracking.index', compact('links'));
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

    public function edit(TrackingLink $trackingLink)
    {
        $domains = TrackingDomain::where('is_active', true)->get();
        return view('admin.tracking.edit', compact('trackingLink', 'domains'));
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
        ]);

        $trackingLink->update($data);
        return redirect()->route('admin.tracking.index')
            ->with('success', 'Tracking link updated successfully.');
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
