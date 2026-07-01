@extends('layouts.admin')
@section('title', 'Windows Redirect History')
@section('page-title', 'Windows Redirect History')

@section('content')
<div style="max-width:900px;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <a href="{{ route('admin.tracking.edit', $trackingLink) }}" class="btn btn-ghost btn-sm">← Back to Link</a>
        <span style="font-size:13px;color:#6b7280;">
            Code: <code style="background:#f3f4f6;padding:2px 8px;border-radius:4px;">{{ $trackingLink->unique_code }}</code>
            @if($trackingLink->name) · <strong>{{ $trackingLink->name }}</strong>@endif
        </span>
    </div>

    <div class="card">
        <div class="card-title mb-1">Windows Redirect History — Last 30 Days</div>
        <div style="font-size:12px;color:#9ca3af;margin-bottom:20px;">
            How many Windows clicks each destination URL received, per day. Times are grouped by the app's calendar day.
        </div>

        @if($byDay->isEmpty())
            <div style="text-align:center;padding:40px;color:#9ca3af;font-size:14px;">
                No Windows redirect data recorded yet in the last 30 days.
            </div>
        @else
            @foreach($byDay as $day => $rows)
            @php
                $dayTotal   = $rows->sum('total');
                $dayCounted = $rows->sum('counted');
            @endphp
            <div style="margin-bottom:18px;border:1px solid #f3f4f6;border-radius:10px;overflow:hidden;">
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f9fafb;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;font-weight:800;color:#111827;">{{ \Carbon\Carbon::parse($day)->format('D, M d, Y') }}</span>
                    @if(\Carbon\Carbon::parse($day)->isToday())
                    <span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;">TODAY</span>
                    @endif
                    <span style="margin-left:auto;font-size:12px;color:#6b7280;"><strong>{{ number_format($dayTotal) }}</strong> clicks · {{ number_format($dayCounted) }} counted</span>
                </div>
                <table style="width:100%;font-size:13px;border-collapse:collapse;">
                    <tbody>
                        @foreach($rows->sortByDesc('total') as $row)
                        @php $label = $urlLabels[$row->redirect_url] ?? null; @endphp
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:9px 14px;">
                                @if($label)
                                <span style="background:#ede9fe;color:#7c3aed;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;margin-right:6px;">{{ $label }}</span>
                                @endif
                                <span style="color:#6b7280;font-size:12px;word-break:break-all;">{{ $row->redirect_url ?: '(no URL recorded)' }}</span>
                            </td>
                            <td style="padding:9px 14px;text-align:right;white-space:nowrap;">
                                <span style="font-weight:800;color:#059669;">{{ number_format($row->total) }}</span>
                                <span style="font-size:11px;color:#9ca3af;">/ {{ number_format($row->counted) }} counted</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
