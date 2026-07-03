<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\PublisherWebsite;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $websites = PublisherWebsite::with('trackingLink.trackingDomain')
            ->where('user_id', $user->id)->latest()->get();

        // Divider-applied all-time valid clicks per link — never show the raw
        // unique_clicks counter to the reseller.
        $divider         = $user->clickDivider;
        $dividerValue    = ($divider && $divider->is_enabled)          ? max(1, (float)$divider->divider_value)     : 1;
        $macDividerValue = ($divider && $divider->mac_divider_enabled) ? max(1, (float)$divider->mac_divider_value) : 1;

        $linkIds = $websites->pluck('tracking_link_id')->filter()->all();

        $validByLink = [];
        if (!empty($linkIds)) {
            $rawPerLink = Click::whereIn('tracking_link_id', $linkIds)
                ->where('is_counted', true)
                ->selectRaw('tracking_link_id,
                    SUM(CASE WHEN is_windows = 1 THEN 1 ELSE 0 END) as w,
                    SUM(CASE WHEN is_mac = 1 THEN 1 ELSE 0 END) as m,
                    SUM(CASE WHEN is_windows = 0 AND is_mac = 0 THEN 1 ELSE 0 END) as o')
                ->groupBy('tracking_link_id')
                ->get();

            foreach ($rawPerLink as $r) {
                $validByLink[$r->tracking_link_id] =
                      (int) floor((int)$r->w / $dividerValue)
                    + (int) floor((int)$r->m / $macDividerValue)
                    + (int) $r->o;
            }
        }

        return view('reseller.websites.index', compact('user', 'websites', 'validByLink'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'website_url'     => 'required|url',
            'screenshots'     => 'required|array|min:1|max:4',
            'screenshots.*'   => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'website_url.required'  => 'Please enter your website URL.',
            'website_url.url'       => 'Please enter a valid URL (e.g. https://yoursite.com).',
            'screenshots.required'  => 'Please upload at least 1 screenshot of your statistics.',
            'screenshots.*.image'   => 'Each file must be an image.',
            'screenshots.*.mimes'   => 'Screenshots must be JPG, PNG, or WebP.',
            'screenshots.*.max'     => 'Each screenshot must be under 5MB.',
        ]);

        $user   = auth()->user();
        $url    = rtrim($request->website_url, '/');
        $domain = strtolower(preg_replace('/^www\./', '', parse_url($url, PHP_URL_HOST) ?? $url));

        $exists = PublisherWebsite::where('user_id', $user->id)
            ->where('domain', $domain)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['website_url' => 'This website has already been added or is pending approval.']);
        }

        $paths = [];
        foreach ($request->file('screenshots', []) as $file) {
            $paths[] = $file->store('website-screenshots', 'public');
        }

        PublisherWebsite::create([
            'user_id'          => $user->id,
            'website_url'      => $url,
            'domain'           => $domain,
            'status'           => 'pending',
            'stat_screenshots' => $paths,
        ]);

        return back()->with('success', 'Website submitted for review. You will receive your ad code once our team approves it.');
    }
}
