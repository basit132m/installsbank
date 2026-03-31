@extends('layouts.admin')
@section('title', 'Managers')
@section('page-title', 'Managers')

@section('content')
<div class="flex-between mb-6">
    <p class="text-muted text-sm">Manage panel managers and their permissions</p>
    <a href="{{ route('admin.managers.create') }}" class="btn btn-primary">+ Add Manager</a>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($managers as $mgr)
                <tr>
                    <td><strong>{{ $mgr->name }}</strong></td>
                    <td style="color:#6b7280;">{{ $mgr->email }}</td>
                    <td><span class="badge badge-success">Active</span></td>
                    <td style="color:#9ca3af;font-size:13px;">{{ $mgr->created_at->format('M d, Y') }}</td>
                    <td style="display:flex;gap:6px;">
                        <a href="{{ route('admin.managers.permissions', $mgr) }}" class="btn btn-ghost btn-sm">Permissions</a>
                        <form method="POST" action="{{ route('admin.managers.destroy', $mgr) }}" onsubmit="return confirm('Delete this manager?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Delete</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:24px;color:#9ca3af;">No managers yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
