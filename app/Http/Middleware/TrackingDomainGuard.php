<?php

namespace App\Http\Middleware;

use App\Models\TrackingDomain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackingDomainGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower(preg_replace('/^www\./', '', $request->getHost()));

        $trackingDomains = Cache::remember('tracking_domain_list', 300, function () {
            return TrackingDomain::where('is_active', true)->pluck('domain')
                ->map(fn($d) => strtolower(preg_replace('/^www\./', '', $d)))
                ->all();
        });

        if (in_array($host, $trackingDomains, true)) {
            // Allow every supported tracking-URL structure on custom domains
            $allowed = [
                'track/*', 'out/*', 'view/*', 'dl/*', 'get/*', 'visit/*',
                'download', 'download/file/*', 'r', 'go/*', 'favicon.ico',
                'portal', 'portal/*', // white-label dashboard may run on a neutral domain
            ];
            if (!$request->is(...$allowed)) {
                abort(404);
            }
        }

        return $next($request);
    }
}
