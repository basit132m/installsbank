@extends('layouts.admin')
@section('title', 'Resellers')
@section('page-title', 'Resellers')

@section('content')

<!-- Summary strip -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:24px;">
    <div style="background:#f5f3ff;border:1.5px solid #ddd6fe;border-radius:12px;padding:16px 18px;">
        <div style="font-size:24px;font-weight:900;color:#7c3aed;">{{ $resellers->total() }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:2px;">Total Resellers</div>
    </div>
    <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:16px 18px;">
        <div style="font-size:24px;font-weight:900;color:#d97706;">{{ $pendingCount }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:2px;">Pending Approval</div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4" style="padding:16px 20px;">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
               placeholder="Search name or email..." style="max-width:260px;">
        <select name="status" class="form-control form-select" style="max-width:170px;">
            <option value="">All Statuses</option>
            <option value="pending"   {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="active"    {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.resellers.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table>
        <thead>
            <tr>
                <th>Reseller</th>
                <th>Website</th>
                <th style="text-align:right;">Clicks Today</th>
                <th style="text-align:center;">Status</th>
                <th>Joined</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resellers as $r)
            <tr>
                <td>
                    <div style="font-weight:700;">{{ $r->name }}</div>
                    <div style="font-size:12px;color:#9ca3af;">{{ $r->email }}</div>
                </td>
                <td>
                    @if($r->website)
                    <a href="{{ $r->website }}" target="_blank" style="color:#3b82f6;font-size:13px;">{{ parse_url($r->website, PHP_URL_HOST) ?? $r->website }}</a>
                    @else
                    <span style="color:#9ca3af;">—</span>
                    @endif
                </td>
                <td style="text-align:right;font-weight:700;color:#7c3aed;">{{ number_format($todayClicks[$r->id] ?? 0) }}</td>
                <td style="text-align:center;">
                    @if($r->status === 'active')<span class="badge badge-success">Active</span>
                    @elseif($r->status === 'pending')<span class="badge badge-warning">Pending</span>
                    @else<span class="badge badge-danger">Suspended</span>@endif
                </td>
                <td style="font-size:13px;color:#6b7280;">{{ $r->created_at->format('M d, Y') }}</td>
                <td style="text-align:right;">
                    <a href="{{ route('admin.resellers.show', $r) }}" class="btn btn-ghost btn-sm">View →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">No resellers yet. They can sign up at <code>/register/reseller</code></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $resellers->withQueryString()->links() }}</div>
@endsection
