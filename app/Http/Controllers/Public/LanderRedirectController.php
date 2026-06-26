<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LanderSetting;
use App\Models\TrackingDomain;

class LanderRedirectController extends Controller
{
    public function redirect(string $code)
    {
        $settings = LanderSetting::where('redirect_code', $code)->first();

        if (!$settings || !$settings->active_lander_domain_id) {
            abort(404);
        }

        $domain = TrackingDomain::where('id', $settings->active_lander_domain_id)
            ->where('is_active', true)
            ->first();

        if (!$domain) {
            abort(404);
        }

        return redirect()->away($domain->base_url . '/download', 302);
    }
}
