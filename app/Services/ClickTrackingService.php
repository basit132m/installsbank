<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\BlacklistedDomain;
use App\Models\Click;
use App\Models\CountryRate;
use App\Models\DailyEarning;
use App\Models\TrackingLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;

class ClickTrackingService
{
    public function __construct(
        private GeoLocationService $geo,
        private FraudDetectionService $fraud
    ) {}

    public function processClick(TrackingLink $link, Request $request): Click
    {
        $ip = $request->ip();
        $ua = $request->userAgent() ?? '';
        $agent = new Agent();
        $agent->setUserAgent($ua);

        $os = $agent->platform() ?: 'Unknown';
        $osVersion = $agent->version($os) ?: null;
        $browser = $agent->browser() ?: 'Unknown';
        $deviceType = $agent->isDesktop() ? 'desktop' : ($agent->isTablet() ? 'tablet' : 'mobile');
        $isWindows = str_contains(strtolower($os), 'windows');

        $geoData = $this->geo->lookup($ip);

        $clickData = [
            'tracking_link_id' => $link->id,
            'user_id' => $link->user_id,
            'ip_address' => $ip,
            'country_code' => $geoData['country_code'],
            'country_name' => $geoData['country_name'],
            'city' => $geoData['city'],
            'os' => $os,
            'os_version' => $osVersion,
            'device_type' => $deviceType,
            'browser' => $browser,
            'user_agent' => $ua,
            'fingerprint' => $this->generateFingerprint($request),
            'referrer' => $request->header('referer'),
            'is_windows' => $isWindows,
            'headers' => $request->headers->all(),
        ];

        // Build per-publisher fraud settings for conditional checks
        $profile = $link->user?->publisherProfile;
        $fraudSettings = [
            'country_mismatch'    => (bool) ($profile?->fraud_country_mismatch ?? false),
            'suspicious_referrer' => (bool) ($profile?->fraud_suspicious_referrer ?? false),
            'headless_browser'    => (bool) ($profile?->fraud_headless_browser ?? false),
            'allowed_countries'   => $profile?->allowed_countries ?: null,
        ];

        $fraudResult = $this->fraud->check($clickData, $fraudSettings);

        // Domain restriction: if tracking link has an allowed_domain, referrer must match
        if (!$fraudResult['is_fraud'] && $link->allowed_domain) {
            $referrerHost = strtolower(parse_url($clickData['referrer'] ?? '', PHP_URL_HOST) ?? '');
            $referrerHost = preg_replace('/^www\./', '', $referrerHost);
            $allowedHost  = strtolower(preg_replace('/^www\./', '', $link->allowed_domain));

            if ($referrerHost !== $allowedHost) {
                $fraudResult['is_fraud']    = true;
                $fraudResult['fraud_reason'] = 'domain_mismatch';
            }
        }

        // Blacklisted domain check — referrer domain must not be on the blacklist
        if (!$fraudResult['is_fraud']) {
            $referrerHost = strtolower(parse_url($clickData['referrer'] ?? '', PHP_URL_HOST) ?? '');
            $referrerHost = preg_replace('/^www\./', '', $referrerHost);

            if ($referrerHost !== '') {
                $blacklist = Cache::remember('blacklisted_domains', 3600, function () {
                    return BlacklistedDomain::pluck('domain')->all();
                });

                if (in_array($referrerHost, $blacklist, true)) {
                    $fraudResult['is_fraud']     = true;
                    $fraudResult['fraud_reason'] = 'blacklisted_domain';
                }
            }
        }

        $clickValue = 0;
        $isCounted = !$fraudResult['is_fraud'];

        // Fixed rate publishers are paid externally — no per-click earnings
        $isFixedRate = $profile?->isFixedRate() ?? false;

        // Discover new countries from ALL counted Windows clicks (fixed + per-click publishers)
        // so admin always sees new traffic countries in the rates panel.
        if ($isCounted && $isWindows) {
            $countryCode = $geoData['country_code'];
            $countryRate = CountryRate::where('country_code', $countryCode)->first();

            if (!$countryRate && !in_array($countryCode, ['XX', 'Unknown', ''])) {
                // Auto-create unrated country so admin can set a rate
                $countryRate = CountryRate::create([
                    'country_code'      => $countryCode,
                    'country_name'      => $geoData['country_name'],
                    'rate_per_click'    => 0,
                    'is_active'         => false,
                    'needs_rate_update' => true,
                ]);
            }

            // Per-click publishers only: earn based on country rate
            if (!$isFixedRate && $countryRate && $countryRate->is_active && !$countryRate->needs_rate_update) {
                $clickValue = $countryRate->rate_per_click;
            }
        }

        $click = Click::create(array_merge($clickData, [
            'is_fraud' => $fraudResult['is_fraud'],
            'fraud_reason' => $fraudResult['fraud_reason'],
            'is_vpn' => $fraudResult['is_vpn'],
            'is_proxy' => $fraudResult['is_proxy'],
            'is_counted' => $isCounted,
            'click_value' => $clickValue,
        ]));

        // Update tracking link counters + last click timestamp
        $link->increment('total_clicks');
        $link->update(['last_click_at' => now()]);
        if ($isCounted) {
            $link->increment('unique_clicks');
        } else {
            $link->increment('fraud_clicks');
        }

        // Update daily earnings if counted
        if ($isCounted) {
            $this->updateDailyEarnings($link->user_id, $isWindows, $os, $geoData['country_code'], $clickValue, $link->user_id);
        }

        $this->maybeStartTestPeriod($link);
        $this->processInstallsIfApplicable($link, $geoData, $isCounted, $isWindows);

        // Track campaign click if this link belongs to a campaign
        if ($isCounted && $link->campaign_id) {
            $this->updateCampaignStats($link->campaign_id, $geoData['country_code']);
        }

        return $click;
    }

    private function updateDailyEarnings(int $publisherId, bool $isWindows, string $osName, string $countryCode, float $clickValue, int $userId): void
    {
        $today = now()->toDateString();
        $earning = DailyEarning::firstOrCreate(
            ['user_id' => $publisherId, 'date' => $today],
            ['total_raw_clicks' => 0, 'windows_clicks' => 0, 'windows_clicks_divided' => 0, 'valid_clicks' => 0, 'earnings' => 0]
        );

        $earning->increment('total_raw_clicks');

        if ($isWindows) {
            $earning->increment('windows_clicks');
            // Apply divider to windows clicks
            $divider = \App\Models\ClickDivider::where('user_id', $publisherId)->where('is_enabled', true)->first();
            $dividerValue = $divider ? $divider->divider_value : 1;
            $earning->windows_clicks_divided = $isWindows ? floor($earning->windows_clicks / $dividerValue) : 0;
        }

        $nonWindowsClicks = Click::where('user_id', $publisherId)
            ->where('is_counted', true)
            ->where('is_windows', false)
            ->whereDate('created_at', $today)
            ->count();

        $divider = \App\Models\ClickDivider::where('user_id', $publisherId)->where('is_enabled', true)->first();
        $dividerValue = $divider ? $divider->divider_value : 1;

        $windowsDivided = $earning->fresh()->windows_clicks_divided ?? 0;
        $earning->valid_clicks = $nonWindowsClicks + $windowsDivided;

        // Country breakdown
        $breakdown = $earning->country_breakdown ?? [];
        $breakdown[$countryCode] = $breakdown[$countryCode] ?? ['clicks' => 0, 'earnings' => 0];
        $breakdown[$countryCode]['clicks']++;
        $breakdown[$countryCode]['earnings'] += $clickValue;
        $earning->country_breakdown = $breakdown;

        // OS breakdown
        $osKey = $osName ?: 'Unknown';
        $osBreakdown = $earning->os_breakdown ?? [];
        $osBreakdown[$osKey] = $osBreakdown[$osKey] ?? ['clicks' => 0];
        $osBreakdown[$osKey]['clicks']++;
        $earning->os_breakdown = $osBreakdown;

        $earning->earnings += $clickValue;
        $earning->save();

        // Update publisher balance (will be finalized end of day in production)
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

        // Update click breakdown by country
        $breakdown = $campaign->click_breakdown ?? [];
        $breakdown[$countryCode] = ($breakdown[$countryCode] ?? 0) + 1;
        $campaign->click_breakdown = $breakdown;

        // Deduct from advertiser balance based on contract type
        $cost = 0;
        if ($campaign->contract_type === 'fixed_rate' && $campaign->fixed_rate > 0) {
            $cost = (float) $campaign->fixed_rate;
        } elseif ($campaign->contract_type === 'per_click') {
            $rates = $campaign->country_rates ?? [];
            $cost = (float) ($rates[$countryCode] ?? $rates['default'] ?? 0);
        }

        if ($cost > 0) {
            $campaign->user->advertiserProfile?->decrement('balance', $cost);
            $campaign->user->advertiserProfile?->increment('total_spent', $cost);
        }

        // Complete campaign if target reached
        if ($campaign->delivered_clicks >= $campaign->target_clicks) {
            $campaign->status = 'completed';
            $campaign->completed_at = now();
        }

        $campaign->save();
    }

    private function maybeStartTestPeriod(TrackingLink $link): void
    {
        $profile = $link->user?->publisherProfile;
        if (!$profile) return;

        // Auto-complete expired tests
        if ($profile->test_status === 'running' && $profile->test_ended_at && $profile->test_ended_at->isPast()) {
            $profile->update(['test_status' => 'completed']);
            return;
        }

        if ($profile->test_status !== 'not_started') return;

        // Only for publishers who don't already have any active contract
        // (test is part of onboarding, not for existing contracted publishers)
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

        // Get today's weekday ratio (0=Sunday, 6=Saturday)
        $weekday = (int) now()->format('w'); // 0=Sunday ... 6=Saturday
        $ratio = \App\Models\InstallDayRatio::where('weekday', $weekday)->value('ratio') ?? 30;

        // Increment pending clicks for this country
        $pending = $profile->install_pending_clicks ?? [];
        $pending[$countryCode] = ($pending[$countryCode] ?? 0) + 1;

        // Check if we've hit the ratio threshold
        $installs = (int) floor($pending[$countryCode] / $ratio);
        if ($installs > 0) {
            $pending[$countryCode] = $pending[$countryCode] % $ratio; // keep remainder

            // Look up earnings rate for this country
            $rate = \App\Models\InstallCountryRate::where('country_code', $countryCode)
                ->where('is_active', true)
                ->value('rate_usd') ?? 0;

            $earnings = $installs * (float) $rate;

            // Upsert publisher_installs record
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

    private function generateFingerprint(Request $request): string
    {
        $data = $request->ip() . $request->userAgent() . $request->header('accept-language') . $request->header('accept-encoding');
        return hash('sha256', $data);
    }
}
