<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\CountryRate;
use App\Models\DailyEarning;
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

        $wasUnrated = $countryRate->needs_rate_update;
        $newRate    = (float) $data['rate_per_click'];

        $countryRate->update([
            'rate_per_click'    => $newRate,
            'is_active'         => $request->boolean('is_active', true),
            'needs_rate_update' => false,
        ]);

        // Retroactively credit past clicks that earned $0 due to missing rate
        $retroCount = 0;
        if ($wasUnrated && $newRate > 0) {
            $retroCount = $this->recalculatePastEarnings($countryRate->country_code, $newRate);
        }

        $msg = 'Rate set for ' . $countryRate->country_name . '.';
        if ($retroCount > 0) {
            $msg .= " {$retroCount} past click(s) have been retroactively credited.";
        }

        return back()->with('success', $msg);
    }

    private function recalculatePastEarnings(string $countryCode, float $rate): int
    {
        // Find all counted Windows clicks from this country that earned $0 (no rate at the time)
        $clicks = Click::where('country_code', $countryCode)
            ->where('is_counted', true)
            ->where('is_windows', true)
            ->where('click_value', 0)
            ->get();

        if ($clicks->isEmpty()) return 0;

        foreach ($clicks as $click) {
            $click->update(['click_value' => $rate]);

            $date    = $click->created_at->toDateString();
            $earning = DailyEarning::firstOrCreate(
                ['user_id' => $click->user_id, 'date' => $date],
                ['total_raw_clicks' => 0, 'windows_clicks' => 0, 'windows_clicks_divided' => 0, 'valid_clicks' => 0, 'earnings' => 0]
            );

            // Update country breakdown in the daily earning JSON
            $breakdown                       = $earning->country_breakdown ?? [];
            $breakdown[$countryCode]         = $breakdown[$countryCode] ?? ['clicks' => 0, 'earnings' => 0];
            $breakdown[$countryCode]['earnings'] += $rate;
            $earning->country_breakdown      = $breakdown;
            $earning->earnings               += $rate;
            $earning->save();

            // Credit publisher balance
            $earning->user?->publisherProfile?->increment('balance', $rate);
            $earning->user?->publisherProfile?->increment('total_earnings', $rate);
        }

        return $clicks->count();
    }

    public function destroy(CountryRate $countryRate)
    {
        $countryRate->delete();
        return back()->with('success', 'Rate deleted.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate(['rates' => 'required|array']);

        $count  = 0;
        $retros = [];
        foreach ($request->input('rates', []) as $id => $row) {
            $cr = CountryRate::find((int)$id);
            if (!$cr) continue;

            $wasUnrated = $cr->needs_rate_update;
            $newRate    = (float)($row['rate_per_click'] ?? 0);

            $cr->update([
                'rate_per_click'    => $newRate,
                'is_active'         => array_key_exists('is_active', $row),
                'needs_rate_update' => false,
            ]);

            if ($wasUnrated && $newRate > 0) {
                $retroCount = $this->recalculatePastEarnings($cr->country_code, $newRate);
                if ($retroCount > 0) {
                    $retros[] = "{$cr->country_name}: {$retroCount} click(s) credited";
                }
            }
            $count++;
        }

        $msg = "{$count} click rates saved.";
        if ($retros) {
            $msg .= ' Retroactive credits: ' . implode(', ', $retros) . '.';
        }

        return back()->with('success', $msg);
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
