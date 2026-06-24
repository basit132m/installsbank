@extends('layouts.admin')
@section('title', 'Publisher Overview')
@section('page-title', 'Publisher Overview')

@section('content')

{{-- Period tabs + date --}}
@php
    $dateLabel = $dateFrom->isSameDay($dateTo)
        ? $dateFrom->format('D, M j, Y')
        : $dateFrom->format('M j') . ' – ' . $dateTo->format('M j, Y');
@endphp

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;gap:6px;">
        @foreach(['today'=>'Today','yesterday'=>'Yesterday','7days'=>'Last 7 Days','28days'=>'Last 28 Days'] as $key=>$lbl)
            <a href="?period={{ $key }}"
               style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;
                      {{ $period===$key ? 'background:#111827;color:#fff;' : 'background:#f3f4f6;color:#374151;' }}">
                {{ $lbl }}
            </a>
        @endforeach
    </div>
    <div style="font-size:13px;color:#6b7280;">
        Showing <strong>{{ $rows->count() }}</strong> publisher(s) with activity
    </div>
</div>
<div style="margin-bottom:18px;">
    <span style="font-size:13px;color:#6b7280;">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="vertical-align:-2px;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        {{ $dateLabel }}
    </span>
</div>

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;">
    @foreach([
        ['label'=>'Total Clicks','value'=>number_format($totals['total']),'color'=>'#2563eb'],
        ['label'=>'Windows','value'=>number_format($totals['windows']),'color'=>'#7c3aed'],
        ['label'=>'Android','value'=>number_format($totals['android']),'color'=>'#16a34a'],
        ['label'=>'Mac / iOS','value'=>number_format($totals['mac']),'color'=>'#0891b2'],
        ['label'=>'Other OS','value'=>number_format($totals['other']),'color'=>'#d97706'],
    ] as $card)
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;">
        <div style="font-size:11px;font-weight:600;color:#9ca3af;margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em;">{{ $card['label'] }}</div>
        <div style="font-size:22px;font-weight:800;color:{{ $card['color'] }};">{{ $card['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Publisher</th>
                    <th style="text-align:right;">
                        <span style="color:#7c3aed;">Windows</span>
                    </th>
                    <th style="text-align:right;">
                        <span style="color:#16a34a;">Android</span>
                    </th>
                    <th style="text-align:right;">
                        <span style="color:#0891b2;">Mac / iOS</span>
                    </th>
                    <th style="text-align:right;">
                        <span style="color:#d97706;">Other</span>
                    </th>
                    <th style="text-align:right;">Total Clicks</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                @php
                    $user  = $row['user'];
                    $total = $row['total'];
                    $winPct     = $total > 0 ? round(($row['windows'] / $total) * 100) : 0;
                    $andPct     = $total > 0 ? round(($row['android'] / $total) * 100) : 0;
                    $macPct     = $total > 0 ? round(($row['mac']     / $total) * 100) : 0;
                    $othPct     = $total > 0 ? round(($row['other']   / $total) * 100) : 0;
                @endphp
                <tr>
                    <td style="color:#9ca3af;font-size:12px;">{{ $i + 1 }}</td>
                    <td>
                        @if($user)
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#374151;flex-shrink:0;">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:#111827;">
                                    <a href="{{ route('admin.publishers.show', $user) }}" style="text-decoration:none;color:inherit;">{{ $user->name }}</a>
                                </div>
                                <div style="font-size:11px;color:#9ca3af;">{{ $user->email }}</div>
                            </div>
                        </div>
                        @else
                            <span style="color:#9ca3af;font-size:12px;">Deleted</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="font-weight:700;color:#7c3aed;">{{ number_format($row['windows']) }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $winPct }}%</div>
                    </td>
                    <td style="text-align:right;">
                        <div style="font-weight:700;color:#16a34a;">{{ number_format($row['android']) }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $andPct }}%</div>
                    </td>
                    <td style="text-align:right;">
                        <div style="font-weight:700;color:#0891b2;">{{ number_format($row['mac']) }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $macPct }}%</div>
                    </td>
                    <td style="text-align:right;">
                        <div style="font-weight:700;color:#d97706;">{{ number_format($row['other']) }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $othPct }}%</div>
                    </td>
                    <td style="text-align:right;">
                        <div style="font-weight:800;font-size:15px;color:#111827;">{{ number_format($total) }}</div>
                        {{-- OS split bar --}}
                        <div style="display:flex;gap:1px;margin-top:4px;height:3px;border-radius:4px;overflow:hidden;width:80px;margin-left:auto;">
                            <div style="width:{{ $winPct }}%;background:#7c3aed;"></div>
                            <div style="width:{{ $andPct }}%;background:#16a34a;"></div>
                            <div style="width:{{ $macPct }}%;background:#0891b2;"></div>
                            <div style="width:{{ $othPct }}%;background:#d97706;"></div>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        @if($user)
                        <a href="{{ route('admin.publishers.show', $user) }}" class="btn btn-ghost btn-sm">View</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:#9ca3af;">
                        No publisher activity found for {{ $label }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($rows->count() > 0)
            <tfoot>
                <tr style="background:#f9fafb;font-weight:800;">
                    <td colspan="2" style="font-size:13px;color:#374151;">Total</td>
                    <td style="text-align:right;color:#7c3aed;">{{ number_format($totals['windows']) }}</td>
                    <td style="text-align:right;color:#16a34a;">{{ number_format($totals['android']) }}</td>
                    <td style="text-align:right;color:#0891b2;">{{ number_format($totals['mac']) }}</td>
                    <td style="text-align:right;color:#d97706;">{{ number_format($totals['other']) }}</td>
                    <td style="text-align:right;color:#111827;">{{ number_format($totals['total']) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
