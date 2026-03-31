<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\AdButton;
use App\Models\AdPreset;
use App\Models\TrackingLink;

class AdCodeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $trackingLinks = TrackingLink::where('user_id', $user->id)->where('is_active', true)->get();
        $presets = AdPreset::where('is_active', true)->get();
        $adButtons = AdButton::where('user_id', $user->id)->with(['preset', 'trackingLink'])->get();
        $hasContract = $user->activeContract !== null;

        return view('publisher.adcode', compact('trackingLinks', 'presets', 'adButtons', 'hasContract'));
    }

    public function selectPreset(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'tracking_link_id' => 'required|exists:tracking_links,id',
            'ad_preset_id' => 'required|exists:ad_presets,id',
            'custom_text' => 'nullable|string|max:50',
        ]);

        // Verify the tracking link belongs to this publisher
        $link = TrackingLink::where('id', $data['tracking_link_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        AdButton::updateOrCreate(
            ['user_id' => auth()->id(), 'tracking_link_id' => $link->id],
            ['ad_preset_id' => $data['ad_preset_id'], 'custom_text' => $data['custom_text'] ?? null, 'is_active' => true]
        );

        return back()->with('success', 'Ad button configured. Copy the embed code below.');
    }
}
