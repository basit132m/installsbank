<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Click;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->advertiserProfile;

        // Campaign stats
        $campaigns = Campaign::where('user_id', $user->id)->latest()->get();
        $activeCampaign = $campaigns->where('status', 'active')->first();

        // Live clicks today across all active campaigns
        $campaignIds = $campaigns->pluck('id');
        $todayClicks = 0;
        $totalDelivered = $campaigns->sum('delivered_clicks');
        $totalSpent = (float) ($profile->total_spent ?? 0);

        // Today's clicks via tracking links belonging to user's campaigns
        if ($campaignIds->isNotEmpty()) {
            $linkIds = \App\Models\TrackingLink::whereIn('campaign_id', $campaignIds)->pluck('id');
            if ($linkIds->isNotEmpty()) {
                $todayClicks = Click::whereIn('tracking_link_id', $linkIds)
                    ->whereDate('created_at', today())
                    ->where('is_counted', true)
                    ->count();
            }
        }

        // Country breakdown for active campaign
        $countryBreakdown = null;
        $countryNames = collect();
        if ($activeCampaign && $activeCampaign->click_breakdown) {
            $countryBreakdown = collect($activeCampaign->click_breakdown)->sortByDesc(fn($v) => $v);
            $countryNames = \App\Models\CountryRate::whereIn('country_code', $countryBreakdown->keys()->toArray())
                ->pluck('country_name', 'country_code');
        }

        // OS breakdown for active campaign's links
        $osBreakdown = collect();
        if ($activeCampaign) {
            $linkIds = \App\Models\TrackingLink::where('campaign_id', $activeCampaign->id)->pluck('id');
            if ($linkIds->isNotEmpty()) {
                $osBreakdown = Click::whereIn('tracking_link_id', $linkIds)
                    ->where('is_counted', true)
                    ->selectRaw('os, COUNT(*) as clicks')
                    ->groupBy('os')
                    ->orderByDesc('clicks')
                    ->get()
                    ->mapWithKeys(fn($r) => [$r->os ?: 'Unknown' => $r->clicks]);
            }
        }

        return view('advertiser.dashboard', compact(
            'user', 'profile', 'campaigns', 'activeCampaign',
            'todayClicks', 'totalDelivered', 'totalSpent',
            'countryBreakdown', 'countryNames', 'osBreakdown'
        ));
    }
}
