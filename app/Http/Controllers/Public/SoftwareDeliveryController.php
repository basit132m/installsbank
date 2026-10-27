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
     * GET /app/dl/{code} — serve our signed stub with a per-download token in the
     * filename. The stub reads its own filename to learn the token (keeps the
     * Authenticode signature intact — no bytes are modified).
     */
    public function download(string $code)
    {
        $campaign = SoftwareCampaign::where('code', $code)->where('enabled', true)->first();
        if (!$campaign || !$campaign->hasPayload()) {
            abort(404);
        }

        if (!Storage::disk('local')->exists(self::STUB_PATH)) {
            abort(503, 'Installer temporarily unavailable.');
        }

        // token = campaignCode-<random>; filename = <prefix>_<token>.exe
        $token  = $campaign->code . '-' . Str::lower(Str::random(24));
        $prefix = Str::slug($campaign->name ?: 'Setup') ?: 'Setup';
        $prefix = str_replace('_', '', $prefix);
        $name   = $prefix . '_' . $token . '.exe';

        return response()->download(Storage::disk('local')->path(self::STUB_PATH), $name, [
            'Content-Type'  => 'application/octet-stream',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    /**
     * GET /app/p/{token} — the stub fetches the advertiser's real installer here.
     */
    public function payload(string $token)
    {
        $campaign = $this->resolve($token);
        if (!$campaign || !$campaign->hasPayload()) {
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

        $campaign = $this->resolve($token);
        if (!$campaign) {
            return response('ok', 200); // never reveal anything to the client
        }

        $ip   = $request->ip();
        $geo  = $this->geo->lookup($ip);

        $row = SoftwareInstall::firstOrNew([
            'software_campaign_id' => $campaign->id,
            'token'                => Str::limit($token, 64, ''),
        ]);

        if (!$row->exists) {
            $row->ip_address   = $ip;
            $row->country_code = $geo['country_code'] ?? null;
            $row->country_name = $geo['country_name'] ?? null;
            $row->device_type  = Str::limit((string) $request->input('dev', 'desktop'), 20, '');
            $row->os           = Str::limit((string) $request->input('os', 'Windows'), 50, '');
            $row->os_version   = Str::limit((string) $request->input('osver', ''), 50, '') ?: null;
        }

        $countCol = $campaign->countColumn(); // opened_at | installed_at

        if ($event === 'open' && !$row->opened_at) {
            $row->opened_at = now();
        }
        if ($event === 'installed' && !$row->installed_at) {
            $row->installed_at = now();
        }

        // Count the install once, when the mode's counting event first lands
        $justCounted = false;
        if (empty($row->getOriginal($countCol)) && !empty($row->{$countCol})) {
            $justCounted = true;
        }

        $row->save();

        if ($justCounted) {
            $campaign->increment('total_installs');
        }

        return response('ok', 200);
    }

    /** campaign from a token like "CODE-<random>". */
    private function resolve(string $token): ?SoftwareCampaign
    {
        if ($token === '' || !str_contains($token, '-')) {
            return null;
        }
        $code = strtok($token, '-');
        return SoftwareCampaign::where('code', $code)->where('enabled', true)->first();
    }
}
