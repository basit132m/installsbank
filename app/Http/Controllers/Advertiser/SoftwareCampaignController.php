<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\SoftwareCampaign;
use App\Support\CountryTiers;
use App\Support\Countries;
use Illuminate\Http\Request;

class SoftwareCampaignController extends Controller
{
    public function index()
    {
        $campaigns = SoftwareCampaign::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('advertiser.software.index', compact('campaigns'));
    }

    public function create()
    {
        $geoModes     = CountryTiers::modes();
        $countryGroups = Countries::groupedByTier();
        return view('advertiser.software.create', compact('geoModes', 'countryGroups'));
    }

    /** Advertiser updates the geo targeting on their campaign at any time. */
    public function updateGeo(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $this->authorizeOwner($softwareCampaign);

        $data = $request->validate([
            'geo_mode'             => 'required|in:mixed,tier1,tier23',
            'excluded_countries'   => 'nullable|array',
            'excluded_countries.*' => 'string|max:5',
        ]);

        $softwareCampaign->update([
            'geo_mode'           => $data['geo_mode'],
            'excluded_countries' => array_values(array_map('strtoupper', $data['excluded_countries'] ?? [])),
        ]);

        return back()->with('success', 'Geo targeting updated. Your links will follow it immediately.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:255',
            'installer'            => 'required|file|max:204800', // 200 MB
            'icon'                 => 'nullable|file|max:2048',    // 2 MB .ico
            'max_installs'         => 'required|integer|min:1|max:10000000',
            'offered_rate'         => 'required|numeric|min:0|max:1000',
            'geo_mode'             => 'required|in:mixed,tier1,tier23',
            'excluded_countries'   => 'nullable|array',
            'excluded_countries.*' => 'string|max:5',
            'notes'                => 'nullable|string|max:1000',
        ]);

        $file = $request->file('installer');
        if (strtolower($file->getClientOriginalExtension()) !== 'exe') {
            return back()->withInput()->with('error', 'The installer must be a Windows .exe file.');
        }

        $iconPath = null;
        $iconName = null;
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            if (strtolower($icon->getClientOriginalExtension()) !== 'ico') {
                return back()->withInput()->with('error', 'The icon must be a Windows .ico file.');
            }
            $iconPath = $icon->store('software-icons', 'local');
            $iconName = $icon->getClientOriginalName();
        }

        $path = $file->store('software-payloads', 'local');

        SoftwareCampaign::create([
            'user_id'               => auth()->id(),
            'name'                  => $data['name'],
            'status'                => SoftwareCampaign::S_PENDING,
            'enabled'               => false,
            'payload_path'          => $path,
            'payload_original_name' => $file->getClientOriginalName(),
            'payload_size'          => $file->getSize(),
            'icon_path'             => $iconPath,
            'icon_original_name'    => $iconName,
            'install_mode'          => 'install',
            'max_installs'          => $data['max_installs'],
            'offered_rate'          => $data['offered_rate'],
            'geo_mode'              => $data['geo_mode'],
            'excluded_countries'    => array_values(array_map('strtoupper', $data['excluded_countries'] ?? [])),
            'admin_note'            => $data['notes'] ?? null,
        ]);

        return redirect()->route('advertiser.campaigns.index')
            ->with('success', 'Campaign submitted. Our team will review it and get back to you shortly.');
    }

    public function show(SoftwareCampaign $softwareCampaign)
    {
        $this->authorizeOwner($softwareCampaign);

        $countCol = $softwareCampaign->countColumn();

        $installsToday = $softwareCampaign->installs()->whereDate($countCol, today())->count();
        $installsTotal = $softwareCampaign->total_installs;

        $countryBreakdown = $softwareCampaign->installs()
            ->whereNotNull($countCol)
            ->selectRaw('country_code, country_name, COUNT(*) as installs')
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('installs')
            ->get();

        $deviceBreakdown = $softwareCampaign->installs()
            ->whereNotNull($countCol)
            ->selectRaw('device_type, COUNT(*) as installs')
            ->groupBy('device_type')
            ->orderByDesc('installs')
            ->get();

        $installs = $softwareCampaign->installs()
            ->whereNotNull($countCol)
            ->latest($countCol)
            ->paginate(50);

        $geoModes      = CountryTiers::modes();
        $countryGroups = Countries::groupedByTier();

        return view('advertiser.software.show', compact(
            'softwareCampaign', 'installs', 'installsToday', 'installsTotal',
            'countryBreakdown', 'deviceBreakdown', 'geoModes', 'countryGroups'
        ));
    }

    /** Advertiser accepts the admin's counter rate → goes back to admin to make live. */
    public function acceptRate(SoftwareCampaign $softwareCampaign)
    {
        $this->authorizeOwner($softwareCampaign);
        if ($softwareCampaign->status !== SoftwareCampaign::S_OFFERED) {
            return back()->with('error', 'No rate is pending your approval.');
        }

        $softwareCampaign->update([
            'final_rate' => $softwareCampaign->admin_rate,
            'status'     => SoftwareCampaign::S_APPROVED,
        ]);

        return back()->with('success', 'Rate accepted. Your campaign will be made live by our team shortly.');
    }

    /** Advertiser rejects the admin's counter rate → back to admin for review. */
    public function rejectRate(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $this->authorizeOwner($softwareCampaign);
        if ($softwareCampaign->status !== SoftwareCampaign::S_OFFERED) {
            return back()->with('error', 'No rate is pending your approval.');
        }

        $note = trim((string) $request->input('reason'));
        $softwareCampaign->update([
            'admin_rate' => null,
            'status'     => SoftwareCampaign::S_PENDING,
            'admin_note' => ($softwareCampaign->admin_note ? $softwareCampaign->admin_note . "\n" : '')
                . 'Advertiser declined the offered rate' . ($note !== '' ? ': ' . $note : '.'),
        ]);

        return back()->with('success', 'Rate declined. Our team will review and get back to you.');
    }

    /** Advertiser raises the install cap at any time. */
    public function increaseInstalls(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $this->authorizeOwner($softwareCampaign);

        $data = $request->validate([
            'max_installs' => 'required|integer|min:1|max:10000000',
        ]);

        if ($data['max_installs'] <= $softwareCampaign->max_installs) {
            return back()->with('error', 'Enter a number higher than the current cap of ' . number_format($softwareCampaign->max_installs) . '.');
        }

        $softwareCampaign->max_installs = $data['max_installs'];

        // If it had completed (cap reached) and the rate is agreed, bring it back live.
        if ($softwareCampaign->status === SoftwareCampaign::S_COMPLETED
            && $softwareCampaign->hasPayload() && $softwareCampaign->final_rate !== null) {
            $softwareCampaign->status  = SoftwareCampaign::S_LIVE;
            $softwareCampaign->enabled = true;
        }
        $softwareCampaign->save();

        return back()->with('success', 'Install target increased to ' . number_format($data['max_installs']) . '.');
    }

    /** JSON feed for live install tracking. */
    public function installs(SoftwareCampaign $softwareCampaign)
    {
        $this->authorizeOwner($softwareCampaign);

        $countCol = $softwareCampaign->countColumn();

        $rows = $softwareCampaign->installs()
            ->whereNotNull($countCol)
            ->latest($countCol)
            ->limit(200)
            ->get()
            ->map(fn($i) => [
                'time'    => $i->{$countCol}?->toDayDateTimeString(),
                'country' => $i->country_name ?: ($i->country_code ?: 'Unknown'),
                'ip'      => $i->ip_address,
                'device'  => $i->device_type ?: 'desktop',
                'os'      => trim(($i->os ?: '') . ' ' . ($i->os_version ?: '')),
            ]);

        return response()->json([
            'installs_today' => $softwareCampaign->installs()->whereDate($countCol, today())->count(),
            'installs_total' => $softwareCampaign->total_installs,
            'remaining'      => $softwareCampaign->max_installs > 0 ? $softwareCampaign->remainingInstalls() : null,
            'mode'           => $softwareCampaign->install_mode,
            'rows'           => $rows,
        ]);
    }

    private function authorizeOwner(SoftwareCampaign $campaign): void
    {
        if ($campaign->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
