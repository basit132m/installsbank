<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignPayment;
use App\Models\TrackingLink;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $campaigns = $query->paginate(25);
        $pendingCount = Campaign::where('status', 'draft')->count();
        $pendingPayments = CampaignPayment::where('status', 'pending')->count();

        return view('admin.campaigns.index', compact('campaigns', 'pendingCount', 'pendingPayments'));
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('user.advertiserProfile', 'payments', 'trackingLinks');
        $payments = $campaign->payments()->with('confirmedBy')->latest()->get();
        return view('admin.campaigns.show', compact('campaign', 'payments'));
    }

    public function setRates(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'contract_type'  => 'required|in:per_click,fixed_rate',
            'fixed_rate'     => 'required_if:contract_type,fixed_rate|nullable|numeric|min:0.000001',
            'target_clicks'  => 'required|integer|min:1',
            'total_value'    => 'required|numeric|min:0.01',
            'admin_note'     => 'nullable|string|max:1000',
            'country_rates'  => 'nullable|string', // JSON string
        ]);

        $totalValue    = (float) $data['total_value'];
        $advanceAmount = round($totalValue * 0.5, 4);

        $countryRates = null;
        if ($data['contract_type'] === 'per_click' && !empty($data['country_rates'])) {
            $countryRates = json_decode($data['country_rates'], true);
        }

        $campaign->update([
            'contract_type' => $data['contract_type'],
            'fixed_rate'    => $data['contract_type'] === 'fixed_rate' ? $data['fixed_rate'] : null,
            'country_rates' => $countryRates,
            'target_clicks' => $data['target_clicks'],
            'total_value'   => $totalValue,
            'advance_amount'=> $advanceAmount,
            'status'        => 'pending_payment',
            'admin_note'    => $data['admin_note'] ?? null,
        ]);

        return back()->with('success', 'Rates set. Campaign is now awaiting advertiser payment.');
    }

    public function confirmPayment(Request $request, CampaignPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Payment already processed.');
        }

        $payment->update([
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        $campaign = $payment->campaign;
        $campaign->increment('total_paid', $payment->amount);

        // Add to advertiser balance
        $campaign->user->advertiserProfile?->increment('balance', $payment->amount);

        // Activate campaign if advance is covered
        if ($campaign->total_paid >= $campaign->advance_amount && $campaign->status === 'pending_payment') {
            $campaign->update(['status' => 'active', 'started_at' => now()]);
        }

        return back()->with('success', 'Payment confirmed and campaign activated.');
    }

    public function rejectPayment(Request $request, CampaignPayment $payment)
    {
        $request->validate(['rejection_note' => 'nullable|string|max:500']);
        $payment->update(['status' => 'rejected']);
        return back()->with('success', 'Payment rejected.');
    }

    public function setFallbackUrl(Request $request, Campaign $campaign)
    {
        $request->validate(['fallback_url' => 'required|url']);
        $campaign->update(['fallback_url' => $request->fallback_url]);
        return back()->with('success', 'Fallback URL set.');
    }

    public function assignLink(Request $request, Campaign $campaign)
    {
        $request->validate(['tracking_link_id' => 'required|exists:tracking_links,id']);

        $link = TrackingLink::findOrFail($request->tracking_link_id);
        $link->update([
            'campaign_id'  => $campaign->id,
            'original_url' => $campaign->destination_url,
        ]);

        return back()->with('success', 'Tracking link assigned to campaign.');
    }

    public function unassignLink(TrackingLink $trackingLink)
    {
        $trackingLink->update(['campaign_id' => null]);
        return back()->with('success', 'Link unassigned from campaign.');
    }

    public function pause(Campaign $campaign)
    {
        if ($campaign->status === 'active') {
            $campaign->update(['status' => 'paused']);
        }
        return back()->with('success', 'Campaign paused.');
    }

    public function resume(Campaign $campaign)
    {
        if ($campaign->status === 'paused') {
            $campaign->update(['status' => 'active']);
        }
        return back()->with('success', 'Campaign resumed.');
    }

    public function cancel(Campaign $campaign)
    {
        $campaign->update(['status' => 'cancelled', 'completed_at' => now()]);
        return back()->with('success', 'Campaign cancelled.');
    }
}
