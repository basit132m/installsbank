<?php

namespace App\Services;

use App\Models\Click;
use App\Models\FraudAlert;
use Illuminate\Support\Facades\Cache;

class FraudDetectionService
{
    private const DUPLICATE_IP_WINDOW = 6 * 3600;
    private const TRAFFIC_SPIKE_THRESHOLD = 3;
    private const SPIKE_CHECK_HOURS = 24;

    // Known VPN provider names (matched against ASN org name)
    private const VPN_KEYWORDS = [
        'nordvpn', 'expressvpn', 'browsec', 'windscribe', 'mullvad',
        'protonvpn', 'cyberghost', 'ipvanish', 'surfshark', 'tunnelbear',
        'torguard', 'hidemyass', 'strongvpn', 'privatevpn', 'purevpn',
        'vyprvpn', 'hotspot shield', 'zenmate', 'private internet access',
        'ivpn', 'airvpn', 'hide.me', 'anonine', 'perfectprivacy',
        'trust.zone', 'bolehvpn', 'goose vpn', 'speedify', 'safervpn',
    ];

    // Known datacenter/hosting providers used by VPNs and bots
    private const DATACENTER_KEYWORDS = [
        'digitalocean', 'linode', 'vultr', 'choopa', 'ovh', 'ovhcloud',
        'hetzner', 'leaseweb', 'datacamp', 'm247', 'quadranet', 'psychz',
        'multacom', 'serverius', 'sharktech', 'nexeon', 'hostwinds',
        'frantech', 'buyvm', 'hostus', 'aeza', 'combahton', 'nforce',
        'voxility', 'path network', 'servermania', 'hostkey', 'greenfloid',
        'tzulo', 'packethub', 'colocation america', 'snel.com',
        'datapoint', 'dacentec', 'robust server', 'corelink',
        'awweb', 'namecheap hosting', 'nocix', 'incapsula',
    ];

    // Generic keywords indicating non-residential traffic
    private const GENERIC_PROXY_KEYWORDS = [
        ' vpn', 'vpn ', '-vpn', 'vpn-', 'proxy', 'anonymous',
        'tor exit', 'tornode', 'exit node', 'hosting',
    ];

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

        // 2. VPN/Proxy check — headers + MaxMind ASN reputation
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

        return Click::where('tracking_link_id', $linkId)
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subSeconds(self::DUPLICATE_IP_WINDOW))
            ->exists();
    }

    private function checkVpnProxy(string $ip, array $headers): array
    {
        $result = ['is_vpn' => false, 'is_proxy' => false];

        // NOTE: We do NOT check proxy headers (X-Forwarded-For, Via, etc.)
        // because shared hosting (Hostinger) adds these headers to ALL requests
        // through their load balancer, causing every click to be falsely flagged.

        // ASN-based VPN detection using MaxMind GeoLite2-ASN database only
        $asnResult = $this->checkAsnReputation($ip);
        if ($asnResult === 'vpn') {
            $result['is_vpn'] = true;
        }

        return $result;
    }

    private function checkAsnReputation(string $ip): ?string
    {
        $dbPath = storage_path('app/geoip/GeoLite2-ASN.mmdb');
        if (!file_exists($dbPath)) {
            \Log::warning('GeoLite2-ASN database not found. VPN detection limited.');
            return null;
        }

        return Cache::remember("asn_{$ip}", 86400, function () use ($ip, $dbPath) {
            try {
                $reader = new \MaxMind\Db\Reader($dbPath);
                $record = $reader->get($ip);
                $reader->close();

                if (!$record) return null;

                $org = strtolower($record['autonomous_system_organization'] ?? '');
                if (empty($org)) return null;

                foreach (self::VPN_KEYWORDS as $keyword) {
                    if (str_contains($org, $keyword)) return 'vpn';
                }
                foreach (self::DATACENTER_KEYWORDS as $keyword) {
                    if (str_contains($org, $keyword)) return 'vpn';
                }
                foreach (self::GENERIC_PROXY_KEYWORDS as $keyword) {
                    if (str_contains($org, $keyword)) return 'proxy';
                }

                return null;
            } catch (\Exception $e) {
                \Log::warning('MaxMind ASN lookup failed: ' . $e->getMessage());
                return null;
            }
        });
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
                'user_id'          => $clickData['user_id'],
                'tracking_link_id' => $clickData['tracking_link_id'],
                'alert_type'       => $type,
                'ip_address'       => $clickData['ip_address'] ?? null,
                'country_code'     => $clickData['country_code'] ?? null,
                'details'          => $clickData,
                'occurrences'      => 1,
                'is_resolved'      => false,
            ]);
        }
    }
}
