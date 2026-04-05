<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Click;
use App\Models\TrackingLink;

class LiveStatsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $campaignIds = Campaign::where('user_id', $user->id)->pluck('id');
        $linkIds = TrackingLink::whereIn('campaign_id', $campaignIds)->pluck('id');

        $clicksToday = 0;
        $clicksLastHour = 0;

        if ($linkIds->isNotEmpty()) {
            $clicksToday = Click::whereIn('tracking_link_id', $linkIds)
                ->whereDate('created_at', today())
                ->where('is_counted', true)
                ->count();

            $clicksLastHour = Click::whereIn('tracking_link_id', $linkIds)
                ->where('created_at', '>=', now()->subHour())
                ->where('is_counted', true)
                ->count();
        }

        // Active campaign progress
        $active = Campaign::where('user_id', $user->id)->where('status', 'active')->first();

        return response()->json([
            'clicks_today'      => $clicksToday,
            'clicks_last_hour'  => $clicksLastHour,
            'delivered_clicks'  => $active ? (int) $active->delivered_clicks : null,
            'target_clicks'     => $active ? (int) $active->target_clicks : null,
            'progress_percent'  => $active ? $active->progressPercent() : null,
        ]);
    }
}
