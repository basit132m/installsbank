<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrackingDomain;
use Illuminate\Http\Request;

class TrackingDomainController extends Controller
{
    public function index()
    {
        $domains = TrackingDomain::withCount('trackingLinks')->latest()->get();
        $ctx = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
        $serverIpv4 = trim((string) @file_get_contents('https://api4.my-ip.io/ip', false, $ctx))
                   ?: trim((string) @file_get_contents('https://ipv4.icanhazip.com', false, $ctx))
                   ?: gethostbyname(gethostname());
        $serverIpv6 = trim((string) @file_get_contents('https://api6.my-ip.io/ip', false, $ctx))
                   ?: trim((string) @file_get_contents('https://ipv6.icanhazip.com', false, $ctx))
                   ?: null;
        // Validate they look like real IPs (not error pages)
        if ($serverIpv4 && !filter_var($serverIpv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $serverIpv4 = gethostbyname(gethostname());
        }
        if ($serverIpv6 && !filter_var($serverIpv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $serverIpv6 = null;
        }
        $appPath = base_path();
        $homeDir = dirname(dirname($appPath)); // ~/domains/installsbank.com → ~
        return view('admin.tracking.domains', compact('domains', 'serverIpv4', 'serverIpv6', 'homeDir'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'domain' => 'required|string|max:253|unique:tracking_domains,domain',
            'label'  => 'nullable|string|max:100',
        ]);

        // Strip protocol if accidentally included
        $data['domain'] = preg_replace('#^https?://#', '', rtrim($data['domain'], '/'));

        TrackingDomain::create($data);
        return back()->with('success', 'Tracking domain added: ' . $data['domain']);
    }

    public function toggle(TrackingDomain $trackingDomain)
    {
        $trackingDomain->update(['is_active' => !$trackingDomain->is_active]);
        return back()->with('success', 'Domain status updated.');
    }

    public function destroy(TrackingDomain $trackingDomain)
    {
        // Unassign all links using this domain
        $trackingDomain->trackingLinks()->update(['tracking_domain_id' => null]);
        $trackingDomain->delete();
        return back()->with('success', 'Domain deleted. Affected links reverted to default domain.');
    }
}
