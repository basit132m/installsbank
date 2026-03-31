@extends('layouts.admin')
@section('title', 'Ticket')
@section('page-title', $supportTicket->subject)

@section('content')
<div style="max-width:700px;">
    <div style="display:flex;gap:8px;margin-bottom:20px;">
        <a href="{{ route('admin.support.index') }}" class="btn btn-ghost btn-sm">← Back</a>
        <span class="badge {{ ['open'=>'badge-success','replied'=>'badge-info','closed'=>'badge-gray'][$supportTicket->status] }}">{{ ucfirst($supportTicket->status) }}</span>
        <span style="font-size:13px;color:#6b7280;margin-left:4px;">from {{ $supportTicket->user->name }}</span>
        @if($supportTicket->status !== 'closed')
            <form method="POST" action="{{ route('admin.support.close', $supportTicket) }}" style="margin-left:auto;">@csrf<button class="btn btn-ghost btn-sm">Close Ticket</button></form>
        @endif
    </div>
    <div class="card mb-4" style="padding:0;overflow:hidden;">
        @foreach($supportTicket->messages as $msg)
        <div style="padding:20px;border-bottom:1px solid #f3f4f6;background:{{ $msg->user_id === $supportTicket->user_id ? 'white' : '#f0fdf7' }};">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <div style="font-size:13px;font-weight:600;color:{{ $msg->user_id !== $supportTicket->user_id ? '#065f46' : '#111827' }};">
                    {{ $msg->sender->name }} {{ $msg->user_id !== $supportTicket->user_id ? '(Admin/Manager)' : '(Publisher)' }}
                </div>
                <div style="font-size:12px;color:#9ca3af;">{{ $msg->created_at->format('M d, Y H:i') }}</div>
            </div>
            <div style="font-size:14px;white-space:pre-wrap;">{{ $msg->message }}</div>
        </div>
        @endforeach
    </div>
    @if($supportTicket->status !== 'closed')
    <div class="card">
        <form method="POST" action="{{ route('admin.support.reply', $supportTicket) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Reply</label>
                <textarea name="message" class="form-control" rows="4" required maxlength="2000" placeholder="Type your reply..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Reply</button>
        </form>
    </div>
    @endif
</div>
@endsection
