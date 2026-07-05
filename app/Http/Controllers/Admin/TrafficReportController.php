<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrafficReportController extends Controller
{
    /** Step 1 — the selection form. */
    public function create()
    {
        $users = User::whereIn('role', ['publisher', 'reseller'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return view('admin.traffic-reports.create', compact('users'));
    }

    /** Step 2 — fetch the real numbers and show the editable preview panel. */
    public function fetch(Request $request)
    {
        $data = $request->validate([
            'date_from'    => 'required|date',
            'date_to'      => 'required|date|after_or_equal:date_from',
            'user_id'      => 'nullable|exists:users,id',
            'click_type'   => 'required|in:valid,all',
            'title'        => 'nullable|string|max:150',
            'prepared_for' => 'nullable|string|max:150',
        ]);

        $from = Carbon::parse($data['date_from'])->startOfDay();
        $to   = Carbon::parse($data['date_to'])->endOfDay();

        $base = Click::whereBetween('created_at', [$from, $to]);
        if ($data['click_type'] === 'valid') {
            $base->where('is_counted', true);
        }
        if (!empty($data['user_id'])) {
            $base->where('user_id', $data['user_id']);
        }

        $total = (int) (clone $base)->count();

        // OS breakdown — top 8, rest lumped into "Other"
        $osRows = (clone $base)
            ->selectRaw("COALESCE(NULLIF(os, ''), 'Unknown') as os, COUNT(*) as c")
            ->groupBy('os')->orderByDesc('c')->get();
        $os = [];
        $otherOs = 0;
        foreach ($osRows as $i => $row) {
            if ($i < 8) $os[] = ['label' => $row->os, 'value' => (int) $row->c];
            else        $otherOs += (int) $row->c;
        }
        if ($otherOs > 0) $os[] = ['label' => 'Other', 'value' => $otherOs];

        // Geo breakdown — top 20, rest lumped into "Other"
        $geoRows = (clone $base)
            ->selectRaw("COALESCE(NULLIF(country_code, ''), 'XX') as cc,
                         COALESCE(NULLIF(country_name, ''), 'Unknown') as cn,
                         COUNT(*) as c")
            ->groupBy('cc', 'cn')->orderByDesc('c')->get();
        $geo = [];
        $otherGeo = 0;
        foreach ($geoRows as $i => $row) {
            if ($i < 20) $geo[] = ['code' => strtolower($row->cc), 'label' => $row->cn, 'value' => (int) $row->c];
            else         $otherGeo += (int) $row->c;
        }
        if ($otherGeo > 0) $geo[] = ['code' => '', 'label' => 'Other', 'value' => $otherGeo];

        $targetName = 'All Traffic';
        if (!empty($data['user_id'])) {
            $u = User::find($data['user_id']);
            $targetName = $u ? ($u->name . ' (' . ucfirst($u->role) . ')') : 'Unknown';
        }

        $meta = [
            'title'        => $data['title'] ?: 'Traffic Testing Report',
            'prepared_for' => $data['prepared_for'] ?? null,
            'date_from'    => $from->toDateString(),
            'date_to'      => $to->toDateString(),
            'click_type'   => $data['click_type'],
            'target'       => $targetName,
        ];

        return view('admin.traffic-reports.preview', compact('total', 'os', 'geo', 'meta'));
    }

    /** Step 3 — render the print-ready report from the (possibly edited) values. */
    public function generate(Request $request)
    {
        $request->validate(['payload' => 'required|string']);

        $p = json_decode($request->input('payload'), true);
        if (!is_array($p)) {
            return back()->with('error', 'Invalid report data.');
        }

        $total = max(0, (int) ($p['total'] ?? 0));

        $os = collect($p['os'] ?? [])
            ->map(fn($r) => ['label' => (string) ($r['label'] ?? '—'), 'value' => max(0, (int) ($r['value'] ?? 0))])
            ->filter(fn($r) => $r['value'] > 0)
            ->sortByDesc('value')->values()->all();

        $geo = collect($p['geo'] ?? [])
            ->map(fn($r) => [
                'code'  => strtolower(preg_replace('/[^a-zA-Z]/', '', (string) ($r['code'] ?? ''))),
                'label' => (string) ($r['label'] ?? '—'),
                'value' => max(0, (int) ($r['value'] ?? 0)),
            ])
            ->filter(fn($r) => $r['value'] > 0)
            ->sortByDesc('value')->values()->all();

        $m = $p['meta'] ?? [];
        $meta = [
            'title'        => Str::limit((string) ($m['title'] ?? 'Traffic Testing Report'), 150, ''),
            'prepared_for' => $m['prepared_for'] ? Str::limit((string) $m['prepared_for'], 150, '') : null,
            'date_from'    => (string) ($m['date_from'] ?? ''),
            'date_to'      => (string) ($m['date_to'] ?? ''),
            'target'       => (string) ($m['target'] ?? 'All Traffic'),
        ];

        $reportNo    = 'IB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        $generatedAt = now()->format('M d, Y H:i');

        // Stamp / logo — served from the live images path; the stamp is optional and
        // hides itself if the admin hasn't uploaded it yet.
        $logoUrl  = 'https://installsbank.com/images/installs-bank.webp';
        $stampUrl = 'https://installsbank.com/images/report-stamp.png';

        return view('admin.traffic-reports.report', compact(
            'total', 'os', 'geo', 'meta', 'reportNo', 'generatedAt', 'logoUrl', 'stampUrl'
        ));
    }
}
