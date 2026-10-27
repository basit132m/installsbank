<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\SoftwareCampaign;
use App\Models\SoftwareInstall;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $profile = $user->advertiserProfile;

        $campaigns = SoftwareCampaign::where('user_id', $user->id)->latest()->get();

        $campaignIds = $campaigns->pluck('id');

        // Installs today across all campaigns (by each campaign's counting column).
        $installsToday = 0;
        $totalInstalls = (int) $campaigns->sum('total_installs');
        $totalTarget   = (int) $campaigns->sum('max_installs');
        $liveCount     = $campaigns->where('status', SoftwareCampaign::S_LIVE)->count();

        if ($campaignIds->isNotEmpty()) {
            // opened_at today for "open"-mode campaigns + installed_at today for "install"-mode.
            $openIds    = $campaigns->where('install_mode', 'open')->pluck('id');
            $installIds = $campaigns->where('install_mode', '!=', 'open')->pluck('id');

            if ($openIds->isNotEmpty()) {
                $installsToday += SoftwareInstall::whereIn('software_campaign_id', $openIds)
                    ->whereDate('opened_at', today())->count();
            }
            if ($installIds->isNotEmpty()) {
                $installsToday += SoftwareInstall::whereIn('software_campaign_id', $installIds)
                    ->whereDate('installed_at', today())->count();
            }
        }

        // The most relevant campaign to feature (first live, else newest).
        $activeCampaign = $campaigns->firstWhere('status', SoftwareCampaign::S_LIVE) ?? $campaigns->first();

        $countryBreakdown = collect();
        $deviceBreakdown  = collect();
        if ($activeCampaign) {
            $cc = $activeCampaign->countColumn();
            $countryBreakdown = $activeCampaign->installs()->whereNotNull($cc)
                ->selectRaw('country_name, COUNT(*) as installs')
                ->groupBy('country_name')->orderByDesc('installs')->limit(8)->get();
            $deviceBreakdown = $activeCampaign->installs()->whereNotNull($cc)
                ->selectRaw('device_type, COUNT(*) as installs')
                ->groupBy('device_type')->orderByDesc('installs')->get();
        }

        return view('advertiser.dashboard', compact(
            'user', 'profile', 'campaigns', 'activeCampaign',
            'installsToday', 'totalInstalls', 'totalTarget', 'liveCount',
            'countryBreakdown', 'deviceBreakdown'
        ));
    }
}
