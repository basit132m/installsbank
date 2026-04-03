<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class GeoLocationService
{
    private string $dbPath;

    public function __construct()
    {
        $this->dbPath = storage_path('app/geoip/GeoLite2-City.mmdb');
    }

    public function lookup(string $ip): array
    {
        if ($this->isLocalIp($ip)) {
            return ['country_code' => 'XX', 'country_name' => 'Local', 'city' => 'Local'];
        }

        return Cache::remember("geo_{$ip}", 3600, function () use ($ip) {
            return $this->lookupFromMaxMind($ip);
        });
    }

    private function lookupFromMaxMind(string $ip): array
    {
        $default = ['country_code' => 'XX', 'country_name' => 'Unknown', 'city' => null];

        if (!file_exists($this->dbPath)) {
            \Log::warning('GeoLite2 database not found at: ' . $this->dbPath);
            return $default;
        }

        try {
            $reader = new \MaxMind\Db\Reader($this->dbPath);
            $record = $reader->get($ip);
            $reader->close();

            if (!$record) {
                return $default;
            }

            return [
                'country_code' => $record['country']['iso_code'] ?? 'XX',
                'country_name' => $record['country']['names']['en'] ?? 'Unknown',
                'city'         => $record['city']['names']['en'] ?? null,
            ];
        } catch (\Exception $e) {
            \Log::error('MaxMind GeoIP lookup failed: ' . $e->getMessage());
            return $default;
        }
    }

    private function isLocalIp(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1'])
            || str_starts_with($ip, '192.168.')
            || str_starts_with($ip, '10.')
            || str_starts_with($ip, '172.');
    }
}
