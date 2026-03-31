<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudAlert;
use Illuminate\Http\Request;

class FraudAlertController extends Controller
{
    public function index(Request $request)
    {
        $query = FraudAlert::with(['publisher', 'trackingLink']);
        if ($request->filled('type')) {
            $query->where('alert_type', $request->type);
        }
        if ($request->filled('resolved')) {
            $query->where('is_resolved', $request->resolved === '1');
        } else {
            $query->where('is_resolved', false);
        }
        $alerts = $query->latest()->paginate(30);
        $alertTypes = FraudAlert::distinct()->pluck('alert_type');
        return view('admin.fraud.index', compact('alerts', 'alertTypes'));
    }

    public function resolve(FraudAlert $fraudAlert)
    {
        $fraudAlert->update(['is_resolved' => true, 'resolved_by' => auth()->id(), 'resolved_at' => now()]);
        return back()->with('success', 'Alert resolved.');
    }

    public function resolveAll(Request $request)
    {
        FraudAlert::where('is_resolved', false)->update([
            'is_resolved' => true,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);
        return back()->with('success', 'All alerts resolved.');
    }
}
