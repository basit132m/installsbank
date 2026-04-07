<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallDayRatio;
use Illuminate\Http\Request;

class InstallSettingsController extends Controller
{
    public function index()
    {
        // Ensure all 7 days exist
        for ($i = 0; $i <= 6; $i++) {
            InstallDayRatio::firstOrCreate(
                ['weekday' => $i],
                ['ratio'   => 30]
            );
        }

        $ratios = InstallDayRatio::orderBy('weekday')->get();

        return view('admin.install-settings.index', compact('ratios'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ratios'   => 'required|array|size:7',
            'ratios.*' => 'required|integer|min:1|max:1000',
        ]);

        foreach ($request->ratios as $weekday => $ratio) {
            InstallDayRatio::where('weekday', $weekday)->update(['ratio' => $ratio]);
        }

        return back()->with('success', 'Clicks-per-install ratios updated.');
    }
}
