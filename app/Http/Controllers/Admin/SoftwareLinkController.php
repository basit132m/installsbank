<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareCampaign;
use App\Models\SoftwareLink;
use Illuminate\Http\Request;

class SoftwareLinkController extends Controller
{
    public function index()
    {
        $links = SoftwareLink::with('campaign.user')->latest()->paginate(30);
        return view('admin.software-links.index', compact('links'));
    }

    public function create()
    {
        // Campaigns that have an installer uploaded can have links.
        $campaigns = SoftwareCampaign::with('user')
            ->whereNotNull('payload_path')
            ->latest()
            ->get();

        return view('admin.software-links.create', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'software_campaign_id' => 'required|exists:software_campaigns,id',
            'name'                 => 'nullable|string|max:120',
            'fallback_url'         => 'nullable|url|max:2048',
        ]);

        $link = SoftwareLink::create([
            'software_campaign_id' => $data['software_campaign_id'],
            'name'                 => $data['name'] ?? null,
            'fallback_url'         => $data['fallback_url'] ?? null,
            'is_active'            => true,
        ]);

        return redirect()->route('admin.software-links.index')
            ->with('success', 'Link created: ' . url(route('software.go', ['code' => $link->code], false)));
    }

    public function toggle(SoftwareLink $softwareLink)
    {
        $softwareLink->update(['is_active' => !$softwareLink->is_active]);
        return back()->with('success', $softwareLink->is_active ? 'Link activated.' : 'Link paused.');
    }

    public function update(Request $request, SoftwareLink $softwareLink)
    {
        $data = $request->validate([
            'name'         => 'nullable|string|max:120',
            'fallback_url' => 'nullable|url|max:2048',
        ]);
        $softwareLink->update([
            'name'         => $data['name'] ?? null,
            'fallback_url' => $data['fallback_url'] ?? null,
        ]);
        return back()->with('success', 'Link updated.');
    }

    public function destroy(SoftwareLink $softwareLink)
    {
        $softwareLink->delete();
        return back()->with('success', 'Link deleted.');
    }
}
