<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPreset;
use Illuminate\Http\Request;

class AdPresetController extends Controller
{
    public function index()
    {
        $presets = AdPreset::latest()->get();
        return view('admin.ad-presets.index', compact('presets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'button_text' => 'required|string|max:50',
            'button_color' => 'required|string|max:20',
            'button_text_color' => 'required|string|max:20',
            'button_size' => 'required|in:small,medium,large',
            'button_style' => 'required|in:rounded,square,pill',
            'custom_css' => 'nullable|string',
            'show_icon' => 'boolean',
            'icon_type' => 'nullable|string|max:30',
        ]);
        AdPreset::create($data);
        return back()->with('success', 'Preset created.');
    }

    public function update(Request $request, AdPreset $adPreset)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'button_text' => 'required|string|max:50',
            'button_color' => 'required|string|max:20',
            'button_text_color' => 'required|string|max:20',
            'button_size' => 'required|in:small,medium,large',
            'button_style' => 'required|in:rounded,square,pill',
            'is_active' => 'boolean',
        ]);
        $adPreset->update($data);
        return back()->with('success', 'Preset updated.');
    }

    public function destroy(AdPreset $adPreset)
    {
        $adPreset->delete();
        return back()->with('success', 'Preset deleted.');
    }
}
