@extends('layouts.admin')
@section('title', 'Live Chat')
@section('page-title', 'Live Chat')

@section('content')
@if(session('success'))
<div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <div class="card-title">Publisher Live Chats</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Messages from the live chat widget in the publisher panel</div>
        </div>
        @if($unreadCount > 0)
        <span style="background:#ef4444;color:#fff;font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;">{{ $unreadCount }} open</span>
        @endif
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Publisher</th>
                    <th>Started</th>
                    <th>Last Message</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($chats as $chat)
                <tr style="{{ $chat->status === 'open' ? 'background:#f0fdf4;' : '' }}">
                    <td>
                        <div style="font-weight:600;">{{ $chat->user->name }}</div>
                        <div style="font-size:12px;color:#9ca3af;">{{ $chat->user->email }}</div>
                    </td>
                    <td style="font-size:13px;color:#6b7280;">{{ $chat->created_at->format('M d, Y · H:i') }}</td>
                    <td style="font-size:13px;color:#6b7280;">
                        @if($chat->latestMessage)
                            <span style="color:#374151;">{{ Str::limit($chat->latestMessage->message, 50) }}</span>
                            <div style="font-size:11px;color:#9ca3af;">{{ $chat->latestMessage->created_at->diffForHumans() }}</div>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ match($chat->status) { 'open' => 'badge-success', 'replied' => 'badge-info', 'closed' => 'badge-gray', default => 'badge-info' } }}">
                            {{ ucfirst($chat->status) }}
                        </span>
                    </td>
                    <td style="display:flex;gap:6px;align-items:center;">
                        <a href="{{ route('admin.chat.show', $chat) }}" class="btn btn-primary btn-sm">Open</a>
                        <form method="POST" action="{{ route('admin.chat.destroy', $chat) }}"
                              onsubmit="return confirm('Delete this chat and all its messages?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:32px;color:#9ca3af;">No live chat conversations yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $chats->links() }}
</div>
@endsection

@push('scripts')
<script>
// Play a soft ping when new open chats appear
let knownOpenCount = {{ $unreadCount }};
function checkNewChats() {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.text())
    .then(html => {
        const m = html.match(/"open"\s+chats.*?(\d+)\s+open/);
        if (!m) return;
        const cur = parseInt(m[1]);
        if (cur > knownOpenCount) {
            playPing();
            knownOpenCount = cur;
        }
    }).catch(() => {});
}
// Poll count badge via dedicated JSON endpoint (see below)
function pingCount() {
    fetch('{{ route("admin.chat.index") }}?count=1', { headers: { 'Accept': 'application/json' } })
    .then(r => r.ok ? r.json() : null)
    .then(d => {
        if (!d) return;
        if (d.open > knownOpenCount) { playPing(); knownOpenCount = d.open; }
    }).catch(() => {});
}

function playPing() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine'; osc.frequency.value = 880;
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
        osc.start(); osc.stop(ctx.currentTime + 0.5);
    } catch(e) {}
}
setInterval(pingCount, 8000);
</script>
@endpush
