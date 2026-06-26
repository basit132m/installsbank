<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LanderHop;
use App\Models\LanderSetting;
use App\Models\TrackingDomain;
use Illuminate\Http\Request;

class LanderRedirectController extends Controller
{
    public function redirect(Request $request, string $code)
    {
        $hop = LanderHop::with('domain')->where('code', $code)->first();

        if (!$hop) {
            abort(404);
        }

        // Determine the source page URL.
        // Priority: ?src= (user-embedded full URL) > existing ?ref= (carried from prev hop) > Referer header.
        // Note: browsers send only the origin for cross-origin Referer by default,
        // so ?src= is the only way to reliably capture the specific page URL.
        $ref = $request->query('src')
            ?: $request->query('ref')
            ?: $request->header('Referer', '');

        // Find the next hop in the chain
        $nextHop = LanderHop::with('domain')
            ->where('position', '>', $hop->position)
            ->orderBy('position')
            ->first();

        if ($nextHop && $nextHop->domain && $nextHop->domain->is_active) {
            $url = $nextHop->domain->base_url . '/go/' . $nextHop->code;
            if ($ref) {
                $url .= '?' . http_build_query(['ref' => $ref]);
            }
            return redirect()->away($url, 302);
        }

        // Last hop — redirect to active lander domain
        $settings = LanderSetting::current();

        $activeDomain = TrackingDomain::where('id', $settings->active_lander_domain_id)
            ->where('is_active', true)
            ->first();

        if (!$activeDomain) {
            abort(404);
        }

        $url = $activeDomain->base_url . '/download';
        if ($ref) {
            $url .= '?' . http_build_query(['ref' => $ref]);
        }

        return redirect()->away($url, 302);
    }
}
