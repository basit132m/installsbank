<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvertiserCountryRate;
use Illuminate\Http\Request;

class AdvertiserRateController extends Controller
{
    public function index()
    {
        $rates = AdvertiserCountryRate::orderBy('country_name')->get();
        return view('admin.advertiser-rates.index', compact('rates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_code' => 'required|string|size:2|unique:advertiser_country_rates,country_code',
            'country_name' => 'required|string|max:100',
            'rate_usd'     => 'required|numeric|min:0.000001',
        ]);

        AdvertiserCountryRate::create($data);

        return back()->with('success', 'Country rate added.');
    }

    public function update(Request $request, AdvertiserCountryRate $advertiserRate)
    {
        $data = $request->validate([
            'rate_usd'  => 'required|numeric|min:0.000001',
            'is_active' => 'boolean',
        ]);

        $advertiserRate->update([
            'rate_usd'  => $data['rate_usd'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Rate updated.');
    }

    public function destroy(AdvertiserCountryRate $advertiserRate)
    {
        $advertiserRate->delete();
        return back()->with('success', 'Rate deleted.');
    }

    /** Returns all active rates as JSON — used by admin campaign form to auto-fill. */
    public function json()
    {
        $rates = AdvertiserCountryRate::where('is_active', true)
            ->orderBy('country_name')
            ->get(['country_code', 'country_name', 'rate_usd']);

        return response()->json($rates);
    }
}
