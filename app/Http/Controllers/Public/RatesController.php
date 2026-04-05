<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CountryRate;

class RatesController extends Controller
{
    public function index()
    {
        $rates = CountryRate::where('is_active', true)
            ->where('needs_rate_update', false)
            ->orderByDesc('rate_per_click')
            ->orderBy('country_name')
            ->get();

        $topRate = $rates->max('rate_per_click');

        return view('public.rates', compact('rates', 'topRate'));
    }
}
