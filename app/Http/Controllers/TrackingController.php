<?php

namespace App\Http\Controllers;

use App\Models\TrackingLink;
use App\Services\ClickTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;

class TrackingController extends Controller
{
    public function __construct(private ClickTrackingService $trackingService) {}

    public function track(string $code, Request $request)
    {
        $link = TrackingLink::where('unique_code', $code)->where('is_active', true)->first();

        if (!$link) {
            abort(404);
        }

        // Detect OS for device-specific redirect
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent() ?? '');
        $os = $agent->platform() ?: 'Unknown';

        // If link is tied to a campaign that is no longer active, use fallback URL
        $link->load('campaign');
        if ($link->campaign && !$link->campaign->isActive() && $link->campaign->fallback_url) {
            return redirect()->away($link->campaign->fallback_url);
        }

        $redirectUrl = $link->resolveUrlForOs($os);

        // Rate limiter: same IP hitting the same link within 30 seconds is noise
        // (page auto-reload, prefetch, redirect bounce, back-button). Silently redirect.
        $ip = $request->ip();
        $rateLimitKey = "rl_{$link->id}_{$ip}";
        if (!Cache::add($rateLimitKey, 1, 30)) {
            return redirect()->away($redirectUrl);
        }

        // Process click — pass the resolved destination so we can report where
        // Windows visitors were actually sent (timer-slot stats)
        try {
            $this->trackingService->processClick($link, $request, $redirectUrl);
        } catch (\Exception $e) {
            \Log::error('Click tracking error: ' . $e->getMessage());
        }

        return redirect()->away($redirectUrl);
    }
}
