@extends('layouts.admin')
@section('title', 'Clicks Cleanup')
@section('page-title', 'Clicks Data Cleanup')

@section('content')
<div style="max-width:820px;">

    <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:16px 18px;margin-bottom:22px;">
        <div style="font-size:14px;font-weight:800;color:#92400e;margin-bottom:6px;">⚠ Permanent deletion</div>
        <div style="font-size:13px;color:#92400e;line-height:1.7;">
            Deleting a month removes its <strong>raw click log rows</strong> and that month's <strong>fraud alerts</strong> to free up space.
            <strong>Earnings, publisher balances, daily totals and withdrawals are NOT affected</strong> — only the detailed per-click records are removed.
            This cannot be undone.
        </div>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
            <div style="font-size:15px;font-weight:700;color:#111827;">Clicks by Month</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Oldest data can be purged safely. The current month is protected.</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th style="text-align:right;">Clicks</th>
                    <th>Range</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($months as $m)
                <tr>
                    <td style="font-weight:700;">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $m->ym.'-01')->format('F Y') }}</td>
                    <td style="text-align:right;font-weight:700;color:#374151;">{{ number_format($m->cnt) }}</td>
                    <td style="font-size:12px;color:#6b7280;">
                        {{ \Carbon\Carbon::parse($m->first_at)->format('M d') }} – {{ \Carbon\Carbon::parse($m->last_at)->format('M d, Y') }}
                    </td>
                    <td style="text-align:right;">
                        @if($m->ym === $currentMonth)
                            <span class="badge badge-gray">Current month</span>
                        @else
                            <form method="POST" action="{{ route('admin.clicks-cleanup.destroy', $m->ym) }}" style="display:inline;margin:0;"
                                  onsubmit="return confirm('Delete ALL {{ number_format($m->cnt) }} clicks for {{ \Carbon\Carbon::createFromFormat('Y-m-d', $m->ym.'-01')->format('F Y') }}?\n\nThis permanently removes the raw click log and fraud alerts for that month. Earnings and balances are NOT affected. This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete Month
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:40px;color:#9ca3af;">No click data found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="font-size:12px;color:#9ca3af;margin-top:12px;">
        Large months are deleted in batches. If a month still shows remaining clicks after deleting, just click <strong>Delete Month</strong> again to continue.
    </div>
</div>
@endsection
