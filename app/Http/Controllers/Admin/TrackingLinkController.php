<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdButton;
use App\Models\AdPreset;
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
        $domains = TrackingDomain::where('is_active', true)->get();
        return view('admin.tracking.create', compact('publishers', 'domains'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'tracking_domain_id' => 'nullable|exists:tracking_domains,id',
            'name'               => 'nullable|string|max:100',
            'original_url'       => 'required|url',
            'url_windows'        => 'nullable|url',
            'url_android'        => 'nullable|url',
            'url_mac'            => 'nullable|url',
            'url_other'          => 'nullable|url',
        ]);

        $link = TrackingLink::create($data);
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
