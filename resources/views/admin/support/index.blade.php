@extends('layouts.admin')
@section('title', 'Support')
@section('page-title', 'Support Tickets')

@section('content')
<div class="card mb-4">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;">
        <div><label class="form-label">Status</label>
            <select name="status" class="form-control form-select">
                <option value="">All</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Publisher</th><th>Subject</th><th>Priority</th><th>Status</th><th>Last Update</th><th></th></tr></thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td style="font-weight:600;">{{ $ticket->user->name }}</td>
                    <td>{{ $ticket->subject }}</td>
                    <td><span class="badge {{ ['low'=>'badge-gray','medium'=>'badge-info','high'=>'badge-danger'][$ticket->priority] }}">{{ ucfirst($ticket->priority) }}</span></td>
                    <td><span class="badge {{ ['open'=>'badge-success','replied'=>'badge-info','closed'=>'badge-gray'][$ticket->status] }}">{{ ucfirst($ticket->status) }}</span></td>
                    <td style="color:#9ca3af;font-size:13px;">{{ ($ticket->last_reply_at ?? $ticket->created_at)->diffForHumans() }}</td>
                    <td><a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-primary btn-sm">Reply</a></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:24px;color:#9ca3af;">No tickets</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $tickets->links() }}
</div>
@endsection
