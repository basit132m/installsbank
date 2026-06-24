@extends('layouts.admin')
@section('title', 'Publisher Overview')
@section('page-title', 'Publisher Overview')

@section('content')

{{-- Period tabs --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
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

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    @foreach([
        ['label'=>'Total Valid Clicks','value'=>number_format($totals['valid']),'color'=>'#2563eb','bg'=>'#eff6ff'],
        ['label'=>'Windows Clicks','value'=>number_format($totals['windows']),'color'=>'#7c3aed','bg'=>'#f5f3ff'],
        ['label'=>'Other OS Clicks','value'=>number_format($totals['other']),'color'=>'#0891b2','bg'=>'#ecfeff'],
        ['label'=>'Total Earnings','value'=>'$'.number_format($totals['earnings'],2),'color'=>'#16a34a','bg'=>'#f0fdf4'],
    ] as $card)
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;">
        <div style="font-size:12px;font-weight:600;color:#6b7280;margin-bottom:6px;">{{ $card['label'] }}</div>
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
                    <th style="width:40px;">#</th>
                    <th>Publisher</th>
                    <th style="text-align:right;">Windows Clicks</th>
                    <th style="text-align:right;">Other OS Clicks</th>
                    <th style="text-align:right;">Total Valid Clicks</th>
                    <th style="text-align:right;">Earnings</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                @php
                    $user = $row->user;
                    $winPct   = $row->total_valid > 0 ? round(($row->total_windows / $row->total_valid) * 100) : 0;
                    $otherPct = 100 - $winPct;
                @endphp
                <tr>
                    <td style="color:#9ca3af;font-size:12px;">{{ $i + 1 }}</td>
                    <td>
                        @if($user)
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#374151;flex-shrink:0;">
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
                        <span style="color:#9ca3af;font-size:12px;">Deleted publisher</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="font-weight:700;color:#7c3aed;">{{ number_format($row->total_windows) }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $winPct }}% of valid</div>
                    </td>
                    <td style="text-align:right;">
                        <div style="font-weight:700;color:#0891b2;">{{ number_format($row->total_other) }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $otherPct }}% of valid</div>
                    </td>
                    <td style="text-align:right;">
                        <div style="font-weight:800;font-size:15px;color:#111827;">{{ number_format($row->total_valid) }}</div>
                        {{-- mini bar --}}
                        <div style="display:flex;gap:1px;margin-top:4px;height:4px;border-radius:4px;overflow:hidden;width:80px;margin-left:auto;">
                            <div style="width:{{ $winPct }}%;background:#7c3aed;"></div>
                            <div style="width:{{ $otherPct }}%;background:#0891b2;"></div>
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <span style="font-weight:700;color:#16a34a;">${{ number_format($row->total_earnings,2) }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($user)
                        <a href="{{ route('admin.publishers.show', $user) }}" class="btn btn-ghost btn-sm">View</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:#9ca3af;">
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
                    <td style="text-align:right;color:#0891b2;">{{ number_format($totals['other']) }}</td>
                    <td style="text-align:right;color:#111827;">{{ number_format($totals['valid']) }}</td>
                    <td style="text-align:right;color:#16a34a;">${{ number_format($totals['earnings'],2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
