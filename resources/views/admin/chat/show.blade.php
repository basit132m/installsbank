@extends('layouts.admin')
@section('title', 'Chat — ' . $supportTicket->user->name)
@section('page-title', 'Live Chat')

@push('styles')
<style>
.admin-chat-wrap {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
.admin-chat-main {
    flex: 1;
    min-width: 0;
}
.admin-chat-sidebar {
    width: 260px;
    flex-shrink: 0;
}
.chat-messages-box {
    height: 480px;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #f3f4f6;
}
.msg-row {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}
.msg-row.mine {
    flex-direction: row-reverse;
}
.msg-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    color: #374151;
    flex-shrink: 0;
}
.msg-avatar.staff {
    background: #01BF63;
    color: #fff;
}
.msg-bubble {
    max-width: 70%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.5;
    word-break: break-word;
}
.msg-row.mine .msg-bubble {
    background: #01BF63;
    color: #fff;
    border-bottom-right-radius: 4px;
}
.msg-row:not(.mine) .msg-bubble {
    background: #fff;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
}
.msg-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 3px;
    text-align: right;
}
.msg-row:not(.mine) .msg-time {
    text-align: left;
}
.chat-input-row {
    display: flex;
    gap: 10px;
    margin-top: 16px;
    align-items: flex-end;
}
.chat-input-row textarea {
    flex: 1;
    border: 1.5px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 14px;
    resize: none;
    outline: none;
    font-family: inherit;
    line-height: 1.5;
}
.chat-input-row textarea:focus {
    border-color: #01BF63;
}
</style>
@endpush

@section('content')
<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('admin.chat.index') }}" class="btn btn-ghost btn-sm">← Back to Chats</a>
    <span class="badge {{ match($supportTicket->status) { 'open' => 'badge-success', 'replied' => 'badge-info', 'closed' => 'badge-gray', default => 'badge-info' } }}">
        {{ ucfirst($supportTicket->status) }}
    </span>
    <div style="display:flex;gap:8px;margin-left:auto;">
        @if($supportTicket->status !== 'closed')
        <form method="POST" action="{{ route('admin.chat.close', $supportTicket) }}">
            @csrf
            <button class="btn btn-ghost btn-sm" style="color:#6b7280;" onclick="return confirm('Close this chat?')">Close Chat</button>
        </form>
        @endif
        <form method="POST" action="{{ route('admin.chat.destroy', $supportTicket) }}"
              onsubmit="return confirm('Permanently delete this chat and all messages?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Delete Chat</button>
        </form>
    </div>
</div>

<div class="admin-chat-wrap">
    <!-- Chat Area -->
    <div class="admin-chat-main">
        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:#01BF63;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:16px;">
                    {{ strtoupper(substr($supportTicket->user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:700;font-size:15px;">{{ $supportTicket->user->name }}</div>
                    <div style="font-size:12px;color:#9ca3af;">{{ $supportTicket->user->email }}</div>
                </div>
                <div id="pollingStatus" style="margin-left:auto;font-size:11px;color:#9ca3af;">Live</div>
            </div>

            <div style="padding:20px;">
                <div id="chatMessages" class="chat-messages-box">
                    @foreach($supportTicket->messages as $msg)
                    @php $isStaff = (bool) $msg->is_staff; @endphp
                    <div class="msg-row {{ $isStaff ? 'mine' : '' }}" data-id="{{ $msg->id }}">
                        <div class="msg-avatar {{ $isStaff ? 'staff' : '' }}">
                            {{ $isStaff ? strtoupper(substr($msg->sender?->name ?? 'S', 0, 1)) : strtoupper(substr($supportTicket->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="msg-bubble">{{ $msg->message }}</div>
                            <div class="msg-time">{{ $msg->created_at->format('H:i') }} · {{ $msg->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($supportTicket->status !== 'closed')
                <div class="chat-input-row">
                    <textarea id="replyInput" rows="2" placeholder="Type your reply…" onkeydown="handleKey(event)"></textarea>
                    <button class="btn btn-primary" onclick="sendReply()" style="height:48px;padding:0 20px;">Send</button>
                </div>
                @else
                <div style="text-align:center;padding:16px;color:#9ca3af;font-size:13px;margin-top:12px;">This chat is closed.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="admin-chat-sidebar">
        <div class="card">
            <div class="card-title mb-3">Publisher</div>
            <table style="width:100%;font-size:13px;">
                <tr><td style="color:#9ca3af;padding:5px 0;width:45%;">Name</td><td style="font-weight:600;">{{ $supportTicket->user->name }}</td></tr>
                <tr><td style="color:#9ca3af;padding:5px 0;">Email</td><td>{{ $supportTicket->user->email }}</td></tr>
                <tr><td style="color:#9ca3af;padding:5px 0;">Status</td><td><span class="badge badge-sm {{ $supportTicket->user->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($supportTicket->user->status) }}</span></td></tr>
                <tr><td style="color:#9ca3af;padding:5px 0;">Started</td><td>{{ $supportTicket->created_at->format('M d, H:i') }}</td></tr>
            </table>
            <div style="margin-top:12px;">
                <a href="{{ route('admin.publishers.show', $supportTicket->user) }}" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;">View Publisher</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ticketId = {{ $supportTicket->id }};
let lastId = {{ $supportTicket->messages->last()?->id ?? 0 }};
const adminInitial = '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}';

// Scroll to bottom on load
const box = document.getElementById('chatMessages');
if (box) box.scrollTop = box.scrollHeight;

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function appendMsg(m) {
    const isStaff = m.is_staff;
    const initial = isStaff ? adminInitial : '{{ strtoupper(substr($supportTicket->user->name, 0, 1)) }}';
    const html = `<div class="msg-row ${isStaff ? 'mine' : ''}" data-id="${m.id}">
        <div class="msg-avatar ${isStaff ? 'staff' : ''}">${escHtml(initial)}</div>
        <div>
            <div class="msg-bubble">${escHtml(m.message)}</div>
            <div class="msg-time">${escHtml(m.time)}</div>
        </div>
    </div>`;
    box.insertAdjacentHTML('beforeend', html);
    box.scrollTop = box.scrollHeight;
    lastId = m.id;
}

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

function pollMessages() {
    fetch('{{ route("admin.chat.messages", $supportTicket) }}', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        let hasNew = false;
        data.messages.forEach(m => {
            if (m.id > lastId) {
                appendMsg(m);
                if (!m.is_staff) hasNew = true; // publisher sent message
            }
        });
        if (hasNew) playPing();
    })
    .catch(() => {});
}

// Poll every 5 seconds
setInterval(pollMessages, 5000);

function sendReply() {
    const input = document.getElementById('replyInput');
    const msg = input.value.trim();
    if (!msg) return;
    input.value = '';
    input.style.height = '';

    fetch('{{ route("admin.chat.reply", $supportTicket) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message: msg })
    })
    .then(r => r.json())
    .then(m => appendMsg(m))
    .catch(() => {});
}

function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendReply();
    }
}
</script>
@endpush
