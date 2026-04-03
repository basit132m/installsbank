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
        return view('admin.tracking.domains', compact('domains'));
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
