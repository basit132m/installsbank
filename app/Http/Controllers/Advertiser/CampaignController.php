<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignPayment;
use App\Models\Click;
use App\Models\CountryRate;
use App\Models\TrackingLink;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::where('user_id', auth()->id())->latest()->get();
        return view('advertiser.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('advertiser.campaigns.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'destination_url' => 'required|url',
            'contract_type'   => 'required|in:per_click,fixed_rate',
            'target_clicks'   => 'required|integer|min:100',
            'notes'           => 'nullable|string|max:1000',
        ]);

        Campaign::create([
            'user_id'         => auth()->id(),
            'name'            => $data['name'],
            'destination_url' => $data['destination_url'],
            'contract_type'   => $data['contract_type'],
            'target_clicks'   => $data['target_clicks'],
            'status'          => 'draft',
            'admin_note'      => $data['notes'] ?? null,
        ]);

        return redirect()->route('advertiser.campaigns.index')
            ->with('success', 'Campaign submitted. Admin will review and set rates shortly.');
    }

    public function show(Campaign $campaign)
    {
        $this->authorize_campaign($campaign);

        $payments = $campaign->payments()->latest()->get();
        $links = TrackingLink::where('campaign_id', $campaign->id)->get();

        // Click stats for this campaign
        $linkIds = $links->pluck('id');
        $clicksByDay = [];
        if ($linkIds->isNotEmpty()) {
            $rows = Click::whereIn('tracking_link_id', $linkIds)
                ->where('is_counted', true)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as clicks')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            foreach ($rows as $row) {
                $clicksByDay[] = ['date' => $row->date, 'clicks' => $row->clicks];
            }

            // OS breakdown
            $osBreakdown = Click::whereIn('tracking_link_id', $linkIds)
                ->where('is_counted', true)
                ->selectRaw('os, COUNT(*) as clicks')
                ->groupBy('os')
                ->orderByDesc('clicks')
                ->get()
                ->mapWithKeys(fn($r) => [$r->os ?: 'Unknown' => $r->clicks]);
        } else {
            $osBreakdown = collect();
        }

        $countryBreakdown = collect($campaign->click_breakdown ?? [])->sortByDesc(fn($v) => $v);
        $countryNames = CountryRate::whereIn('country_code', $countryBreakdown->keys()->toArray())
            ->pluck('country_name', 'country_code');

        return view('advertiser.campaigns.show', compact(
            'campaign', 'payments', 'links', 'clicksByDay', 'osBreakdown',
            'countryBreakdown', 'countryNames'
        ));
    }

    public function approveContract(Campaign $campaign)
    {
        $this->authorize_campaign($campaign);
        if ($campaign->status !== 'pending_approval') {
            return back()->with('error', 'No contract pending approval.');
        }
        $campaign->update(['status' => 'pending_payment']);
        return back()->with('success', 'Contract approved! Please submit your advance payment to activate the campaign.');
    }

    public function rejectContract(Request $request, Campaign $campaign)
    {
        $this->authorize_campaign($campaign);
        if ($campaign->status !== 'pending_approval') {
            return back()->with('error', 'No contract pending approval.');
        }
        $campaign->update([
            'status'     => 'draft',
            'admin_note' => ($campaign->admin_note ? $campaign->admin_note . "\n" : '') . 'Advertiser rejected contract: ' . ($request->reason ?? 'No reason given'),
        ]);
        return back()->with('success', 'Contract rejected. Admin will be notified to review the rates.');
    }

    public function submitPayment(Request $request, Campaign $campaign)
    {
        $this->authorize_campaign($campaign);

        if ($campaign->status !== 'pending_payment') {
            return back()->with('error', 'Payment can only be submitted after approving the contract.');
        }

        $data = $request->validate([
            'payment_method'  => 'required|string|max:100',
            'transaction_id'  => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0.01',
            'notes'           => 'nullable|string|max:500',
        ]);

        CampaignPayment::create([
            'campaign_id'    => $campaign->id,
            'user_id'        => auth()->id(),
            'type'           => 'advance',
            'amount'         => $data['amount'],
            'status'         => 'pending',
            'payment_method' => $data['payment_method'],
            'transaction_id' => $data['transaction_id'],
            'notes'          => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Payment submitted. Admin will confirm and activate your campaign.');
    }

    private function authorize_campaign(Campaign $campaign): void
    {
        if ($campaign->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
