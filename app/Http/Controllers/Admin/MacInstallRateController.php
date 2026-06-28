<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallCountryRate;
use App\Models\MacInstallCountryRate;
use Illuminate\Http\Request;

class MacInstallRateController extends Controller
{
    public function index()
    {
        $rates       = MacInstallCountryRate::orderByRaw('mac_rate_usd = 0 DESC, country_name ASC')->get();
        $noRateCount = $rates->where('mac_rate_usd', 0)->count();

        // Countries in InstallCountryRate not yet in mac_install_country_rates
        $existing = $rates->pluck('country_code')->map('strtolower');
        $unsynced = InstallCountryRate::whereNotIn('country_code', $existing)
            ->orderBy('country_name')
            ->get(['country_code', 'country_name']);

        return view('admin.mac-install-rates.index', compact('rates', 'unsynced', 'noRateCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_code' => 'required|string|max:5|unique:mac_install_country_rates,country_code',
            'country_name' => 'required|string|max:100',
            'mac_rate_usd' => 'required|numeric|min:0',
        ]);

        MacInstallCountryRate::create($data);
        return back()->with('success', 'Mac install country rate added.');
    }

    public function update(Request $request, MacInstallCountryRate $macInstallRate)
    {
        $data = $request->validate([
            'mac_rate_usd' => 'required|numeric|min:0',
            'is_active'    => 'boolean',
        ]);

        $macInstallRate->update([
            'mac_rate_usd' => $data['mac_rate_usd'],
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Mac install rate updated.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate(['rates' => 'required|array']);

        $count = 0;
        foreach ($request->input('rates', []) as $id => $row) {
            $rate = MacInstallCountryRate::find((int) $id);
            if (!$rate) continue;
            $rate->update([
                'mac_rate_usd' => (float) ($row['mac_rate_usd'] ?? 0),
                'is_active'    => array_key_exists('is_active', $row),
            ]);
            $count++;
        }

        return back()->with('success', "{$count} Mac install rates saved.");
    }

    public function destroy(MacInstallCountryRate $macInstallRate)
    {
        $macInstallRate->delete();
        return back()->with('success', 'Mac install rate deleted.');
    }

    public function syncFromTracked()
    {
        $existing = MacInstallCountryRate::pluck('country_code')->map('strtolower');
        $toImport = InstallCountryRate::whereNotIn('country_code', $existing)->get();

        foreach ($toImport as $ir) {
            MacInstallCountryRate::create([
                'country_code' => strtolower($ir->country_code),
                'country_name' => $ir->country_name,
                'mac_rate_usd' => 0,
                'is_active'    => true,
            ]);
        }

        return back()->with('success', "Imported {$toImport->count()} countries with \$0 Mac install rate. Set the rates before they take effect.");
    }
}
