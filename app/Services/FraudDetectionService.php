<?php

namespace App\Services;

use App\Models\Click;
use App\Models\FraudAlert;
use Illuminate\Support\Facades\Cache;

class FraudDetectionService
{
    private const DUPLICATE_IP_WINDOW = 6 * 3600; // 6 hours in seconds
    private const TRAFFIC_SPIKE_THRESHOLD = 3; // 3x normal hourly rate
    private const SPIKE_CHECK_HOURS = 24;

    public function check(array $clickData): array
    {
        $result = ['is_fraud' => false, 'fraud_reason' => null, 'is_vpn' => false, 'is_proxy' => false];

        // 1. Duplicate IP check within 6 hours
        if ($this->isDuplicateIp($clickData['tracking_link_id'], $clickData['ip_address'])) {
            $result['is_fraud'] = true;
            $result['fraud_reason'] = 'duplicate_ip';
            $this->logFraudAlert($clickData, 'duplicate_ip');
            return $result;
        }

        // 2. VPN/Proxy check (basic known ranges + headers)
        $vpnCheck = $this->checkVpnProxy($clickData['ip_address'], $clickData['headers'] ?? []);
        if ($vpnCheck['is_vpn']) {
            $result['is_vpn'] = true;
            $result['is_fraud'] = true;
            $result['fraud_reason'] = 'vpn_detected';
            $this->logFraudAlert($clickData, 'vpn_detected');
        }
        if ($vpnCheck['is_proxy']) {
            $result['is_proxy'] = true;
            $result['is_fraud'] = true;
            $result['fraud_reason'] = 'proxy_detected';
            $this->logFraudAlert($clickData, 'proxy_detected');
        }

        if ($result['is_fraud']) return $result;

        // 3. Bot/empty UA detection
        if ($this->isBot($clickData['user_agent'] ?? '')) {
            $result['is_fraud'] = true;
            $result['fraud_reason'] = 'bot_detected';
            $this->logFraudAlert($clickData, 'bot_detected');
            return $result;
        }

        // 4. Traffic spike detection
        if ($this->isTrafficSpike($clickData['tracking_link_id'], $clickData['user_id'])) {
            $result['is_fraud'] = true;
            $result['fraud_reason'] = 'traffic_spike';
            $this->logFraudAlert($clickData, 'traffic_spike');
            return $result;
        }

        return $result;
    }

    private function isDuplicateIp(int $linkId, string $ip): bool
    {
        $key = "click_{$linkId}_{$ip}";
        if (Cache::has($key)) return true;
        Cache::put($key, 1, self::DUPLICATE_IP_WINDOW);

        // Also check database for recent clicks
        return Click::where('tracking_link_id', $linkId)
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subSeconds(self::DUPLICATE_IP_WINDOW))
            ->exists();
    }

    private function checkVpnProxy(string $ip, array $headers): array
    {
        $result = ['is_vpn' => false, 'is_proxy' => false];

        // Check proxy headers
        $proxyHeaders = ['HTTP_VIA', 'HTTP_X_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_CLIENT_IP', 'HTTP_PROXY_CONNECTION'];
        foreach ($proxyHeaders as $header) {
            if (!empty($headers[$header])) {
                $result['is_proxy'] = true;
                return $result;
            }
        }

        // Check for known VPN/datacenter IP ranges (basic check)
        // In production, integrate with ipqualityscore.com or similar
        return $result;
    }

    private function isBot(string $ua): bool
    {
        if (empty($ua) || strlen($ua) < 20) return true;
        $botPatterns = ['bot', 'crawler', 'spider', 'scraper', 'wget', 'curl', 'python', 'java', 'go-http'];
        $uaLower = strtolower($ua);
        foreach ($botPatterns as $pattern) {
            if (str_contains($uaLower, $pattern)) return true;
        }
        return false;
    }

    private function isTrafficSpike(int $linkId, int $publisherId): bool
    {
        $key = "spike_check_{$publisherId}";
        return Cache::remember($key, 300, function () use ($linkId, $publisherId) {
            $currentHourClicks = Click::where('user_id', $publisherId)
                ->where('created_at', '>=', now()->subHour())
                ->count();

            $avgHourlyClicks = Click::where('user_id', $publisherId)
                ->where('created_at', '>=', now()->subHours(self::SPIKE_CHECK_HOURS))
                ->where('created_at', '<', now()->subHour())
                ->count() / (self::SPIKE_CHECK_HOURS - 1);

            if ($avgHourlyClicks > 10 && $currentHourClicks > ($avgHourlyClicks * self::TRAFFIC_SPIKE_THRESHOLD)) {
                return true;
            }
            return false;
        });
    }

    private function logFraudAlert(array $clickData, string $type): void
    {
        $existing = FraudAlert::where('user_id', $clickData['user_id'])
            ->where('alert_type', $type)
            ->where('is_resolved', false)
            ->where('ip_address', $clickData['ip_address'] ?? null)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        if ($existing) {
            $existing->increment('occurrences');
        } else {
            FraudAlert::create([
                'user_id' => $clickData['user_id'],
                'tracking_link_id' => $clickData['tracking_link_id'],
                'alert_type' => $type,
                'ip_address' => $clickData['ip_address'] ?? null,
                'country_code' => $clickData['country_code'] ?? null,
                'details' => $clickData,
                'occurrences' => 1,
                'is_resolved' => false,
            ]);
        }
    }
}
