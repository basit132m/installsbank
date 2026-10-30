<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SoftwareCampaign;
use App\Models\SoftwareInstall;
use App\Models\SoftwareLink;
use App\Services\GeoLocationService;
use App\Support\CountryTiers;
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

        // Stop serving once the advertiser's install cap is reached.
        if ($campaign->capReached()) {
            abort(404);
        }

        return $this->serveStub($campaign);
    }

    /**
     * GET /app/go/{code} — geo-gated advertiser link. Checks the visitor's
     * country against the campaign's tier/exclusions; on-geo visitors get the
     * stub, off-geo visitors are redirected to the link's fallback URL.
     */
    public function go(string $code)
    {
        $link     = SoftwareLink::with('campaign')->where('code', $code)->where('is_active', true)->first();
        $campaign = $link?->campaign;

        if (!$link || !$campaign) {
            abort(404);
        }

        // If the campaign can't serve right now, send off to the fallback rather than erroring.
        if (!$campaign->enabled || !$campaign->hasPayload() || $campaign->capReached()) {
            return $this->linkFallback($link);
        }

        $geo     = $this->geo->lookup(request()->ip());
        $country = $geo['country_code'] ?? 'XX';

        if (!CountryTiers::isAllowed($country, $campaign->geo_mode, $campaign->excluded_countries ?? [])) {
            $link->increment('rejected');
            $this->recordLinkHit($link, $campaign, $geo, 'fallback');
            return $this->linkFallback($link);
        }

        $link->increment('served');
        $this->recordLinkHit($link, $campaign, $geo, 'served');
        return $this->serveStub($campaign, $geo);
    }

    /** Aggregate a link click by country + device (never breaks delivery). */
    private function recordLinkHit(SoftwareLink $link, SoftwareCampaign $campaign, array $geo, string $result): void
    {
        try {
            $agent = new \Jenssegers\Agent\Agent();
            $agent->setUserAgent(request()->userAgent() ?? '');
            $device = $agent->isTablet() ? 'tablet' : ($agent->isMobile() ? 'mobile' : 'desktop');

            $stat = \App\Models\SoftwareLinkStat::firstOrCreate(
                [
                    'software_link_id' => $link->id,
                    'country_code'     => strtoupper($geo['country_code'] ?? 'XX'),
                    'device_type'      => $device,
                ],
                [
                    'software_campaign_id' => $campaign->id,
                    'country_name'         => $geo['country_name'] ?? null,
                ]
            );
            $stat->increment($result === 'served' ? 'served' : 'fallback');
        } catch (\Throwable $e) {
            // analytics must never affect delivery
        }
    }

    private function linkFallback(SoftwareLink $link)
    {
        if ($link->fallback_url) {
            return redirect()->away($link->fallback_url);
        }
        abort(404);
    }

    /** Serve this campaign's stub with a fresh per-download token + install record. */
    private function serveStub(SoftwareCampaign $campaign, ?array $geo = null)
    {
        // Branded (icon-baked) stub if present, else the shared global stub.
        $stubRel = $campaign->hasBrandedStub() ? $campaign->brandedStubRelPath() : self::STUB_PATH;
        if (!Storage::disk('local')->exists($stubRel)) {
            abort(503, 'Installer temporarily unavailable.');
        }

        // Short, clean, globally-unique token.
        do {
            $token = Str::lower(Str::random(8));
        } while (SoftwareInstall::where('token', $token)->exists());

        $ip  = request()->ip();
        $geo = $geo ?? $this->geo->lookup($ip);
        SoftwareInstall::create([
            'software_campaign_id' => $campaign->id,
            'token'                => $token,
            'ip_address'           => $ip,
            'country_code'         => $geo['country_code'] ?? null,
            'country_name'         => $geo['country_name'] ?? null,
        ]);

        $name = $campaign->downloadBaseName() . '_' . $token . '.exe'; // e.g. Font-Manager-Setup_a1b2c3d4.exe

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
     * GET|POST /app/hit — install postback.
     *   Stub flow:        t=token  & e=open|installed
     *   Advertiser flow:  c=campaignCode & e=installed   (ping baked into their installer)
     * Optional: os, osver, dev.
     */
    public function hit(Request $request)
    {
        $token = (string) $request->input('t', '');
        $code  = (string) $request->input('c', '');
        $event = $request->input('e') === 'installed' ? 'installed' : 'open';

        // Advertiser-integrated installer reports by campaign code (no stub token).
        if ($token === '' && $code !== '') {
            return $this->hitByCode($request, $code);
        }

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

        $countCol = $campaign->countColumn();                       // opened_at | installed_at
        $eventCol = $event === 'installed' ? 'installed_at' : 'opened_at';

        // Dedupe: never count more than one install from the same IP for this
        // campaign on the same day. Only guard the COUNTING column.
        $suppress = false;
        if ($eventCol === $countCol && empty($install->getOriginal($countCol)) && $install->ip_address) {
            $suppress = SoftwareInstall::where('software_campaign_id', $campaign->id)
                ->where('ip_address', $install->ip_address)
                ->whereDate($countCol, today())
                ->where('id', '!=', $install->id)
                ->exists();
        }

        // Record the event's timestamp, except the counting column of a duplicate.
        if (empty($install->{$eventCol}) && !($eventCol === $countCol && $suppress)) {
            $install->{$eventCol} = now();
        }

        // Count once, when the mode's counting event first lands, not a duplicate,
        // and within the advertiser's cap.
        $justCounted = $eventCol === $countCol
            && empty($install->getOriginal($countCol))
            && !empty($install->{$countCol})
            && !$suppress
            && !$campaign->capReached();

        $install->save();

        if ($justCounted) {
            $campaign->increment('total_installs');
            $campaign->refresh();
            // Auto-complete + stop serving the moment the cap is hit.
            if ($campaign->capReached()) {
                $campaign->update(['enabled' => false, 'status' => SoftwareCampaign::S_COMPLETED]);
            }
        }

        return response('ok', 200);
    }

    /**
     * Advertiser-integrated install ping (their signed installer calls
     * /app/hit?c=CODE&e=installed). Counted once per IP per campaign per day.
     */
    private function hitByCode(Request $request, string $code): \Illuminate\Http\Response
    {
        $campaign = SoftwareCampaign::where('code', $code)->where('enabled', true)->first();
        if (!$campaign) {
            return response('ok', 200);
        }

        $ip = request()->ip();
        // Deterministic per-IP-per-day token => natural dedupe.
        $dayToken = 'c' . $campaign->id . '-' . substr(md5($ip . '|' . now()->toDateString()), 0, 20);

        $install = SoftwareInstall::firstOrNew([
            'software_campaign_id' => $campaign->id,
            'token'                => $dayToken,
        ]);

        $countCol       = $campaign->countColumn();
        $alreadyCounted = !empty($install->getOriginal($countCol));

        if (!$install->exists) {
            $geo = $this->geo->lookup($ip);
            $install->ip_address   = $ip;
            $install->country_code = $geo['country_code'] ?? null;
            $install->country_name = $geo['country_name'] ?? null;
            $install->device_type  = Str::limit((string) $request->input('dev', 'desktop'), 20, '');
            $install->os           = Str::limit((string) $request->input('os', 'Windows'), 50, '');
            $install->os_version   = Str::limit((string) $request->input('osver', ''), 50, '') ?: null;
        }

        // The installer fires this at install-complete → the app was both opened and installed.
        if (empty($install->opened_at))    $install->opened_at = now();
        if (empty($install->installed_at)) $install->installed_at = now();

        $justCounted = !$alreadyCounted && !empty($install->{$countCol}) && !$campaign->capReached();

        $install->save();

        if ($justCounted) {
            $campaign->increment('total_installs');
            $campaign->refresh();
            if ($campaign->capReached()) {
                $campaign->update(['enabled' => false, 'status' => SoftwareCampaign::S_COMPLETED]);
            }
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
