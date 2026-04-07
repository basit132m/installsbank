<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\InstallCountryRate;

class InstallRatesController extends Controller
{
    public function index()
    {
        $rates = InstallCountryRate::where('is_active', true)
            ->where('rate_usd', '>', 0)
            ->orderByDesc('rate_usd')
            ->orderBy('country_name')
            ->get();

        $topRate = $rates->max('rate_usd');

        return view('public.install-rates', compact('rates', 'topRate'));
    }
}
