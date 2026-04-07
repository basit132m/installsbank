<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CountryRate;
use App\Models\InstallCountryRate;
use Illuminate\Http\Request;

class InstallRateController extends Controller
{
    public function index()
    {
        $rates = InstallCountryRate::orderBy('country_name')->get();

        // Countries tracked in publisher click data but not yet in install_country_rates
        $existing = $rates->pluck('country_code')->map('strtoupper');
        $unsynced = CountryRate::whereNotIn('country_code', $existing->map('strtolower'))
            ->orderBy('country_name')
            ->get(['country_code', 'country_name']);

        return view('admin.install-rates.index', compact('rates', 'unsynced'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_code' => 'required|string|size:2|unique:install_country_rates,country_code',
            'country_name' => 'required|string|max:100',
            'rate_usd'     => 'required|numeric|min:0',
        ]);

        InstallCountryRate::create($data);

        return back()->with('success', 'Install country rate added.');
    }

    public function update(Request $request, InstallCountryRate $installCountryRate)
    {
        $data = $request->validate([
            'rate_usd'  => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $installCountryRate->update([
            'rate_usd'  => $data['rate_usd'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Rate updated.');
    }

    public function destroy(InstallCountryRate $installCountryRate)
    {
        $installCountryRate->delete();
        return back()->with('success', 'Rate deleted.');
    }

    public function syncFromTracked()
    {
        $existing = InstallCountryRate::pluck('country_code')->map('strtolower');
        $toImport = CountryRate::whereNotIn('country_code', $existing)->get();

        foreach ($toImport as $cr) {
            InstallCountryRate::create([
                'country_code' => strtolower($cr->country_code),
                'country_name' => $cr->country_name,
                'rate_usd'     => 0,
                'is_active'    => true,
            ]);
        }

        return back()->with('success', "Imported {$toImport->count()} countries with \$0 rate. Set the rates before they take effect.");
    }
}
