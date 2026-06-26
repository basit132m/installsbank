<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LanderSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LanderController extends Controller
{
    private static array $schemes = [
        'dark-red'    => ['bg_from'=>'#0f0a0a','bg_to'=>'#1a0505','card'=>'rgba(20,10,10,0.82)','accent'=>'#dc2626','accent2'=>'#991b1b','glow'=>'rgba(220,38,38,0.25)','text_accent'=>'#fca5a5'],
        'dark-blue'   => ['bg_from'=>'#080d1a','bg_to'=>'#030a1a','card'=>'rgba(8,13,30,0.85)','accent'=>'#2563eb','accent2'=>'#1d4ed8','glow'=>'rgba(37,99,235,0.25)','text_accent'=>'#93c5fd'],
        'dark-green'  => ['bg_from'=>'#030f0a','bg_to'=>'#010f07','card'=>'rgba(3,15,10,0.85)','accent'=>'#059669','accent2'=>'#047857','glow'=>'rgba(5,150,105,0.25)','text_accent'=>'#6ee7b7'],
        'dark-purple' => ['bg_from'=>'#0a060f','bg_to'=>'#06030f','card'=>'rgba(10,6,15,0.85)','accent'=>'#7c3aed','accent2'=>'#6d28d9','glow'=>'rgba(124,58,237,0.25)','text_accent'=>'#c4b5fd'],
        'neon-cyan'   => ['bg_from'=>'#030a10','bg_to'=>'#010610','card'=>'rgba(3,10,16,0.85)','accent'=>'#06b6d4','accent2'=>'#0891b2','glow'=>'rgba(6,182,212,0.25)','text_accent'=>'#a5f3fc'],
        'amber-gold'  => ['bg_from'=>'#100900','bg_to'=>'#0f0600','card'=>'rgba(16,9,0,0.85)','accent'=>'#d97706','accent2'=>'#b45309','glow'=>'rgba(217,119,6,0.25)','text_accent'=>'#fcd34d'],
    ];

    public function show(Request $request)
    {
        $settings = LanderSetting::current();

        // Increment real visit counter atomically (no race condition)
        LanderSetting::where('id', 1)->increment('visit_count');
        $settings->visit_count += 1;

        $scheme       = self::$schemes[$settings->color_scheme] ?? self::$schemes['dark-red'];
        $displayCount = $settings->download_count + $settings->visit_count;

        // Resolve source URL from ?ref= query param
        $refTitle  = null;
        $refDomain = null;
        $refUrl    = $request->query('ref');

        if ($refUrl && filter_var($refUrl, FILTER_VALIDATE_URL) && preg_match('/^https?:\/\//i', $refUrl)) {
            // Extract domain as a guaranteed fallback display
            $parsed    = parse_url($refUrl);
            $refDomain = $parsed['host'] ?? null;
            if ($refDomain) {
                $refDomain = preg_replace('/^www\./i', '', $refDomain);
            }

            // Fetch the actual page title — cached per URL for 2 hours
            $cacheKey = 'ref_title_' . md5($refUrl);
            $fetched  = Cache::remember($cacheKey, 7200, fn () => $this->fetchPageTitle($refUrl));
            if ($fetched) {
                $refTitle = $fetched;
            }
        }

        return view('public.lander', compact('settings', 'scheme', 'displayCount', 'refTitle', 'refDomain'));
    }

    private function fetchPageTitle(string $url): ?string
    {
        $html = $this->fetchHtml($url);
        if (!$html) {
            return null;
        }

        if (preg_match('/<title[^>]*>(.*?)<\/title>/isu', $html, $matches)) {
            $title = trim(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            // Clean up whitespace (some titles have newlines inside)
            $title = preg_replace('/\s+/', ' ', $title);
            // Strip trailing " | Site Name" / " - Site Name" suffix
            $title = preg_replace('/\s*[\|\-–—]\s*.{3,60}$/', '', $title);
            $title = trim($title);
            return mb_strlen($title) > 0 ? $title : null;
        }

        return null;
    }

    private function fetchHtml(string $url): ?string
    {
        $headers = [
            'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
            'Accept'          => 'text/html,application/xhtml+xml',
            'Accept-Language' => 'en-US,en;q=0.9,ar;q=0.8',
        ];

        // Try Laravel HTTP client (Guzzle) first
        try {
            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->withHeaders($headers)
                ->get($url);

            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Throwable) {}

        // Fallback: file_get_contents (works on more shared-hosting configs)
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout'         => 5,
                    'header'          => implode("\r\n", array_map(
                        fn($k, $v) => "$k: $v",
                        array_keys($headers),
                        $headers
                    )),
                    'follow_location' => 1,
                    'max_redirects'   => 3,
                ],
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $html = @file_get_contents($url, false, $context);
            if ($html !== false && strlen($html) > 0) {
                return $html;
            }
        } catch (\Throwable) {}

        return null;
    }
}
