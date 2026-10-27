<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareCampaign;
use App\Models\SoftwareInstall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoftwareCampaignController extends Controller
{
    private const STUB_PATH = 'software-stub/stub.exe';

    public function index(Request $request)
    {
        $query = SoftwareCampaign::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            });
        }

        $campaigns = $query->latest()->paginate(20)->withQueryString();
        $stubReady = Storage::disk('local')->exists(self::STUB_PATH);
        $stubSize  = $stubReady ? Storage::disk('local')->size(self::STUB_PATH) : 0;

        return view('admin.software-campaigns.index', compact('campaigns', 'stubReady', 'stubSize'));
    }

    public function show(SoftwareCampaign $softwareCampaign)
    {
        $softwareCampaign->load('user');

        $countCol = $softwareCampaign->countColumn();

        $installsToday  = $softwareCampaign->installs()->whereDate($countCol, today())->count();
        $installsTotal  = $softwareCampaign->installs()->whereNotNull($countCol)->count();
        $downloadsTotal = $softwareCampaign->installs()->count();

        $installs = $softwareCampaign->installs()
            ->latest()
            ->paginate(50);

        $stubReady = Storage::disk('local')->exists(self::STUB_PATH);

        // The distributable link the admin drives traffic to.
        $downloadUrl = route('software.download', ['code' => $softwareCampaign->code]);

        return view('admin.software-campaigns.show', compact(
            'softwareCampaign', 'installs', 'installsToday', 'installsTotal', 'downloadsTotal', 'stubReady', 'downloadUrl'
        ));
    }

    public function setDownloadName(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $data = $request->validate([
            'download_name' => 'nullable|string|max:60',
        ]);
        $softwareCampaign->update(['download_name' => $data['download_name'] ?: null]);

        return back()->with('success', 'Download name saved. New downloads will use it.');
    }

    /** Admin downloads the advertiser's .ico so it can be baked into the branded stub. */
    public function downloadIcon(SoftwareCampaign $softwareCampaign)
    {
        if (!$softwareCampaign->hasIcon()) {
            return back()->with('error', 'No icon uploaded for this campaign.');
        }

        return response()->download(
            Storage::disk('local')->path($softwareCampaign->icon_path),
            $softwareCampaign->icon_original_name ?: ($softwareCampaign->code . '.ico')
        );
    }

    /** Admin uploads/replaces the campaign icon (e.g. if the advertiser didn't). */
    public function setIcon(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $request->validate(['icon' => 'required|file|max:2048']);
        $icon = $request->file('icon');
        if (strtolower($icon->getClientOriginalExtension()) !== 'ico') {
            return back()->with('error', 'The icon must be a Windows .ico file.');
        }

        if ($softwareCampaign->icon_path) {
            Storage::disk('local')->delete($softwareCampaign->icon_path);
        }
        $softwareCampaign->update([
            'icon_path'          => $icon->store('software-icons', 'local'),
            'icon_original_name' => $icon->getClientOriginalName(),
        ]);

        return back()->with('success', 'Icon saved. Build the branded stub with it and upload below.');
    }

    /** Admin uploads the branded (icon-baked) stub built for this campaign. */
    public function uploadCampaignStub(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $request->validate(['stub' => 'required|file|max:51200']);
        $file = $request->file('stub');
        if (strtolower($file->getClientOriginalExtension()) !== 'exe') {
            return back()->with('error', 'The branded stub must be a .exe file.');
        }

        Storage::disk('local')->putFileAs('software-stub/campaigns', $file, $softwareCampaign->code . '.exe');

        return back()->with('success', 'Branded stub uploaded. This campaign now serves it.');
    }

    /** Remove the branded stub — campaign falls back to the global stub. */
    public function removeCampaignStub(SoftwareCampaign $softwareCampaign)
    {
        Storage::disk('local')->delete($softwareCampaign->brandedStubRelPath());

        return back()->with('success', 'Branded stub removed. This campaign now uses the global stub.');
    }

    /** Admin downloads the advertiser's raw uploaded .exe. */
    public function downloadPayload(SoftwareCampaign $softwareCampaign)
    {
        if (!$softwareCampaign->hasPayload()) {
            return back()->with('error', 'No installer uploaded for this campaign.');
        }

        return response()->download(
            Storage::disk('local')->path($softwareCampaign->payload_path),
            $softwareCampaign->payload_original_name ?: ($softwareCampaign->code . '.exe')
        );
    }

    public function setMode(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $data = $request->validate([
            'install_mode' => 'required|in:open,install',
        ]);
        $softwareCampaign->update(['install_mode' => $data['install_mode']]);

        return back()->with('success', 'Counting mode updated to "' . $data['install_mode'] . '".');
    }

    public function toggle(SoftwareCampaign $softwareCampaign)
    {
        if (!$softwareCampaign->enabled) {
            // Can't enable a campaign that has no installer or no stub.
            if (!$softwareCampaign->hasPayload()) {
                return back()->with('error', 'Upload the advertiser installer before enabling.');
            }
            if (!Storage::disk('local')->exists(self::STUB_PATH)) {
                return back()->with('error', 'Upload the global stub binary before enabling any campaign.');
            }
        }

        $softwareCampaign->update([
            'enabled' => !$softwareCampaign->enabled,
            'status'  => !$softwareCampaign->enabled ? 'active' : 'paused',
        ]);

        return back()->with('success', $softwareCampaign->enabled ? 'Campaign is now live.' : 'Campaign paused.');
    }

    public function setNote(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $data = $request->validate(['admin_note' => 'nullable|string|max:2000']);
        $softwareCampaign->update(['admin_note' => $data['admin_note'] ?? null]);

        return back()->with('success', 'Note saved.');
    }

    public function destroy(SoftwareCampaign $softwareCampaign)
    {
        if ($softwareCampaign->payload_path) {
            Storage::disk('local')->delete($softwareCampaign->payload_path);
        }
        if ($softwareCampaign->icon_path) {
            Storage::disk('local')->delete($softwareCampaign->icon_path);
        }
        Storage::disk('local')->delete($softwareCampaign->brandedStubRelPath());
        $softwareCampaign->delete();

        return redirect()->route('admin.software-campaigns.index')->with('success', 'Campaign deleted.');
    }

    /** Upload the single global stub binary all campaigns are served from. */
    public function uploadStub(Request $request)
    {
        $request->validate([
            'stub' => 'required|file|max:51200', // 50 MB
        ]);

        $file = $request->file('stub');
        // Accept .exe regardless of the browser-reported mime (octet-stream, x-msdownload, etc.)
        if (strtolower($file->getClientOriginalExtension()) !== 'exe') {
            return back()->with('error', 'The stub must be a .exe file.');
        }

        Storage::disk('local')->putFileAs('software-stub', $file, 'stub.exe');

        return back()->with('success', 'Stub binary uploaded. All enabled campaigns now serve it.');
    }
}
