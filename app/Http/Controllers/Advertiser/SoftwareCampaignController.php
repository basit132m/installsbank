<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\SoftwareCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        return view('advertiser.software.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'installer' => 'required|file|max:204800', // 200 MB
            'icon'      => 'nullable|file|max:2048',    // 2 MB .ico
            'notes'     => 'nullable|string|max:1000',
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
            'status'                => 'pending',
            'enabled'               => false,
            'payload_path'          => $path,
            'payload_original_name' => $file->getClientOriginalName(),
            'payload_size'          => $file->getSize(),
            'icon_path'             => $iconPath,
            'icon_original_name'    => $iconName,
            'install_mode'          => 'install',
            'admin_note'            => $data['notes'] ?? null,
        ]);

        return redirect()->route('advertiser.software.index')
            ->with('success', 'Software uploaded. Our team will review and activate your install campaign shortly.');
    }

    public function show(SoftwareCampaign $softwareCampaign)
    {
        $this->authorizeOwner($softwareCampaign);

        $countCol = $softwareCampaign->countColumn();

        $installsToday = $softwareCampaign->installs()->whereDate($countCol, today())->count();
        $installsTotal = $softwareCampaign->installs()->whereNotNull($countCol)->count();

        // Country breakdown of counted installs
        $countryBreakdown = $softwareCampaign->installs()
            ->whereNotNull($countCol)
            ->selectRaw('country_name, COUNT(*) as installs')
            ->groupBy('country_name')
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

        return view('advertiser.software.show', compact(
            'softwareCampaign', 'installs', 'installsToday', 'installsTotal',
            'countryBreakdown', 'deviceBreakdown'
        ));
    }

    /** JSON feed for the dashboard campaign selector (installs today, region, ip, device). */
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

        $today = $softwareCampaign->installs()->whereDate($countCol, today())->count();

        return response()->json([
            'installs_today' => $today,
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
