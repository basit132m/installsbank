@extends('layouts.publisher')
@section('title', 'Support')
@section('page-title', 'Support')

@section('content')
<div class="flex-between mb-6">
    <p class="text-muted text-sm">View and manage your support tickets</p>
    <a href="{{ route('publisher.support.create') }}" class="btn btn-primary">New Ticket</a>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Subject</th><th>Priority</th><th>Status</th><th>Last Update</th><th></th></tr></thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td><strong>{{ $ticket->subject }}</strong></td>
                    <td><span class="badge {{ ['low'=>'badge-gray','medium'=>'badge-info','high'=>'badge-danger'][$ticket->priority] }}">{{ ucfirst($ticket->priority) }}</span></td>
                    <td><span class="badge {{ ['open'=>'badge-success','replied'=>'badge-info','closed'=>'badge-gray'][$ticket->status] }}">{{ ucfirst($ticket->status) }}</span></td>
                    <td style="color:#9ca3af;font-size:13px;">{{ ($ticket->last_reply_at ?? $ticket->created_at)->diffForHumans() }}</td>
                    <td><a href="{{ route('publisher.support.show', $ticket) }}" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:24px;color:#9ca3af;">No tickets yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $tickets->links() }}
</div>
@endsection
