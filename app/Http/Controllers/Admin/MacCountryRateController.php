<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\DailyEarning;
use App\Models\MacCountryRate;
use Illuminate\Http\Request;

class MacCountryRateController extends Controller
{
    public function index()
    {
        $rates = MacCountryRate::orderByDesc('needs_rate_update')->orderBy('country_name')->paginate(50);
        $unratedCount = MacCountryRate::where('needs_rate_update', true)->count();
        return view('admin.mac-rates.index', compact('rates', 'unratedCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_code'       => 'required|string|max:5|unique:mac_country_rates',
            'country_name'       => 'required|string|max:100',
            'mac_rate_per_click' => 'required|numeric|min:0',
        ]);

        MacCountryRate::create($data);
        return back()->with('success', 'Mac country rate added.');
    }

    public function update(Request $request, MacCountryRate $macRate)
    {
        $data = $request->validate([
            'mac_rate_per_click' => 'required|numeric|min:0',
            'is_active'          => 'boolean',
        ]);

        $wasUnrated = $macRate->needs_rate_update;
        $newRate    = (float) $data['mac_rate_per_click'];

        $macRate->update([
            'mac_rate_per_click' => $newRate,
            'is_active'          => $request->boolean('is_active', true),
            'needs_rate_update'  => false,
        ]);

        $retroCount = 0;
        if ($wasUnrated && $newRate > 0) {
            $retroCount = $this->recalculatePastEarnings($macRate->country_code, $newRate);
        }

        $msg = 'Mac rate set for ' . $macRate->country_name . '.';
        if ($retroCount > 0) {
            $msg .= " {$retroCount} past Mac click(s) have been retroactively credited.";
        }

        return back()->with('success', $msg);
    }

    private function recalculatePastEarnings(string $countryCode, float $rate): int
    {
        $clicks = Click::where('country_code', $countryCode)
            ->where('is_counted', true)
            ->where('is_mac', true)
            ->where('click_value', 0)
            ->get();

        if ($clicks->isEmpty()) return 0;

        foreach ($clicks as $click) {
            $click->update(['click_value' => $rate]);

            $date    = $click->created_at->toDateString();
            $earning = DailyEarning::firstOrCreate(
                ['user_id' => $click->user_id, 'date' => $date],
                ['total_raw_clicks' => 0, 'mac_clicks' => 0, 'mac_clicks_divided' => 0, 'valid_clicks' => 0, 'earnings' => 0]
            );

            $breakdown                           = $earning->country_breakdown ?? [];
            $breakdown[$countryCode]             = $breakdown[$countryCode] ?? ['clicks' => 0, 'earnings' => 0];
            $breakdown[$countryCode]['earnings'] += $rate;
            $earning->country_breakdown          = $breakdown;
            $earning->earnings                   += $rate;
            $earning->save();

            $earning->user?->publisherProfile?->increment('balance', $rate);
            $earning->user?->publisherProfile?->increment('total_earnings', $rate);
        }

        return $clicks->count();
    }

    public function destroy(MacCountryRate $macRate)
    {
        $macRate->delete();
        return back()->with('success', 'Mac rate deleted.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate(['rates' => 'required|array']);

        $count  = 0;
        $retros = [];
        foreach ($request->input('rates', []) as $id => $row) {
            $mr = MacCountryRate::find((int) $id);
            if (!$mr) continue;

            $wasUnrated = $mr->needs_rate_update;
            $newRate    = (float) ($row['mac_rate_per_click'] ?? 0);

            $mr->update([
                'mac_rate_per_click' => $newRate,
                'is_active'          => array_key_exists('is_active', $row),
                'needs_rate_update'  => false,
            ]);

            if ($wasUnrated && $newRate > 0) {
                $retroCount = $this->recalculatePastEarnings($mr->country_code, $newRate);
                if ($retroCount > 0) {
                    $retros[] = "{$mr->country_name}: {$retroCount} click(s) credited";
                }
            }
            $count++;
        }

        $msg = "{$count} Mac click rates saved.";
        if ($retros) {
            $msg .= ' Retroactive credits: ' . implode(', ', $retros) . '.';
        }

        return back()->with('success', $msg);
    }
}
