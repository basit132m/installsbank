@extends('layouts.admin')
@section('title', 'Chat — ' . $supportTicket->user->name)
@section('page-title', 'Live Chat')

@push('styles')
<style>
.chat-shell {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 140px);
    min-height: 500px;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
/* ── Header ── */
.chat-head {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid #f3f4f6;
    flex-shrink: 0;
}
.chat-head-avatar {
    width: 42px; height: 42px; border-radius: 50%;
    background: #01BF63; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 700; flex-shrink: 0;
}
.chat-head-meta { flex: 1; min-width: 0; }
.chat-head-meta strong { display: block; font-size: 15px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.chat-head-meta span  { font-size: 12px; color: #9ca3af; }
.chat-head-actions { display: flex; gap: 8px; flex-shrink: 0; }

/* ── Messages ── */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 24px 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: #f9fafb;
    scroll-behavior: smooth;
}

/* ── Row ── */
.cm-row {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    max-width: 72%;
}
.cm-row.staff {
    align-self: flex-end;
    flex-direction: row-reverse;
}
.cm-row:not(.staff) {
    align-self: flex-start;
}

/* ── Avatar ── */
.cm-av {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700;
}
.cm-av.pub  { background: #e5e7eb; color: #374151; }
.cm-av.adm  { background: #01BF63; color: #fff; }

/* ── Content (bubble + time) ── */
.cm-content {
    display: flex;
    flex-direction: column;
    min-width: 0;
    max-width: 100%;
}
.cm-row.staff .cm-content { align-items: flex-end; }
.cm-row:not(.staff) .cm-content { align-items: flex-start; }

/* ── Bubble ── */
.cm-bubble {
    padding: 10px 15px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.55;
    word-break: break-word;
    white-space: pre-wrap;
    display: block;          /* NOT inline-block — block fills cm-content width */
    width: fit-content;      /* but shrinks to text */
    max-width: 100%;
}
.cm-row:not(.staff) .cm-bubble {
    background: #fff;
    color: #111827;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
}
.cm-row.staff .cm-bubble {
    background: #01BF63;
    color: #fff;
    border-bottom-right-radius: 4px;
}
.cm-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 4px;
    padding: 0 2px;
}

/* ── System message ── */
.cm-system {
    align-self: center;
    font-size: 12px;
    color: #9ca3af;
    background: #f3f4f6;
    padding: 5px 14px;
    border-radius: 20px;
}

/* ── Input ── */
.chat-input-wrap {
    flex-shrink: 0;
    border-top: 1px solid #f3f4f6;
    padding: 14px 18px;
    background: #fff;
    display: flex;
    gap: 10px;
    align-items: flex-end;
}
.chat-input-wrap textarea {
    flex: 1;
    border: 1.5px solid #e5e7eb;
    border-radius: 14px;
    padding: 11px 16px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    outline: none;
    line-height: 1.5;
    max-height: 120px;
    transition: border-color .15s;
}
.chat-input-wrap textarea:focus { border-color: #01BF63; }
.chat-send-btn {
    width: 44px; height: 44px; border-radius: 50%;
    background: #01BF63; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: background .15s;
}
.chat-send-btn:hover { background: #00a855; }
.chat-closed-bar {
    padding: 14px 18px;
    background: #f3f4f6;
    border-top: 1px solid #e5e7eb;
    text-align: center;
    font-size: 13px;
    color: #9ca3af;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')
{{-- Top bar --}}
<div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
    <a href="{{ route('admin.chat.index') }}" class="btn btn-ghost btn-sm">← Chats</a>
    <a href="{{ route('admin.publishers.show', $supportTicket->user) }}" class="btn btn-ghost btn-sm" style="color:#3b82f6;border-color:#3b82f6;">View Publisher</a>
    <div style="margin-left:auto;display:flex;gap:8px;">
        @if($supportTicket->status !== 'closed')
        <form method="POST" action="{{ route('admin.chat.close', $supportTicket) }}">
            @csrf
            <button class="btn btn-ghost btn-sm" onclick="return confirm('Close this chat?')">Close Chat</button>
        </form>
        @endif
        <form method="POST" action="{{ route('admin.chat.destroy', $supportTicket) }}"
              onsubmit="return confirm('Delete this chat permanently?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Delete</button>
        </form>
    </div>
</div>

{{-- Chat shell --}}
<div class="chat-shell">
    {{-- Header --}}
    <div class="chat-head">
        <div class="chat-head-avatar">{{ strtoupper(substr($supportTicket->user->name, 0, 1)) }}</div>
        <div class="chat-head-meta">
            <strong>{{ $supportTicket->user->name }}</strong>
            <span>{{ $supportTicket->user->email }} &nbsp;·&nbsp; Started {{ $supportTicket->created_at->format('M d, Y · H:i') }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            <span class="badge {{ match($supportTicket->status) { 'open' => 'badge-success', 'replied' => 'badge-info', 'closed' => 'badge-gray', default => 'badge-info' } }}">
                {{ ucfirst($supportTicket->status) }}
            </span>
            <span id="liveIndicator" style="font-size:11px;color:#9ca3af;display:flex;align-items:center;gap:4px;">
                <span style="width:6px;height:6px;border-radius:50%;background:#01BF63;display:inline-block;animation:livePulse 2s infinite;"></span>Live
            </span>
        </div>
    </div>

    {{-- Messages --}}
    <div id="chatMessages" class="chat-messages">
        @foreach($supportTicket->messages as $msg)
        @php $isStaff = (bool) $msg->is_staff; @endphp
        @if($isStaff && is_null($msg->user_id) && str_contains($msg->message, 'closed'))
            <div class="cm-system">{{ $msg->message }}</div>
        @else
        <div class="cm-row {{ $isStaff ? 'staff' : '' }}" data-id="{{ $msg->id }}">
            <div class="cm-av {{ $isStaff ? 'adm' : 'pub' }}">
                {{ $isStaff ? strtoupper(substr($msg->sender?->name ?? auth()->user()->name, 0, 1)) : strtoupper(substr($supportTicket->user->name, 0, 1)) }}
            </div>
            <div class="cm-content">
                <div class="cm-bubble">{{ $msg->message }}</div>
                <div class="cm-time">{{ $msg->created_at->format('H:i') }} · {{ $msg->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    {{-- Input --}}
    @if($supportTicket->status !== 'closed')
    <div class="chat-input-wrap">
        <textarea id="replyInput" rows="1" placeholder="Type your reply… (Enter to send, Shift+Enter for newline)"
                  onkeydown="handleKey(event)" oninput="autoGrow(this)"></textarea>
        <button class="chat-send-btn" onclick="sendReply()">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
        </button>
    </div>
    @else
    <div class="chat-closed-bar">This chat session is closed.</div>
    @endif
</div>

<style>
@keyframes livePulse { 0%,100%{opacity:1} 50%{opacity:.3} }
</style>
@endsection

@push('scripts')
<script>
let lastId = {{ $supportTicket->messages->last()?->id ?? 0 }};
const adminInitial = '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}';
const pubInitial   = '{{ strtoupper(substr($supportTicket->user->name, 0, 1)) }}';
const box = document.getElementById('chatMessages');

// Scroll to bottom on load
if (box) box.scrollTop = box.scrollHeight;

function autoGrow(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}

function appendMsg(m) {
    let html;
    // System message (null user = auto/close notice)
    if (m.is_staff && !m.sender_name && m.message && m.message.includes('closed')) {
        html = `<div class="cm-system">${esc(m.message)}</div>`;
    } else {
        const staff = m.is_staff;
        const initial = staff ? adminInitial : pubInitial;
        html = `<div class="cm-row ${staff ? 'staff' : ''}" data-id="${m.id}">
            <div class="cm-av ${staff ? 'adm' : 'pub'}">${esc(initial)}</div>
            <div class="cm-content">
                <div class="cm-bubble">${esc(m.message)}</div>
                <div class="cm-time">${esc(m.time)}</div>
            </div>
        </div>`;
    }
    box.insertAdjacentHTML('beforeend', html);
    box.scrollTop = box.scrollHeight;
    lastId = m.id;
}

function playPing() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(ctx.destination);
        o.type = 'sine'; o.frequency.value = 880;
        g.gain.setValueAtTime(0.2, ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
        o.start(); o.stop(ctx.currentTime + 0.5);
    } catch(e) {}
}

function pollMessages() {
    fetch('{{ route("admin.chat.messages", $supportTicket) }}', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        let hasNewPub = false;
        data.messages.forEach(m => {
            if (m.id > lastId) {
                appendMsg(m);
                if (!m.is_staff) hasNewPub = true;
            }
        });
        if (hasNewPub) playPing(); // only ring when publisher sends a message
    })
    .catch(() => {});
}

// Poll only while this chat page is open
setInterval(pollMessages, 4000);

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
