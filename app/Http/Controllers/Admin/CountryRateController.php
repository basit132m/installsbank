<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CountryRate;
use Illuminate\Http\Request;

class CountryRateController extends Controller
{
    public function index()
    {
        // Unrated (needs_rate_update) float to top, then alphabetical
        $rates = CountryRate::orderByDesc('needs_rate_update')->orderBy('country_name')->paginate(50);
        $unratedCount = CountryRate::where('needs_rate_update', true)->count();
        return view('admin.rates.index', compact('rates', 'unratedCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_code' => 'required|string|max:5|unique:country_rates',
            'country_name' => 'required|string|max:100',
            'rate_per_click' => 'required|numeric|min:0',
        ]);

        CountryRate::create($data);
        return back()->with('success', 'Country rate added.');
    }

    public function update(Request $request, CountryRate $countryRate)
    {
        $data = $request->validate([
            'rate_per_click' => 'required|numeric|min:0',
            'is_active'      => 'boolean',
        ]);
        $countryRate->update([
            'rate_per_click'    => $data['rate_per_click'],
            'is_active'         => $request->boolean('is_active', true),
            'needs_rate_update' => false, // Clear flag when admin sets a rate
        ]);
        return back()->with('success', 'Rate updated for ' . $countryRate->country_name . '.');
    }

    public function destroy(CountryRate $countryRate)
    {
        $countryRate->delete();
        return back()->with('success', 'Rate deleted.');
    }

    public function bulkStore(Request $request)
    {
        $data = $request->validate(['rates' => 'required|array', 'rates.*.country_code' => 'required', 'rates.*.country_name' => 'required', 'rates.*.rate_per_click' => 'required|numeric']);
        foreach ($data['rates'] as $rate) {
            CountryRate::updateOrCreate(['country_code' => $rate['country_code']], ['country_name' => $rate['country_name'], 'rate_per_click' => $rate['rate_per_click'], 'is_active' => true]);
        }
        return back()->with('success', count($data['rates']) . ' rates saved.');
    }
}
