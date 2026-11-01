<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Checks an uploaded installer against VirusTotal's multi-engine database by
 * file hash. A hash lookup instantly catches files already known to be malware.
 * Best-effort: if no API key is set or the API fails, it returns "unscanned"
 * and never blocks the upload.
 */
class VirusScanService
{
    /**
     * @return array{status:string,positives:int,total:int,sha256:string}
     *   status: clean | flagged | unknown (not in VT) | unscanned (no key/error)
     */
    public function scan(string $absolutePath): array
    {
        $sha256 = @hash_file('sha256', $absolutePath) ?: '';
        $base   = ['positives' => 0, 'total' => 0, 'sha256' => $sha256];

        $key = config('services.virustotal.key');
        if (!$key || $sha256 === '') {
            return ['status' => 'unscanned'] + $base;
        }

        try {
            $resp = Http::withHeaders(['x-apikey' => $key])
                ->timeout(15)
                ->get("https://www.virustotal.com/api/v3/files/{$sha256}");

            if ($resp->status() === 404) {
                return ['status' => 'unknown'] + $base; // VT has never seen this file
            }
            if (!$resp->successful()) {
                return ['status' => 'unscanned'] + $base;
            }

            $stats     = (array) data_get($resp->json(), 'data.attributes.last_analysis_stats', []);
            $positives = (int) ($stats['malicious'] ?? 0) + (int) ($stats['suspicious'] ?? 0);
            $total     = array_sum(array_map('intval', $stats));

            return [
                'status'    => $positives > 0 ? 'flagged' : 'clean',
                'positives' => $positives,
                'total'     => $total,
                'sha256'    => $sha256,
            ];
        } catch (\Throwable $e) {
            return ['status' => 'unscanned'] + $base;
        }
    }

    /** Should this result block the upload outright (clearly malicious)? */
    public function isRejected(array $result): bool
    {
        return ($result['status'] ?? '') === 'flagged'
            && ($result['positives'] ?? 0) >= (int) config('services.virustotal.reject_threshold', 5);
    }
}
