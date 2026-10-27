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
