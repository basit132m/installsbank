<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RateIncreaseRequest;
use App\Models\PublisherNotification;
use Illuminate\Http\Request;

class RateIncreaseRequestController extends Controller
{
    public function index()
    {
        $requests = RateIncreaseRequest::with('user')
            ->orderByRaw("FIELD(status,'pending','approved','rejected')")
            ->orderByDesc('created_at')
            ->paginate(30);

        $pendingCount = RateIncreaseRequest::where('status', 'pending')->count();
        $approved     = RateIncreaseRequest::where('status', 'approved')->count();
        $rejected     = RateIncreaseRequest::where('status', 'rejected')->count();

        return view('admin.rate-increase-requests.index', compact('requests', 'pendingCount', 'approved', 'rejected'));
    }

    public function approve(Request $request, RateIncreaseRequest $rateRequest)
    {
        if (!$rateRequest->isPending()) {
            return back()->with('error', 'This request has already been responded to.');
        }

        $data = $request->validate([
            'approved_rate' => 'required|numeric|min:0.0001|max:9999',
            'admin_note'    => 'nullable|string|max:500',
        ]);

        $effectiveFrom = now()->addMonthNoOverflow()->startOfMonth()->toDateString();

        $rateRequest->update([
            'status'        => 'approved',
            'approved_rate' => $data['approved_rate'],
            'effective_from'=> $effectiveFrom,
            'admin_note'    => $data['admin_note'] ?? null,
            'responded_at'  => now(),
        ]);

        // Notify publisher
        PublisherNotification::create([
            'user_id' => $rateRequest->user_id,
            'type'    => 'rate_increase_approved',
            'message' => "Your rate increase request was approved! Your new daily rate of \${$data['approved_rate']} will take effect from " . \Illuminate\Support\Carbon::parse($effectiveFrom)->format('F 1, Y') . '.',
        ]);

        return back()->with('success', 'Rate increase approved. New rate of $' . $data['approved_rate'] . '/day takes effect from ' . \Illuminate\Support\Carbon::parse($effectiveFrom)->format('M 1, Y') . '.');
    }

    public function reject(Request $request, RateIncreaseRequest $rateRequest)
    {
        if (!$rateRequest->isPending()) {
            return back()->with('error', 'This request has already been responded to.');
        }

        $data = $request->validate(['admin_note' => 'nullable|string|max:500']);

        $rateRequest->update([
            'status'       => 'rejected',
            'admin_note'   => $data['admin_note'] ?? null,
            'responded_at' => now(),
        ]);

        // Notify publisher
        PublisherNotification::create([
            'user_id' => $rateRequest->user_id,
            'type'    => 'rate_increase_rejected',
            'message' => 'Your rate increase request was reviewed and declined.' . ($data['admin_note'] ? ' Admin note: ' . $data['admin_note'] : ''),
        ]);

        return back()->with('success', 'Rate increase request rejected.');
    }
}
