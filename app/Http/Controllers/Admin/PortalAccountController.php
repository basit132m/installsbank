<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalAccount;
use App\Models\TrackingLink;
use App\Services\PortalStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PortalAccountController extends Controller
{
    public function index()
    {
        $accounts = PortalAccount::with('trackingLink.user')->latest()->paginate(20);
        return view('admin.portal-accounts.index', compact('accounts'));
    }

    public function create()
    {
        $links = $this->linkOptions();
        return view('admin.portal-accounts.form', ['account' => new PortalAccount(), 'links' => $links]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request, null);

        $data['password'] = Hash::make($data['password']);
        PortalAccount::create($data);

        return redirect()->route('admin.portal-accounts.index')
            ->with('success', 'Dashboard account created.');
    }

    public function edit(PortalAccount $portalAccount)
    {
        $links = $this->linkOptions();
        return view('admin.portal-accounts.form', ['account' => $portalAccount, 'links' => $links]);
    }

    public function update(Request $request, PortalAccount $portalAccount, PortalStatsService $stats)
    {
        $data = $this->validateData($request, $portalAccount->id);

        // Freeze current shown numbers under the OLD settings first, so a new
        // divider / min / max only affects clicks from now on (no retroactive
        // fluctuation of already-shown stats).
        $stats->freezeBeforeSettingsChange($portalAccount);

        // Password optional on edit
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $portalAccount->update($data);

        return redirect()->route('admin.portal-accounts.index')
            ->with('success', 'Dashboard account updated.');
    }

    public function destroy(PortalAccount $portalAccount)
    {
        $portalAccount->delete();
        return back()->with('success', 'Dashboard account deleted.');
    }

    private function validateData(Request $request, ?int $id): array
    {
        $data = $request->validate([
            'username'         => ['required', 'string', 'max:60', 'alpha_dash', Rule::unique('portal_accounts', 'username')->ignore($id)],
            'password'         => [$id ? 'nullable' : 'required', 'string', 'min:6'],
            'display_title'    => ['nullable', 'string', 'max:80'],
            'tracking_link_id' => ['nullable', 'exists:tracking_links,id'],
            'divider_value'    => ['required', 'numeric', 'min:1', 'max:100000'],
            'min_clicks'       => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'max_clicks'       => ['nullable', 'integer', 'min:0', 'max:100000000', 'gte:min_clicks'],
        ], [
            'max_clicks.gte' => 'Maximum clicks must be greater than or equal to the minimum.',
        ]);

        $data['divider_enabled']  = $request->boolean('divider_enabled');
        $data['is_active']        = $request->boolean('is_active');
        $data['tracking_link_id'] = $data['tracking_link_id'] ?? null;
        $data['min_clicks']       = $data['min_clicks'] ?? null;
        $data['max_clicks']       = $data['max_clicks'] ?? null;
        $data['display_title']    = $data['display_title'] ?? null;

        return $data;
    }

    private function linkOptions()
    {
        return TrackingLink::with('user')->orderByDesc('id')->get()
            ->map(fn($l) => [
                'id'    => $l->id,
                'label' => $l->unique_code . ' — ' . ($l->user?->name ?? 'Unassigned') . ($l->name ? ' (' . $l->name . ')' : ''),
            ]);
    }
}
