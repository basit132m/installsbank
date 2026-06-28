<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MacCountryRate;

class MacRatesController extends Controller
{
    public function index()
    {
        $clickRates = MacCountryRate::where('is_active', true)
            ->where('needs_rate_update', false)
            ->where('mac_rate_per_click', '>', 0)
            ->orderByDesc('mac_rate_per_click')
            ->orderBy('country_name')
            ->get();

        $topClickRate = $clickRates->max('mac_rate_per_click');

        return view('public.mac-rates', compact('clickRates', 'topClickRate'));
    }
}
