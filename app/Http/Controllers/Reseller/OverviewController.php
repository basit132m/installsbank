<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\PublisherWebsite;
use App\Models\TrackingLink;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $period = $request->get('period', 'today');
        [$dateFrom, $dateTo, $label] = match ($period) {
            'yesterday' => [today()->subDay(),     today()->subDay(), 'Yesterday'],
            '7days'     => [today()->subDays(6),   today(),           'Last 7 Days'],
            '28days'    => [today()->subDays(27),  today(),           'Last 28 Days'],
            default     => [today(),               today(),           'Today'],
        };

        $divider         = $user->clickDivider;
        $dividerValue    = ($divider && $divider->is_enabled)          ? max(1, (float)$divider->divider_value)     : 1;
        $macDividerValue = ($divider && $divider->mac_divider_enabled) ? max(1, (float)$divider->mac_divider_value) : 1;

        // Per tracking-link OS counts (raw), then apply dividers to Windows & Mac
        $raw = Click::where('user_id', $user->id)
            ->where('is_counted', true)
            ->whereBetween('created_at', [$dateFrom->copy()->startOfDay(), $dateTo->copy()->endOfDay()])
            ->selectRaw("tracking_link_id,
                SUM(CASE WHEN is_windows = 1 THEN 1 ELSE 0 END) as raw_windows,
                SUM(CASE WHEN is_mac = 1 THEN 1 ELSE 0 END) as raw_mac,
                SUM(CASE WHEN is_windows = 0 AND is_mac = 0 AND LOWER(os) LIKE '%android%' THEN 1 ELSE 0 END) as raw_android,
                SUM(CASE WHEN is_windows = 0 AND is_mac = 0 AND LOWER(os) NOT LIKE '%android%' THEN 1 ELSE 0 END) as raw_other")
            ->groupBy('tracking_link_id')
            ->get()
            ->keyBy('tracking_link_id');

        // Map every tracking link to its website domain (nicer label)
        $links      = TrackingLink::where('user_id', $user->id)->get()->keyBy('id');
        $siteByLink = PublisherWebsite::where('user_id', $user->id)
            ->whereNotNull('tracking_link_id')
            ->get()
            ->keyBy('tracking_link_id');

        $rows = $raw->map(function ($r) use ($links, $siteByLink, $dividerValue, $macDividerValue) {
            $link    = $r->tracking_link_id ? ($links[$r->tracking_link_id] ?? null) : null;
            $site    = $r->tracking_link_id ? ($siteByLink[$r->tracking_link_id] ?? null) : null;

            $windows = (int) floor((int)$r->raw_windows / $dividerValue);
            $mac     = (int) floor((int)$r->raw_mac / $macDividerValue);
            $android = (int) $r->raw_android;
            $other   = (int) $r->raw_other;
            $total   = $windows + $android + $mac + $other;

            $name = $site->domain
                ?? ($link->name ?: ($link ? ('Link #' . $link->id) : 'Deleted link'));

            return [
                'name'    => $name,
                'code'    => $link?->unique_code,
                'active'  => $link?->is_active ?? false,
                'windows' => $windows,
                'android' => $android,
                'mac'     => $mac,
                'other'   => $other,
                'total'   => $total,
            ];
        })
        ->filter(fn($r) => $r['total'] > 0)
        ->sortByDesc('total')
        ->values();

        $totals = [
            'windows' => $rows->sum('windows'),
            'android' => $rows->sum('android'),
            'mac'     => $rows->sum('mac'),
            'other'   => $rows->sum('other'),
            'total'   => $rows->sum('total'),
        ];

        return view('reseller.overview', compact('rows', 'totals', 'period', 'label', 'dateFrom', 'dateTo'));
    }
}
