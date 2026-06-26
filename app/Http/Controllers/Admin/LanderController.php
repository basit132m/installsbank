<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanderSetting;
use App\Models\MegaUrl;
use Illuminate\Http\Request;

class LanderController extends Controller
{
    public function index()
    {
        $settings = LanderSetting::current();
        $megaUrls = MegaUrl::orderByDesc('created_at')->get();

        return view('admin.lander.index', compact('settings', 'megaUrls'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mega_url'         => 'nullable|string|max:2000',
            'archive_password' => 'nullable|string|max:100',
            'color_scheme'     => 'required|in:dark-red,dark-blue,dark-green,dark-purple,neon-cyan,amber-gold',
            'page_title'       => 'required|string|max:200',
            'download_count'   => 'required|integer|min:0',
            'show_password'    => 'sometimes|boolean',
            'show_checks'      => 'sometimes|boolean',
        ]);

        $data['show_password'] = $request->boolean('show_password');
        $data['show_checks']   = $request->boolean('show_checks');

        LanderSetting::current()->update($data);

        return back()->with('success', 'Lander settings saved successfully.');
    }

    public function storeMegaUrl(Request $request)
    {
        $data = $request->validate([
            'nickname' => 'required|string|max:100',
            'url'      => 'required|string|max:2000',
            'notes'    => 'nullable|string|max:500',
        ]);

        MegaUrl::create($data);

        return back()->with('success', 'MEGA URL saved to vault.');
    }

    public function destroyMegaUrl(MegaUrl $megaUrl)
    {
        $megaUrl->delete();

        return back()->with('success', 'URL removed from vault.');
    }

    public function useMegaUrl(MegaUrl $megaUrl)
    {
        LanderSetting::current()->update(['mega_url' => $megaUrl->url]);

        return back()->with('success', "Now using \"{$megaUrl->nickname}\" as the active MEGA link.");
    }
}
