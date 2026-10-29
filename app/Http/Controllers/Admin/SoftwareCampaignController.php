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

        $amountPaid = (float) $softwareCampaign->payments()->sum('amount');
        $payments   = $softwareCampaign->payments()->latest()->get();

        return view('admin.software-campaigns.show', compact(
            'softwareCampaign', 'installs', 'installsToday', 'installsTotal', 'downloadsTotal',
            'stubReady', 'downloadUrl', 'amountPaid', 'payments'
        ));
    }

    public function recordPayment(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:100000000',
            'note'   => 'nullable|string|max:255',
        ]);

        $softwareCampaign->payments()->create([
            'amount' => $data['amount'],
            'note'   => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Payment of $' . number_format($data['amount'], 2) . ' recorded.');
    }

    public function deletePayment(SoftwareCampaign $softwareCampaign, \App\Models\SoftwarePayment $payment)
    {
        if ($payment->software_campaign_id !== $softwareCampaign->id) {
            abort(404);
        }
        $payment->delete();

        return back()->with('success', 'Payment entry removed.');
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
        if (strtolower($icon->getClientOriginalExtension()) !== 'ico' || !\App\Support\UploadGuard::isIco($icon)) {
            return back()->with('error', 'The icon must be a valid Windows .ico file.');
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
        if (strtolower($file->getClientOriginalExtension()) !== 'exe' || !\App\Support\UploadGuard::isWindowsExe($file)) {
            return back()->with('error', 'The branded stub must be a valid .exe file.');
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

    /** Admin accepts the advertiser's offered rate as-is → ready to go live. */
    public function approve(SoftwareCampaign $softwareCampaign)
    {
        $softwareCampaign->update([
            'final_rate' => $softwareCampaign->offered_rate,
            'admin_rate' => null,
            'status'     => SoftwareCampaign::S_APPROVED,
        ]);

        return back()->with('success', 'Approved at the offered rate. You can now make it live.');
    }

    /** Admin proposes a different per-install rate → back to the advertiser. */
    public function counterRate(Request $request, SoftwareCampaign $softwareCampaign)
    {
        $data = $request->validate(['admin_rate' => 'required|numeric|min:0|max:1000']);

        $softwareCampaign->update([
            'admin_rate' => $data['admin_rate'],
            'status'     => SoftwareCampaign::S_OFFERED,
        ]);

        return back()->with('success', 'Rate sent to the advertiser for approval.');
    }

    /** Make an approved campaign live (start serving installs). */
    public function makeLive(SoftwareCampaign $softwareCampaign)
    {
        if ($softwareCampaign->status !== SoftwareCampaign::S_APPROVED) {
            return back()->with('error', 'The rate must be approved before going live.');
        }
        if (!$softwareCampaign->hasPayload()) {
            return back()->with('error', 'Upload the advertiser installer before going live.');
        }
        if (!$softwareCampaign->hasBrandedStub() && !Storage::disk('local')->exists(self::STUB_PATH)) {
            return back()->with('error', 'Upload the global stub binary before going live.');
        }

        $softwareCampaign->update([
            'enabled'    => true,
            'status'     => SoftwareCampaign::S_LIVE,
            'final_rate' => $softwareCampaign->final_rate ?? $softwareCampaign->offered_rate,
        ]);

        return back()->with('success', 'Campaign is now live.');
    }

    public function pause(SoftwareCampaign $softwareCampaign)
    {
        $softwareCampaign->update(['enabled' => false, 'status' => SoftwareCampaign::S_PAUSED]);
        return back()->with('success', 'Campaign paused.');
    }

    public function resume(SoftwareCampaign $softwareCampaign)
    {
        if ($softwareCampaign->capReached()) {
            return back()->with('error', 'Install cap reached — ask the advertiser to raise the target first.');
        }
        if (!$softwareCampaign->hasPayload()) {
            return back()->with('error', 'Upload the advertiser installer first.');
        }
        $softwareCampaign->update(['enabled' => true, 'status' => SoftwareCampaign::S_LIVE]);
        return back()->with('success', 'Campaign resumed.');
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
        // Accept .exe regardless of the browser-reported mime (octet-stream, x-msdownload, etc.),
        // but confirm it's a real Windows executable by its magic bytes.
        if (strtolower($file->getClientOriginalExtension()) !== 'exe' || !\App\Support\UploadGuard::isWindowsExe($file)) {
            return back()->with('error', 'The stub must be a valid Windows .exe file.');
        }

        Storage::disk('local')->putFileAs('software-stub', $file, 'stub.exe');

        return back()->with('success', 'Stub binary uploaded. All enabled campaigns now serve it.');
    }
}
