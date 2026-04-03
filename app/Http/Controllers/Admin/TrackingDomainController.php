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
        $serverIpv4 = gethostbyname(gethostname());
        $serverIpv6 = null;
        // Try to get IPv6
        $records = @dns_get_record(gethostname(), DNS_AAAA);
        if (!empty($records)) {
            $serverIpv6 = $records[0]['ipv6'] ?? null;
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
