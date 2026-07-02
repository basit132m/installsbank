<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Click;
use App\Models\CountryRate;
use App\Models\DailyEarning;
use App\Models\MacCountryRate;
use App\Models\MacInstallCountryRate;
use App\Models\MacPublisherInstall;
use App\Models\TrackingLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Jenssegers\Agent\Agent;

class ClickTrackingService
{
    public function __construct(
        private GeoLocationService $geo,
        private FraudDetectionService $fraud
    ) {}

    /** Whether the clicks.redirect_url column exists (cached to avoid per-click schema hits). */
    private function clicksHaveRedirectUrl(): bool
    {
        return Cache::remember('schema_clicks_has_redirect_url', 3600, function () {
            try {
                return Schema::hasColumn('clicks', 'redirect_url');
            } catch (\Throwable $e) {
                return false;
            }
        });
    }

    public function processClick(TrackingLink $link, Request $request, ?string $redirectUrl = null): ?Click
    {
        $ip = $request->ip();
        $ua = $request->userAgent() ?? '';

        // Block no-referrer clicks only when a domain restriction is set on the link.
        // Without allowed_domain, referrer is not verifiable — blocking it would silently
        // drop legitimate traffic from publishers whose sites strip the Referer header
        // (e.g. WordPress buttons with rel="noreferrer").
        $rawReferrer = $request->header('referer') ?? '';
        if (empty($rawReferrer) && $link->allowed_domain) {
            return null;
        }

        // Blacklist check — bail out immediately, nothing is written to DB
        $referrerHost = strtolower(parse_url($rawReferrer, PHP_URL_HOST) ?? '');
        $referrerHost = preg_replace('/^www\./', '', $referrerHost);

        if ($referrerHost !== '') {
            $blacklist = Cache::remember('blacklisted_domains', 3600, function () {
                return \App\Models\BlacklistedDomain::pluck('domain')->all();
            });

            if (in_array($referrerHost, $blacklist, true)) {
                return null;
            }
        }

        $agent = new Agent();
        $agent->setUserAgent($ua);

        $os = $agent->platform() ?: 'Unknown';
        $osVersion = $agent->version($os) ?: null;
        $browser = $agent->browser() ?: 'Unknown';
        $deviceType = $agent->isDesktop() ? 'desktop' : ($agent->isTablet() ? 'tablet' : 'mobile');
        $isWindows = str_contains(strtolower($os), 'windows');
        $isMac     = !$isWindows && (bool) preg_match('/^(os[\s_]?x|mac)/i', $os);

        $geoData = $this->geo->lookup($ip);

        $clickData = [
            'tracking_link_id' => $link->id,
            'user_id'          => $link->user_id,
            'ip_address'       => $ip,
            'country_code'     => $geoData['country_code'],
            'country_name'     => $geoData['country_name'],
            'city'             => $geoData['city'],
            'os'               => $os,
            'os_version'       => $osVersion,
            'device_type'      => $deviceType,
            'browser'          => $browser,
            'user_agent'       => $ua,
            'fingerprint'      => $this->generateFingerprint($request),
            'referrer'         => $rawReferrer,
            'is_windows'       => $isWindows,
            'is_mac'           => $isMac,
            'headers'          => $request->headers->all(),
        ];

        // Only write redirect_url if the column exists — a pending migration must
        // never silently break click recording. Cached so it's one cheap check.
        if ($this->clicksHaveRedirectUrl()) {
            $clickData['redirect_url'] = $redirectUrl;
        }

        // Load profile
        $profile = $link->user?->publisherProfile;

        // Publisher IP exclusion — never count clicks from the publisher's own IP
        $excludedIps = $profile?->excluded_ips ?? [];
        if (!empty($excludedIps) && in_array($ip, $excludedIps, true)) {
            return null;
        }

        // Build per-publisher fraud settings for conditional checks
        $fraudSettings = [
            'country_mismatch'    => (bool) ($profile?->fraud_country_mismatch ?? false),
            'suspicious_referrer' => (bool) ($profile?->fraud_suspicious_referrer ?? false),
            'headless_browser'    => (bool) ($profile?->fraud_headless_browser ?? false),
            'allowed_countries'   => $profile?->allowed_countries ?: null,
        ];

        $fraudResult = $this->fraud->check($clickData, $fraudSettings);

        // Duplicate clicks — silently discard without writing to DB.
        // They are the same visitor hitting the link again; storing them pollutes
        // the click log and confuses publishers who see fraud rows they didn't cause.
        if ($fraudResult['fraud_reason'] === 'duplicate_ip') {
            return null;
        }

        // Domain restriction: only enforce when admin has it enabled for this publisher
        $enforceDomain = $profile?->enforce_domain_restriction ?? true;
        if (!$fraudResult['is_fraud'] && $link->allowed_domain && $enforceDomain) {
            $clickReferrerHost = strtolower(parse_url($clickData['referrer'] ?? '', PHP_URL_HOST) ?? '');
            $clickReferrerHost = preg_replace('/^www\./', '', $clickReferrerHost);
            $allowedHost       = strtolower(preg_replace('/^www\./', '', $link->allowed_domain));

            if ($clickReferrerHost !== $allowedHost) {
                $fraudResult['is_fraud']     = true;
                $fraudResult['fraud_reason'] = 'domain_mismatch';
            }
        }

        $clickValue = 0;
        $isCounted  = !$fraudResult['is_fraud'];

        // Fixed rate publishers are paid externally — no per-click earnings
        $isFixedRate = $profile?->isFixedRate() ?? false;

        $countryCode = $geoData['country_code'];

        // Discover new countries from counted Windows clicks and assign Windows rate
        if ($isCounted && $isWindows) {
            $countryRate = CountryRate::where('country_code', $countryCode)->first();

            if (!$countryRate && !in_array($countryCode, ['XX', 'Unknown', ''])) {
                $countryRate = CountryRate::create([
                    'country_code'      => $countryCode,
                    'country_name'      => $geoData['country_name'],
                    'rate_per_click'    => 0,
                    'is_active'         => false,
                    'needs_rate_update' => true,
                ]);
            }

            if (!$isFixedRate && $countryRate && $countryRate->is_active && !$countryRate->needs_rate_update) {
                $clickValue = $countryRate->rate_per_click;
            }
        }

        // Discover new countries from counted Mac clicks and assign Mac rate
        if ($isCounted && $isMac) {
            $macRate = MacCountryRate::where('country_code', $countryCode)->first();

            if (!$macRate && !in_array($countryCode, ['XX', 'Unknown', ''])) {
                $macRate = MacCountryRate::create([
                    'country_code'      => $countryCode,
                    'country_name'      => $geoData['country_name'],
                    'mac_rate_per_click'=> 0,
                    'is_active'         => false,
                    'needs_rate_update' => true,
                ]);
            }

            if (!$isFixedRate && $macRate && $macRate->is_active && !$macRate->needs_rate_update) {
                $clickValue = $macRate->mac_rate_per_click;
            }
        }

        $click = Click::create(array_merge($clickData, [
            'is_fraud'     => $fraudResult['is_fraud'],
            'fraud_reason' => $fraudResult['fraud_reason'],
            'is_vpn'       => $fraudResult['is_vpn'],
            'is_proxy'     => $fraudResult['is_proxy'],
            'is_counted'   => $isCounted,
            'click_value'  => $clickValue,
        ]));

        $link->increment('total_clicks');
        $link->update(['last_click_at' => now()]);
        if ($isCounted) {
            $link->increment('unique_clicks');
        } else {
            $link->increment('fraud_clicks');
        }

        if ($isCounted) {
            $this->updateDailyEarnings($link->user_id, $isWindows, $isMac, $os, $countryCode, $clickValue);
        }

        $this->maybeStartTestPeriod($link);
        $this->processInstallsIfApplicable($link, $geoData, $isCounted, $isWindows);
        $this->processMacInstallsIfApplicable($link, $geoData, $isCounted, $isMac);

        if ($isCounted && $link->campaign_id) {
            $this->updateCampaignStats($link->campaign_id, $geoData['country_code']);
        }

        return $click;
    }

    private function updateDailyEarnings(int $publisherId, bool $isWindows, bool $isMac, string $osName, string $countryCode, float $clickValue): void
    {
        $today   = now()->toDateString();
        $earning = DailyEarning::firstOrCreate(
            ['user_id' => $publisherId, 'date' => $today],
            [
                'total_raw_clicks'            => 0,
                'windows_clicks'              => 0,
                'windows_clicks_base_count'   => 0,
                'windows_clicks_base_divided' => 0,
                'windows_clicks_divided'      => 0,
                'mac_clicks'                  => 0,
                'mac_clicks_base_count'       => 0,
                'mac_clicks_base_divided'     => 0,
                'mac_clicks_divided'          => 0,
                'valid_clicks'                => 0,
                'earnings'                    => 0,
            ]
        );

        $earning->increment('total_raw_clicks');

        // Load divider record once
        $dividerRecord = \App\Models\ClickDivider::where('user_id', $publisherId)->first();

        if ($isWindows) {
            $earning->increment('windows_clicks');

            // Snapshot-aware Windows divider — new divider only applies to clicks after the change
            $dividerValue = ($dividerRecord && $dividerRecord->is_enabled)
                ? (float) $dividerRecord->divider_value : 1;

            $fresh       = $earning->fresh();
            $baseCount   = (int) ($fresh->windows_clicks_base_count ?? 0);
            $baseDivided = (int) ($fresh->windows_clicks_base_divided ?? 0);
            $newWindows  = $fresh->windows_clicks - $baseCount;

            $earning->windows_clicks_divided = $baseDivided + (int) floor($newWindows / $dividerValue);
        }

        if ($isMac) {
            $earning->increment('mac_clicks');

            // Snapshot-aware Mac divider
            $macDividerValue = ($dividerRecord && $dividerRecord->mac_divider_enabled)
                ? (float) $dividerRecord->mac_divider_value : 1;

            $fresh          = $earning->fresh();
            $macBaseCount   = (int) ($fresh->mac_clicks_base_count ?? 0);
            $macBaseDivided = (int) ($fresh->mac_clicks_base_divided ?? 0);
            $newMac         = $fresh->mac_clicks - $macBaseCount;

            $earning->mac_clicks_divided = $macBaseDivided + (int) floor($newMac / $macDividerValue);
        }

        // valid_clicks = windows (divider-adjusted) + mac (divider-adjusted) + all other OS (raw)
        $fresh          = $earning->fresh();
        $windowsDivided = (int) ($fresh->windows_clicks_divided ?? 0);
        $macDivided     = (int) ($fresh->mac_clicks_divided ?? 0);
        $otherClicks    = Click::where('user_id', $publisherId)
            ->where('is_counted', true)
            ->where('is_windows', false)
            ->where('is_mac', false)
            ->whereDate('created_at', $today)
            ->count();

        $earning->valid_clicks = $otherClicks + $windowsDivided + $macDivided;

        // Country breakdown
        $breakdown                           = $earning->country_breakdown ?? [];
        $breakdown[$countryCode]             = $breakdown[$countryCode] ?? ['clicks' => 0, 'earnings' => 0];
        $breakdown[$countryCode]['clicks']++;
        $breakdown[$countryCode]['earnings'] += $clickValue;
        $earning->country_breakdown          = $breakdown;

        // OS breakdown
        $osKey               = $osName ?: 'Unknown';
        $osBreakdown         = $earning->os_breakdown ?? [];
        $osBreakdown[$osKey] = $osBreakdown[$osKey] ?? ['clicks' => 0];
        $osBreakdown[$osKey]['clicks']++;
        $earning->os_breakdown = $osBreakdown;

        $earning->earnings += $clickValue;
        $earning->save();

        if ($clickValue > 0) {
            $earning->user->publisherProfile?->increment('balance', $clickValue);
            $earning->user->publisherProfile?->increment('total_earnings', $clickValue);
        }
    }

    private function updateCampaignStats(int $campaignId, string $countryCode): void
    {
        $campaign = Campaign::find($campaignId);
        if (!$campaign || !$campaign->isActive()) return;

        $campaign->increment('delivered_clicks');
        $campaign->refresh();

        $breakdown = $campaign->click_breakdown ?? [];
        $breakdown[$countryCode] = ($breakdown[$countryCode] ?? 0) + 1;
        $campaign->click_breakdown = $breakdown;

        $cost = 0;
        if ($campaign->contract_type === 'fixed_rate' && $campaign->fixed_rate > 0) {
            $cost = (float) $campaign->fixed_rate;
        } elseif ($campaign->contract_type === 'per_click') {
            $rates = $campaign->country_rates ?? [];
            $cost  = (float) ($rates[$countryCode] ?? $rates['default'] ?? 0);
        }

        if ($cost > 0) {
            $campaign->user->advertiserProfile?->decrement('balance', $cost);
            $campaign->user->advertiserProfile?->increment('total_spent', $cost);
        }

        if ($campaign->delivered_clicks >= $campaign->target_clicks) {
            $campaign->status       = 'completed';
            $campaign->completed_at = now();
        }

        $campaign->save();
    }

    private function maybeStartTestPeriod(TrackingLink $link): void
    {
        $profile = $link->user?->publisherProfile;
        if (!$profile) return;

        if ($profile->test_status === 'running' && $profile->test_ended_at && $profile->test_ended_at->isPast()) {
            $profile->update(['test_status' => 'completed']);
            return;
        }

        if ($profile->test_status !== 'not_started') return;
        if ($profile->contract_type !== 'none') return;

        $uniqueClicks = Click::where('user_id', $link->user_id)->where('is_counted', true)->count();
        if ($uniqueClicks >= 20) {
            $profile->update([
                'test_status'     => 'running',
                'test_started_at' => now(),
                'test_ended_at'   => now()->addHours(48),
            ]);
        }
    }

    private function processInstallsIfApplicable(TrackingLink $link, array $geoData, bool $isCounted, bool $isWindows): void
    {
        if (!$isCounted || !$isWindows) return;

        $profile = $link->user?->publisherProfile;
        if (!$profile || $profile->contract_type !== 'installs_base') return;

        $countryCode = strtolower($geoData['country_code'] ?? '');
        if (!$countryCode || $countryCode === 'xx') return;

        $weekday = (int) now()->format('w');
        $ratio   = \App\Models\InstallDayRatio::where('weekday', $weekday)->value('ratio') ?? 30;

        // Apply divider: publisher sees divider-adjusted clicks, so installs must scale
        // accordingly. If divider=3 and ratio=30, publisher needs 90 raw Windows clicks
        // per install (same as 30 divider-adjusted clicks = 1 install).
        $divider        = \App\Models\ClickDivider::where('user_id', $link->user_id)->where('is_enabled', true)->first();
        $dividerValue   = $divider ? max(1, (float)$divider->divider_value) : 1;
        $effectiveRatio = max(1, (int)round($ratio * $dividerValue));

        $pending               = $profile->install_pending_clicks ?? [];
        $pending[$countryCode] = ($pending[$countryCode] ?? 0) + 1;

        $installs = (int) floor($pending[$countryCode] / $effectiveRatio);
        if ($installs > 0) {
            $pending[$countryCode] = $pending[$countryCode] % $effectiveRatio;

            $rate     = \App\Models\InstallCountryRate::where('country_code', $countryCode)->where('is_active', true)->value('rate_usd') ?? 0;
            $earnings = $installs * (float) $rate;

            $installRecord = \App\Models\PublisherInstall::firstOrCreate(
                ['user_id' => $link->user_id, 'country_code' => $countryCode, 'date' => now()->toDateString()],
                ['country_name' => $geoData['country_name'], 'install_count' => 0, 'earnings' => 0]
            );
            $installRecord->increment('install_count', $installs);
            if ($earnings > 0) {
                $installRecord->increment('earnings', $earnings);
                $profile->increment('balance', $earnings);
                $profile->increment('total_earnings', $earnings);
            }
        }

        $profile->update(['install_pending_clicks' => $pending]);
    }

    private function processMacInstallsIfApplicable(TrackingLink $link, array $geoData, bool $isCounted, bool $isMac): void
    {
        if (!$isCounted || !$isMac) return;

        $profile = $link->user?->publisherProfile;
        if (!$profile || $profile->contract_type !== 'installs_base') return;

        $countryCode = strtolower($geoData['country_code'] ?? '');
        if (!$countryCode || $countryCode === 'xx') return;

        $weekday = (int) now()->format('w');
        $ratio   = \App\Models\InstallDayRatio::where('weekday', $weekday)->value('ratio') ?? 30;

        // Apply Mac divider: mac installs scale with mac divider, same logic as Windows
        $divider        = \App\Models\ClickDivider::where('user_id', $link->user_id)->where('mac_divider_enabled', true)->first();
        $dividerValue   = $divider ? max(1, (float)$divider->mac_divider_value) : 1;
        $effectiveRatio = max(1, (int)round($ratio * $dividerValue));

        $pending               = $profile->mac_install_pending_clicks ?? [];
        $pending[$countryCode] = ($pending[$countryCode] ?? 0) + 1;

        $installs = (int) floor($pending[$countryCode] / $effectiveRatio);
        if ($installs > 0) {
            $pending[$countryCode] = $pending[$countryCode] % $effectiveRatio;

            $rate     = MacInstallCountryRate::where('country_code', $countryCode)->where('is_active', true)->value('mac_rate_usd') ?? 0;
            $earnings = $installs * (float) $rate;

            $installRecord = MacPublisherInstall::firstOrCreate(
                ['user_id' => $link->user_id, 'country_code' => $countryCode, 'date' => now()->toDateString()],
                ['country_name' => $geoData['country_name'], 'install_count' => 0, 'earnings' => 0]
            );
            $installRecord->increment('install_count', $installs);
            if ($earnings > 0) {
                $installRecord->increment('earnings', $earnings);
                $profile->increment('balance', $earnings);
                $profile->increment('total_earnings', $earnings);
            }
        }

        $profile->update(['mac_install_pending_clicks' => $pending]);
    }

    private function generateFingerprint(Request $request): string
    {
        $data = $request->ip() . $request->userAgent() . $request->header('accept-language') . $request->header('accept-encoding');
        return hash('sha256', $data);
    }
}
