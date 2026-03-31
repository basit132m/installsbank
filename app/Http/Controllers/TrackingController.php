<?php

namespace App\Http\Controllers;

use App\Models\TrackingLink;
use App\Services\ClickTrackingService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __construct(private ClickTrackingService $trackingService) {}

    public function track(string $code, Request $request)
    {
        $link = TrackingLink::where('unique_code', $code)->where('is_active', true)->first();

        if (!$link) {
            abort(404);
        }

        // Process click async-like (quick response)
        try {
            $this->trackingService->processClick($link, $request);
        } catch (\Exception $e) {
            \Log::error('Click tracking error: ' . $e->getMessage());
        }

        return redirect()->away($link->original_url);
    }
}
