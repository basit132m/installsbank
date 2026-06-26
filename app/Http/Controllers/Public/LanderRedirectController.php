<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LanderHop;
use App\Models\LanderSetting;
use App\Models\TrackingDomain;

class LanderRedirectController extends Controller
{
    public function redirect(string $code)
    {
        $hop = LanderHop::with('domain')->where('code', $code)->first();

        if (!$hop) {
            abort(404);
        }

        // Find the next hop in the chain
        $nextHop = LanderHop::with('domain')
            ->where('position', '>', $hop->position)
            ->orderBy('position')
            ->first();

        if ($nextHop && $nextHop->domain && $nextHop->domain->is_active) {
            return redirect()->away(
                $nextHop->domain->base_url . '/go/' . $nextHop->code,
                302
            );
        }

        // Last hop — redirect to active lander domain
        $settings = LanderSetting::current();

        $activeDomain = TrackingDomain::where('id', $settings->active_lander_domain_id)
            ->where('is_active', true)
            ->first();

        if (!$activeDomain) {
            abort(404);
        }

        return redirect()->away($activeDomain->base_url . '/download', 302);
    }
}
