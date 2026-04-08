<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlacklistedDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlacklistedDomainController extends Controller
{
    public function index()
    {
        $domains = BlacklistedDomain::orderByDesc('created_at')->paginate(50);
        return view('admin.blacklisted-domains.index', compact('domains'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'domain' => 'required|string|max:255',
            'reason' => 'nullable|string|max:500',
        ]);

        $normalised = BlacklistedDomain::normalise($data['domain']);

        if (empty($normalised)) {
            return back()->with('error', 'Invalid domain entered.');
        }

        BlacklistedDomain::firstOrCreate(
            ['domain' => $normalised],
            ['reason' => $data['reason'] ?? null]
        );

        Cache::forget('blacklisted_domains');

        return back()->with('success', "Domain \"{$normalised}\" has been blacklisted.");
    }

    public function destroy(BlacklistedDomain $blacklistedDomain)
    {
        $blacklistedDomain->delete();
        Cache::forget('blacklisted_domains');
        return back()->with('success', "Domain \"{$blacklistedDomain->domain}\" removed from blacklist.");
    }
}
