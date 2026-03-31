<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoLocationService
{
    public function lookup(string $ip): array
    {
        if ($this->isLocalIp($ip)) {
            return ['country_code' => 'XX', 'country_name' => 'Local', 'city' => 'Local'];
        }

        return Cache::remember("geo_{$ip}", 3600, function () use ($ip) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,city");
                if ($response->ok()) {
                    $data = $response->json();
                    if (($data['status'] ?? '') === 'success') {
                        return [
                            'country_code' => $data['countryCode'] ?? 'XX',
                            'country_name' => $data['country'] ?? 'Unknown',
                            'city' => $data['city'] ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {}
            return ['country_code' => 'XX', 'country_name' => 'Unknown', 'city' => null];
        });
    }

    private function isLocalIp(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1']) ||
               str_starts_with($ip, '192.168.') ||
               str_starts_with($ip, '10.') ||
               str_starts_with($ip, '172.');
    }
}
