<?php

namespace App\Services;

use App\Models\Click;
use App\Models\FraudAlert;
use Illuminate\Support\Facades\Cache;

class FraudDetectionService
{
    private const DUPLICATE_WINDOW = 24 * 3600; // 24-hour dedup window

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

    // Country code → expected Accept-Language prefixes
    // English is always allowed as a universal browser language
    private const COUNTRY_LANGUAGE_MAP = [
        'ID' => ['id'],
        'LT' => ['lt'],
        'LV' => ['lv'],
        'ET' => ['et'],
        'TR' => ['tr'],
        'RU' => ['ru'],
        'CN' => ['zh'],
        'JP' => ['ja'],
        'KR' => ['ko'],
        'TH' => ['th'],
        'VN' => ['vi'],
        'PL' => ['pl'],
        'DE' => ['de'],
        'FR' => ['fr'],
        'NL' => ['nl'],
        'IT' => ['it'],
        'ES' => ['es'],
        'PT' => ['pt'],
        'AR' => ['ar'],
        'SA' => ['ar'],
        'EG' => ['ar'],
        'UA' => ['uk', 'ru'],
        'PK' => ['ur', 'en'],
        'BD' => ['bn'],
        'MM' => ['my'],
        'MY' => ['ms', 'en'],
        'PH' => ['tl', 'fil', 'en'],
        'IN' => ['hi', 'en', 'bn', 'te', 'mr', 'ta', 'gu'],
        'NG' => ['en'],
        'GH' => ['en'],
        'RO' => ['ro'],
        'HU' => ['hu'],
        'CZ' => ['cs'],
        'SK' => ['sk'],
        'BG' => ['bg'],
        'HR' => ['hr'],
        'RS' => ['sr'],
        'GR' => ['el'],
        'IL' => ['he'],
        'IR' => ['fa'],
    ];

    // Known traffic-exchange / PTC / bot-traffic referrer domains
    private const SUSPICIOUS_REFERRERS = [
        'hitleap.com', 'easyhits4u.com', 'trafficswirl.com',
        'hits2u.com', '10khits.com', 'smileytraffic.com',
        'jingling.com', 'traffic-splash.com', 'trafficg.com',
        'autowebsurf.com', 'bravenet.com', 'webhitscounter.com',
        'familysurf.com', 'surfthechannel.com', 'bigsite.ru',
        'trafficfactory.biz', 'adnxs.com', 'adsterra.com',
        'popads.net', 'popcash.net', 'clickdealer.com',
        'zeropark.com', 'propellerads.com',
    ];

    // UA substrings that indicate headless/automation browsers
    private const HEADLESS_UA_PATTERNS = [
        'headlesschrome', 'phantomjs', 'selenium', 'webdriver',
        'htmlunit', 'slimerjs', 'nightmare', 'splash', 'puppeteer',
        'playwright', 'cypress',
    ];

    /**
     * Main fraud check. $fraudSettings contains per-publisher toggles:
     * [
     *   'country_mismatch'    => bool,
     *   'suspicious_referrer' => bool,
     *   'headless_browser'    => bool,
     *   'allowed_countries'   => array|null,
     * ]
     */
    public function check(array $clickData, array $fraudSettings = []): array
    {
        $result = ['is_fraud' => false, 'fraud_reason' => null, 'is_vpn' => false, 'is_proxy' => false];

        // 0. Country whitelist — block non-whitelisted GEOs before anything else
        if (!empty($fraudSettings['allowed_countries'])) {
            $allowed = array_map('strtoupper', $fraudSettings['allowed_countries']);
            if (!in_array(strtoupper($clickData['country_code'] ?? ''), $allowed)) {
                $result['is_fraud'] = true;
                $result['fraud_reason'] = 'country_blocked';
                $this->logFraudAlert($clickData, 'country_blocked');
                return $result;
            }
        }

        // 1. Duplicate session check (24h window using fingerprint = IP+UA+language)
        if ($this->isDuplicateSession($clickData['tracking_link_id'], $clickData['fingerprint'])) {
            $result['is_fraud'] = true;
            $result['fraud_reason'] = 'duplicate_ip';
            $this->logFraudAlert($clickData, 'duplicate_ip');
            return $result;
        }

        // 2. VPN/Proxy — ASN-based only (no header inspection due to Hostinger LB)
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

        // 3. Bot / empty UA detection
        if ($this->isBot($clickData['user_agent'] ?? '')) {
            $result['is_fraud'] = true;
            $result['fraud_reason'] = 'bot_detected';
            $this->logFraudAlert($clickData, 'bot_detected');
            return $result;
        }

        // 4. Headless browser detection (optional, per-publisher)
        if (!empty($fraudSettings['headless_browser'])) {
            if ($this->isHeadlessBrowser($clickData['user_agent'] ?? '', $clickData['headers'] ?? [])) {
                $result['is_fraud'] = true;
                $result['fraud_reason'] = 'headless_browser';
                $this->logFraudAlert($clickData, 'headless_browser');
                return $result;
            }
        }

        // 5. Country mismatch (optional, per-publisher)
        if (!empty($fraudSettings['country_mismatch'])) {
            if ($this->isCountryMismatch($clickData['country_code'] ?? '', $clickData['headers'] ?? [])) {
                $result['is_fraud'] = true;
                $result['fraud_reason'] = 'country_mismatch';
                $this->logFraudAlert($clickData, 'country_mismatch');
                return $result;
            }
        }

        // 6. Suspicious referrer (optional, per-publisher)
        if (!empty($fraudSettings['suspicious_referrer'])) {
            if ($this->isSuspiciousReferrer($clickData['referrer'] ?? '')) {
                $result['is_fraud'] = true;
                $result['fraud_reason'] = 'suspicious_referrer';
                $this->logFraudAlert($clickData, 'suspicious_referrer');
                return $result;
            }
        }

        return $result;
    }

    private function isDuplicateSession(int $linkId, string $fingerprint): bool
    {
        $cacheKey = "click_fp_{$linkId}_{$fingerprint}";
        if (Cache::has($cacheKey)) return true;
        Cache::put($cacheKey, 1, self::DUPLICATE_WINDOW);
        return Click::where('tracking_link_id', $linkId)
            ->where('fingerprint', $fingerprint)
            ->where('created_at', '>=', now()->subSeconds(self::DUPLICATE_WINDOW))
            ->exists();
    }

    private function checkVpnProxy(string $ip, array $headers): array
    {
        $result = ['is_vpn' => false, 'is_proxy' => false];
        // NOTE: No proxy header checks — Hostinger LB adds X-Forwarded-For to all requests
        $asnResult = $this->checkAsnReputation($ip);
        if ($asnResult === 'vpn') $result['is_vpn'] = true;
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
                foreach (self::VPN_KEYWORDS as $kw) { if (str_contains($org, $kw)) return 'vpn'; }
                foreach (self::DATACENTER_KEYWORDS as $kw) { if (str_contains($org, $kw)) return 'vpn'; }
                foreach (self::GENERIC_PROXY_KEYWORDS as $kw) { if (str_contains($org, $kw)) return 'proxy'; }
                return null;
            } catch (\Exception $e) {
                \Log::warning('MaxMind ASN lookup failed: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Headless browser detection using UA string patterns and missing headers.
     * Real browsers always send Accept and Accept-Language headers.
     */
    private function isHeadlessBrowser(string $ua, array $headers): bool
    {
        $uaLower = strtolower($ua);

        // Known headless/automation UA strings
        foreach (self::HEADLESS_UA_PATTERNS as $pattern) {
            if (str_contains($uaLower, $pattern)) return true;
        }

        // Real browsers always send Accept header
        $accept = $headers['accept'][0] ?? $headers['Accept'][0] ?? null;
        if (empty($accept)) return true;

        // Real browsers always send Accept-Language
        $lang = $headers['accept-language'][0] ?? $headers['Accept-Language'][0] ?? null;
        if (empty($lang)) return true;

        return false;
    }

    /**
     * Country mismatch: IP says one country but Accept-Language says another.
     * English is allowed universally. Only flags if we have a known mapping.
     */
    private function isCountryMismatch(string $countryCode, array $headers): bool
    {
        $countryCode = strtoupper($countryCode);
        if (!isset(self::COUNTRY_LANGUAGE_MAP[$countryCode])) return false;

        $langHeader = $headers['accept-language'][0] ?? $headers['Accept-Language'][0] ?? '';
        if (empty($langHeader)) return false;

        // Extract all language codes from Accept-Language (e.g. "id-ID,id;q=0.9,en;q=0.8")
        preg_match_all('/([a-z]{2})[-_]?/i', $langHeader, $matches);
        $detectedLangs = array_map('strtolower', $matches[1] ?? []);

        // Allow if English is in the list (many people use en-US browsers globally)
        if (in_array('en', $detectedLangs)) return false;

        $expectedLangs = self::COUNTRY_LANGUAGE_MAP[$countryCode];
        foreach ($expectedLangs as $expected) {
            if (in_array($expected, $detectedLangs)) return false;
        }

        return true; // Language doesn't match country at all
    }

    /**
     * Suspicious referrer: known traffic exchange / PTC sites.
     */
    private function isSuspiciousReferrer(string $referrer): bool
    {
        if (empty($referrer)) return false;

        $host = strtolower(parse_url($referrer, PHP_URL_HOST) ?? '');
        if (empty($host)) return false;

        // Strip www. prefix
        $host = preg_replace('/^www\./', '', $host);

        foreach (self::SUSPICIOUS_REFERRERS as $bad) {
            if ($host === $bad || str_ends_with($host, '.' . $bad)) return true;
        }

        return false;
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
