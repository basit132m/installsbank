<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SoftwareCampaign;
use App\Models\SoftwareInstall;
use App\Services\GeoLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SoftwareDeliveryController extends Controller
{
    public function __construct(private GeoLocationService $geo) {}

    private const STUB_PATH = 'software-stub/stub.exe';

    /**
     * GET /app/dl/{code} — serve our stub with a short per-download token in
     * the filename. The stub reads its own filename to learn the token; the
     * token maps to this campaign in the DB (so the campaign code never has to
     * appear in the user-facing name). No bytes of any installer are modified.
     */
    public function download(string $code)
    {
        $campaign = SoftwareCampaign::where('code', $code)->where('enabled', true)->first();
        if (!$campaign || !$campaign->hasPayload()) {
            abort(404);
        }

        // Serve this campaign's branded (icon-baked) stub if one was uploaded,
        // otherwise fall back to the shared global stub.
        $stubRel = $campaign->hasBrandedStub() ? $campaign->brandedStubRelPath() : self::STUB_PATH;
        if (!Storage::disk('local')->exists($stubRel)) {
            abort(503, 'Installer temporarily unavailable.');
        }

        // Short, clean, globally-unique token.
        do {
            $token = Str::lower(Str::random(8));
        } while (SoftwareInstall::where('token', $token)->exists());

        // Record the download now (captures the real user IP + region).
        $ip  = request()->ip();
        $geo = $this->geo->lookup($ip);
        SoftwareInstall::create([
            'software_campaign_id' => $campaign->id,
            'token'                => $token,
            'ip_address'           => $ip,
            'country_code'         => $geo['country_code'] ?? null,
            'country_name'         => $geo['country_name'] ?? null,
        ]);

        // e.g. "Font-Manager-Setup_a1b2c3d4.exe"
        $name = $campaign->downloadBaseName() . '_' . $token . '.exe';

        return response()->download(Storage::disk('local')->path($stubRel), $name, [
            'Content-Type'  => 'application/octet-stream',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    /**
     * GET /app/p/{token} — the stub fetches the advertiser's real installer here.
     */
    public function payload(string $token)
    {
        $install  = $this->resolveInstall($token);
        $campaign = $install?->campaign;

        if (!$campaign || !$campaign->enabled || !$campaign->hasPayload()) {
            abort(404);
        }

        return response()->download(
            Storage::disk('local')->path($campaign->payload_path),
            $campaign->payload_original_name ?: 'setup.exe',
            ['Content-Type' => 'application/octet-stream', 'Cache-Control' => 'no-store, max-age=0']
        );
    }

    /**
     * GET|POST /app/hit — install postback from the stub.
     * Params: t=token, e=event(open|installed), optional os, osver, dev.
     */
    public function hit(Request $request)
    {
        $token = (string) $request->input('t', '');
        $event = $request->input('e') === 'installed' ? 'installed' : 'open';

        $install = $this->resolveInstall($token);
        if (!$install || !$install->campaign) {
            return response('ok', 200); // never reveal anything to the client
        }
        $campaign = $install->campaign;

        // Fill device/OS the first time the stub reports in.
        if (!$install->device_type) {
            $install->device_type = Str::limit((string) $request->input('dev', 'desktop'), 20, '');
        }
        if (!$install->os) {
            $install->os = Str::limit((string) $request->input('os', 'Windows'), 50, '');
        }
        if (!$install->os_version) {
            $install->os_version = Str::limit((string) $request->input('osver', ''), 50, '') ?: null;
        }

        $countCol = $campaign->countColumn(); // opened_at | installed_at

        if ($event === 'open' && !$install->opened_at) {
            $install->opened_at = now();
        }
        if ($event === 'installed' && !$install->installed_at) {
            $install->installed_at = now();
        }

        // Count the install once, when the mode's counting event first lands.
        $justCounted = empty($install->getOriginal($countCol)) && !empty($install->{$countCol});

        $install->save();

        if ($justCounted) {
            $campaign->increment('total_installs');
        }

        return response('ok', 200);
    }

    /** Resolve the download record (and its campaign) from the token. */
    private function resolveInstall(string $token): ?SoftwareInstall
    {
        if ($token === '') {
            return null;
        }
        return SoftwareInstall::with('campaign')
            ->where('token', Str::limit($token, 64, ''))
            ->first();
    }
}
