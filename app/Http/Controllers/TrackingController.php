<?php

namespace App\Http\Controllers;

use App\Models\TrackingLink;
use App\Services\ClickTrackingService;
use Illuminate\Http\Request;
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
        $redirectUrl = $link->resolveUrlForOs($os);

        // Process click async-like (quick response)
        try {
            $this->trackingService->processClick($link, $request);
        } catch (\Exception $e) {
            \Log::error('Click tracking error: ' . $e->getMessage());
        }

        return redirect()->away($redirectUrl);
    }
}
