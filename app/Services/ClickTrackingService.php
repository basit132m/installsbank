<?php

namespace App\Services;

use App\Models\Click;
use App\Models\CountryRate;
use App\Models\DailyEarning;
use App\Models\TrackingLink;
use Illuminate\Http\Request;
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

        $fraudResult = $this->fraud->check($clickData);

        $clickValue = 0;
        $isCounted = !$fraudResult['is_fraud'];

        if ($isCounted) {
            $countryRate = CountryRate::where('country_code', $geoData['country_code'])
                ->where('is_active', true)->first();
            $clickValue = $countryRate ? $countryRate->rate_per_click : 0;
        }

        $click = Click::create(array_merge($clickData, [
            'is_fraud' => $fraudResult['is_fraud'],
            'fraud_reason' => $fraudResult['fraud_reason'],
            'is_vpn' => $fraudResult['is_vpn'],
            'is_proxy' => $fraudResult['is_proxy'],
            'is_counted' => $isCounted,
            'click_value' => $clickValue,
        ]));

        // Update tracking link counters
        $link->increment('total_clicks');
        if ($isCounted) {
            $link->increment('unique_clicks');
        } else {
            $link->increment('fraud_clicks');
        }

        // Update daily earnings if counted
        if ($isCounted) {
            $this->updateDailyEarnings($link->user_id, $isWindows, $geoData['country_code'], $clickValue, $link->user_id);
        }

        return $click;
    }

    private function updateDailyEarnings(int $publisherId, bool $isWindows, string $countryCode, float $clickValue, int $userId): void
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

        $earning->earnings += $clickValue;
        $earning->save();

        // Update publisher balance (will be finalized end of day in production)
        if ($clickValue > 0) {
            $earning->user->publisherProfile?->increment('balance', $clickValue);
            $earning->user->publisherProfile?->increment('total_earnings', $clickValue);
        }
    }

    private function generateFingerprint(Request $request): string
    {
        $data = $request->ip() . $request->userAgent() . $request->header('accept-language') . $request->header('accept-encoding');
        return hash('sha256', $data);
    }
}
