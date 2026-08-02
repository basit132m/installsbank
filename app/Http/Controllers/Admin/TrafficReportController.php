<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\TrafficReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrafficReportController extends Controller
{
    /** Saved reports list. */
    public function index()
    {
        $reports = TrafficReport::with('generatedBy')->latest()->paginate(20);
        return view('admin.traffic-reports.index', compact('reports'));
    }

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
            'range_mode'   => 'nullable|in:custom,24h,48h,24h_prev',
            'date_from'    => 'required|date',
            'date_to'      => 'required|date|after_or_equal:date_from',
            'user_id'      => 'nullable|exists:users,id',
            'click_type'   => 'required|in:valid,all',
            'title'        => 'nullable|string|max:150',
            'prepared_for' => 'nullable|string|max:150',
            'show_branding' => 'nullable|boolean',
        ]);

        // Rolling hour windows take precedence over the calendar dates
        $mode        = $data['range_mode'] ?? 'custom';
        $periodLabel = null;
        if ($mode === '24h') {
            $from = now()->subHours(24);
            $to   = now();
            $periodLabel = 'Last 24 Hours';
        } elseif ($mode === '24h_prev') {
            // The 24-hour block before the most recent 24 hours (24–48h ago)
            $from = now()->subHours(48);
            $to   = now()->subHours(24);
            $periodLabel = 'Previous 24 Hours';
        } elseif ($mode === '48h') {
            $from = now()->subHours(48);
            $to   = now();
            $periodLabel = 'Last 48 Hours';
        } else {
            $from = Carbon::parse($data['date_from'])->startOfDay();
            $to   = Carbon::parse($data['date_to'])->endOfDay();
        }

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

        // 24-hour split — only for the 48-hour window: break the total into the
        // most-recent 24h and the previous 24h so they can be shown separately.
        $split = [];
        if ($mode === '48h') {
            $splitQuery = fn($start, $end) => Click::whereBetween('created_at', [$start, $end])
                ->when($data['click_type'] === 'valid', fn($q) => $q->where('is_counted', true))
                ->when(!empty($data['user_id']), fn($q) => $q->where('user_id', $data['user_id']))
                ->count();
            $split = [
                ['label' => 'Last 24 Hours',     'value' => (int) $splitQuery(now()->subHours(24), now())],
                ['label' => 'Previous 24 Hours', 'value' => (int) $splitQuery(now()->subHours(48), now()->subHours(24))],
            ];
        }

        $targetName = 'All Traffic';
        if (!empty($data['user_id'])) {
            $u = User::find($data['user_id']);
            $targetName = $u ? ($u->name . ' (' . ucfirst($u->role) . ')') : 'Unknown';
        }

        $meta = [
            'title'         => $data['title'] ?: 'Traffic Testing Report',
            'prepared_for'  => $data['prepared_for'] ?? null,
            'date_from'     => $from->toDateString(),
            'date_to'       => $to->toDateString(),
            'period_label'  => $periodLabel,
            'click_type'    => $data['click_type'],
            'target'        => $targetName,
            'show_branding' => $request->boolean('show_branding'),
        ];

        return view('admin.traffic-reports.preview', compact('total', 'os', 'geo', 'split', 'meta'));
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

        $split = collect($p['split'] ?? [])
            ->map(fn($r) => ['label' => (string) ($r['label'] ?? '—'), 'value' => max(0, (int) ($r['value'] ?? 0))])
            ->values()->all();

        // Keep the "Other" bucket at the very end of each breakdown
        $moveOtherLast = function (array $rows): array {
            $c      = collect($rows);
            $others = $c->filter(fn($r) => strtolower(trim($r['label'])) === 'other')->values();
            $rest   = $c->reject(fn($r) => strtolower(trim($r['label'])) === 'other')->values();
            return $rest->concat($others)->values()->all();
        };
        $os  = $moveOtherLast($os);
        $geo = $moveOtherLast($geo);

        $m = $p['meta'] ?? [];

        // Rate offered per day — keep only digits and a decimal point
        $rateRaw = trim((string) ($m['rate'] ?? ''));
        $rate    = $rateRaw !== '' ? preg_replace('/[^0-9.]/', '', $rateRaw) : '';

        $meta = [
            'title'         => Str::limit((string) ($m['title'] ?? 'Traffic Testing Report'), 150, ''),
            'prepared_for'  => $m['prepared_for'] ? Str::limit((string) $m['prepared_for'], 150, '') : null,
            'date_from'     => (string) ($m['date_from'] ?? ''),
            'date_to'       => (string) ($m['date_to'] ?? ''),
            'period_label'  => $m['period_label'] ? Str::limit((string) $m['period_label'], 60, '') : null,
            'target'        => Str::limit((string) ($m['target'] ?? 'All Traffic'), 120, '') ?: 'All Traffic',
            'show_branding' => (bool) ($m['show_branding'] ?? true),
            'rate'          => ($rate !== '' && (float) $rate > 0) ? $rate : null,
            'payment_terms' => $m['payment_terms'] ? Str::limit((string) $m['payment_terms'], 60, '') : null,
            'tracking_code' => $m['tracking_code'] ? Str::limit(preg_replace('/[^A-Za-z0-9\-_]/', '', (string) $m['tracking_code']), 60, '') : null,
        ];

        $reportNo = 'IB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        // Persist so every generated report is saved and re-openable
        $report = TrafficReport::create([
            'generated_by'  => auth()->id(),
            'report_no'     => $reportNo,
            'title'         => $meta['title'],
            'show_branding' => $meta['show_branding'],
            'prepared_for'  => $meta['prepared_for'],
            'target'        => $meta['target'],
            'date_from'     => $meta['date_from'] ?: now()->toDateString(),
            'date_to'       => $meta['date_to'] ?: now()->toDateString(),
            'period_label'  => $meta['period_label'],
            'click_type'    => in_array(($m['click_type'] ?? 'valid'), ['valid', 'all']) ? $m['click_type'] : 'valid',
            'total'         => $total,
            'os_data'       => $os,
            'geo_data'      => $geo,
            'split_data'    => $split ?: null,
            'rate'          => $meta['rate'],
            'payment_terms' => $meta['payment_terms'],
            'tracking_code' => $meta['tracking_code'],
        ]);

        return $this->renderReport($report);
    }

    /** Re-open a saved report. */
    public function show(TrafficReport $trafficReport)
    {
        return $this->renderReport($trafficReport);
    }

    /** Delete a saved report. */
    public function destroy(TrafficReport $trafficReport)
    {
        $no = $trafficReport->report_no;
        $trafficReport->delete();
        return redirect()->route('admin.traffic-reports.index')
            ->with('success', "Report {$no} deleted.");
    }

    /** Render the print-ready report from a saved record. */
    private function renderReport(TrafficReport $report)
    {
        $total = (int) $report->total;
        $os    = $report->os_data ?: [];
        $geo   = $report->geo_data ?: [];
        $split = $report->split_data ?: [];

        $meta = [
            'title'         => $report->title,
            'show_branding' => (bool) $report->show_branding,
            'prepared_for'  => $report->prepared_for,
            'date_from'     => $report->date_from->toDateString(),
            'date_to'       => $report->date_to->toDateString(),
            'period_label'  => $report->period_label,
            'target'        => $report->target,
            'rate'          => $report->rate,
            'payment_terms' => $report->payment_terms,
            'tracking_code' => $report->tracking_code,
        ];

        $reportNo    = $report->report_no;
        $generatedAt = $report->created_at->format('M d, Y H:i');
        $logoUrl     = 'https://installsbank.com/images/installs-bank.webp';
        $stampUrl    = 'https://installsbank.com/images/report-stamp.png';

        return view('admin.traffic-reports.report', compact(
            'total', 'os', 'geo', 'split', 'meta', 'reportNo', 'generatedAt', 'logoUrl', 'stampUrl'
        ));
    }
}
