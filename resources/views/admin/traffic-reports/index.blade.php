@extends('layouts.admin')
@section('title', 'Traffic Reports')
@section('page-title', 'Traffic Reports')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <div style="font-size:13px;color:#6b7280;">All generated traffic testing reports are saved here. Open one to re-print, or delete it.</div>
    <a href="{{ route('admin.traffic-reports.create') }}" class="btn btn-primary btn-sm">+ New Report</a>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table>
        <thead>
            <tr>
                <th>Report No.</th>
                <th>Title / Source</th>
                <th>Period</th>
                <th style="text-align:right;">Total Clicks</th>
                <th>Generated</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $r)
            <tr>
                <td><code style="background:#f3f4f6;padding:2px 8px;border-radius:4px;font-size:12px;">{{ $r->report_no }}</code></td>
                <td>
                    <div style="font-weight:700;">{{ $r->title }}</div>
                    <div style="font-size:12px;color:#9ca3af;">{{ $r->target }}{{ $r->prepared_for ? ' · for '.$r->prepared_for : '' }}</div>
                </td>
                <td style="font-size:13px;color:#6b7280;white-space:nowrap;">
                    {{ $r->date_from->format('M d, Y') }} – {{ $r->date_to->format('M d, Y') }}
                    <div style="font-size:11px;color:#9ca3af;">{{ ucfirst($r->click_type) }} clicks</div>
                </td>
                <td style="text-align:right;font-weight:800;color:#01BF63;">{{ number_format($r->total) }}</td>
                <td style="font-size:12px;color:#6b7280;white-space:nowrap;">
                    {{ $r->created_at->format('M d, Y H:i') }}
                    @if($r->generatedBy)<div style="font-size:11px;color:#9ca3af;">by {{ $r->generatedBy->name }}</div>@endif
                </td>
                <td style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('admin.traffic-reports.show', $r) }}" target="_blank" class="btn btn-ghost btn-sm">Open / PDF</a>
                    <form method="POST" action="{{ route('admin.traffic-reports.destroy', $r) }}" style="display:inline;margin:0;"
                          onsubmit="return confirm('Delete report {{ $r->report_no }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:7px;cursor:pointer;vertical-align:middle;" title="Delete">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:44px;color:#9ca3af;">
                <div style="font-size:36px;margin-bottom:8px;">📄</div>
                No reports generated yet. <a href="{{ route('admin.traffic-reports.create') }}" style="color:#3b82f6;font-weight:600;">Create your first report →</a>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $reports->links() }}</div>
@endsection
