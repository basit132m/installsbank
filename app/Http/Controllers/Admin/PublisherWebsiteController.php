<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublisherWebsite;
use App\Models\TrackingLink;
use Illuminate\Http\Request;

class PublisherWebsiteController extends Controller
{
    public function index(Request $request)
    {
        $query = PublisherWebsite::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $websites = $query->paginate(30);
        $pendingCount = PublisherWebsite::where('status', 'pending')->count();

        return view('admin.publisher-websites.index', compact('websites', 'pendingCount'));
    }

    public function approve(Request $request, PublisherWebsite $publisherWebsite)
    {
        if (!$publisherWebsite->isPending()) {
            return back()->with('error', 'This website has already been reviewed.');
        }

        $request->validate([
            'original_url' => 'required|url',
            'url_windows'  => 'nullable|url',
            'url_android'  => 'nullable|url',
            'url_mac'      => 'nullable|url',
            'url_other'    => 'nullable|url',
        ]);

        // Create a dedicated tracking link locked to this domain
        $link = TrackingLink::create([
            'user_id'       => $publisherWebsite->user_id,
            'name'          => 'Website: ' . $publisherWebsite->domain,
            'original_url'  => $request->original_url,
            'url_windows'   => $request->url_windows ?: null,
            'url_android'   => $request->url_android ?: null,
            'url_mac'       => $request->url_mac ?: null,
            'url_other'     => $request->url_other ?: null,
            'is_active'     => true,
            'allowed_domain'=> $publisherWebsite->domain,
        ]);

        $publisherWebsite->update([
            'status'          => 'approved',
            'tracking_link_id'=> $link->id,
            'reviewed_by'     => auth()->id(),
            'reviewed_at'     => now(),
        ]);

        return back()->with('success', 'Website approved and ad code created for ' . $publisherWebsite->domain);
    }

    public function reject(Request $request, PublisherWebsite $publisherWebsite)
    {
        if (!$publisherWebsite->isPending()) {
            return back()->with('error', 'This website has already been reviewed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $publisherWebsite->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
        ]);

        return back()->with('success', 'Website rejected.');
    }
}
