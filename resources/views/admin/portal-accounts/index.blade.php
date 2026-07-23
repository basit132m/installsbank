@extends('layouts.admin')
@section('title', 'Dashboard Accounts')
@section('page-title', 'White-Label Dashboard Accounts')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <div style="font-size:13px;color:#6b7280;max-width:640px;">
        Isolated, unbranded dashboards for individual publishers. Each shows only <strong>Windows clicks</strong> for its assigned tracking code, with its own divider and daily min/max — completely separate from the real publisher panel. Login page: <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;">/portal/login</code>
    </div>
    <a href="{{ route('admin.portal-accounts.create') }}" class="btn btn-primary btn-sm">+ New Dashboard</a>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Tracking Code</th>
                <th>Divider</th>
                <th>Min / Max Daily</th>
                <th style="text-align:center;">Status</th>
                <th>Last Login</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $a)
            <tr>
                <td>
                    <div style="font-weight:700;">{{ $a->username }}</div>
                    @if($a->display_title)<div style="font-size:12px;color:#9ca3af;">{{ $a->display_title }}</div>@endif
                </td>
                <td>
                    @if($a->trackingLink)
                        <code style="background:#f3f4f6;padding:2px 8px;border-radius:4px;font-size:12px;">{{ $a->trackingLink->unique_code }}</code>
                        <div style="font-size:11px;color:#9ca3af;">{{ $a->trackingLink->user?->name ?? 'Unassigned' }}</div>
                    @else
                        <span style="color:#ef4444;font-size:12px;">No link assigned</span>
                    @endif
                </td>
                <td style="font-size:13px;">{{ $a->divider_enabled ? $a->divider_value.'×' : 'Off' }}</td>
                <td style="font-size:13px;color:#6b7280;">{{ $a->min_clicks !== null ? number_format($a->min_clicks) : '—' }} / {{ $a->max_clicks !== null ? number_format($a->max_clicks) : '—' }}</td>
                <td style="text-align:center;">
                    @if($a->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Disabled</span>@endif
                </td>
                <td style="font-size:12px;color:#6b7280;">{{ $a->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                <td style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('admin.portal-accounts.edit', $a) }}" class="btn btn-ghost btn-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.portal-accounts.destroy', $a) }}" style="display:inline;margin:0;"
                          onsubmit="return confirm('Delete dashboard account “{{ $a->username }}”? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:7px;cursor:pointer;vertical-align:middle;" title="Delete">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:44px;color:#9ca3af;">
                <div style="font-size:36px;margin-bottom:8px;">🖥️</div>
                No dashboard accounts yet. <a href="{{ route('admin.portal-accounts.create') }}" style="color:#3b82f6;font-weight:600;">Create one →</a>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $accounts->links() }}</div>
@endsection
