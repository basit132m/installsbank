<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublisherWebsite;
use App\Models\TrackingDomain;
use App\Models\TrackingLink;
use Illuminate\Http\Request;

class PublisherWebsiteController extends Controller
{
    public function index(Request $request)
    {
        $query = PublisherWebsite::with(['user', 'trackingLink.trackingDomain'])->latest();

        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $websites     = $query->paginate(30);
        $pendingCount = PublisherWebsite::where('status', 'pending')->count();
        $domains      = TrackingDomain::where('is_active', true)->orderBy('domain')->get();

        return view('admin.publisher-websites.index', compact('websites', 'pendingCount', 'domains'));
    }

    public function approve(Request $request, PublisherWebsite $publisherWebsite)
    {
        if (!$publisherWebsite->isPending()) {
            return back()->with('error', 'This website has already been reviewed.');
        }

        $request->validate([
            'tracking_domain_id' => 'nullable|exists:tracking_domains,id',
            'url_format'         => ['nullable', \Illuminate\Validation\Rule::in(array_keys(TrackingLink::URL_FORMATS))],
            'original_url'       => 'required|url',
            'url_windows'        => 'nullable|url',
            'url_android'        => 'nullable|url',
            'url_mac'            => 'nullable|url',
            'url_other'          => 'nullable|url',
        ]);

        // Create a dedicated tracking link locked to this domain.
        // tracking_domain_id chooses which custom domain serves the tracking URL,
        // url_format chooses the URL structure (both null = default).
        $link = TrackingLink::create([
            'user_id'            => $publisherWebsite->user_id,
            'tracking_domain_id' => $request->tracking_domain_id ?: null,
            'url_format'         => $request->url_format ?: 'track',
            'name'               => 'Website: ' . $publisherWebsite->domain,
            'original_url'       => $request->original_url,
            'url_windows'        => $request->url_windows ?: null,
            'url_android'        => $request->url_android ?: null,
            'url_mac'            => $request->url_mac ?: null,
            'url_other'          => $request->url_other ?: null,
            'is_active'          => true,
            'allowed_domain'     => $publisherWebsite->domain,
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

    /**
     * Change the tracking domain used by an approved website's ad code.
     * Lets an admin move a created link onto a custom domain (or back to default).
     */
    public function changeDomain(Request $request, PublisherWebsite $publisherWebsite)
    {
        $request->validate([
            'tracking_domain_id' => 'nullable|exists:tracking_domains,id',
            'url_format'         => ['nullable', \Illuminate\Validation\Rule::in(array_keys(TrackingLink::URL_FORMATS))],
        ]);

        if (!$publisherWebsite->isApproved() || !$publisherWebsite->trackingLink) {
            return back()->with('error', 'This website has no ad code to update.');
        }

        $publisherWebsite->trackingLink->update([
            'tracking_domain_id' => $request->tracking_domain_id ?: null,
            'url_format'         => $request->url_format ?: 'track',
        ]);

        $newDomain = $publisherWebsite->trackingLink->trackingDomain?->domain ?? 'default (installsbank.com)';

        return back()->with('success', "Ad code URL for {$publisherWebsite->domain} updated (domain: {$newDomain}).");
    }

    /**
     * Pause / resume an approved website's ad code without deleting anything.
     * Paused links stop counting clicks immediately.
     */
    public function toggleSuspend(PublisherWebsite $publisherWebsite)
    {
        $link = $publisherWebsite->trackingLink;
        if (!$link) {
            return back()->with('error', 'This website has no ad code to suspend.');
        }

        $link->update(['is_active' => !$link->is_active]);

        return back()->with('success', $link->is_active
            ? "Ad code for {$publisherWebsite->domain} resumed."
            : "Ad code for {$publisherWebsite->domain} suspended — it will stop counting clicks.");
    }

    /**
     * Delete a website and its ad code. The tracking link is removed; because
     * clicks cascade-delete with their link, this also clears that link's click
     * log. The user's daily earnings totals (separate rows) are unaffected.
     * Use "Suspend" instead when you want to keep the history.
     */
    public function destroy(PublisherWebsite $publisherWebsite)
    {
        $domain = $publisherWebsite->domain;

        // Deleting the link cascade-deletes its clicks (FK cascadeOnDelete)
        $publisherWebsite->trackingLink?->delete();
        $publisherWebsite->delete();

        return back()->with('success', "Website {$domain} and its ad code have been deleted.");
    }
}
