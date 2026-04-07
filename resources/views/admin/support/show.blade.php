@extends('layouts.admin')
@section('title', 'Ticket')
@section('page-title', $supportTicket->subject)

@section('content')
<div style="max-width:700px;">
    <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('admin.support.index') }}" class="btn btn-ghost btn-sm">← Back</a>
        <span class="badge {{ ['open'=>'badge-success','replied'=>'badge-info','closed'=>'badge-gray'][$supportTicket->status] ?? 'badge-gray' }}">{{ ucfirst($supportTicket->status) }}</span>
        <span style="font-size:13px;color:#6b7280;margin-left:4px;">from {{ $supportTicket->user->name }}</span>
        <div style="margin-left:auto;display:flex;gap:8px;">
            @if($supportTicket->status !== 'closed')
                <form method="POST" action="{{ route('admin.support.close', $supportTicket) }}">@csrf<button class="btn btn-ghost btn-sm">Close Ticket</button></form>
            @endif
            <form method="POST" action="{{ route('admin.support.destroy', $supportTicket) }}"
                  onsubmit="return confirm('Delete this ticket and all messages?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>
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

@push('scripts')
<script>
// Play a sound when a new reply arrives on this ticket
let lastMsgCount = {{ $supportTicket->messages->count() }};

function playPing() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine'; osc.frequency.value = 820;
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
        osc.start(); osc.stop(ctx.currentTime + 0.6);
    } catch(e) {}
}

function pollTicket() {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.text())
    .then(html => {
        const matches = html.match(/border-bottom:1px solid #f3f4f6/g);
        const count = matches ? matches.length : 0;
        if (count > lastMsgCount) {
            playPing();
            // Reload so new messages appear
            window.location.reload();
        }
    }).catch(() => {});
}

@if($supportTicket->status !== 'closed')
setInterval(pollTicket, 10000);
@endif
</script>
@endpush
