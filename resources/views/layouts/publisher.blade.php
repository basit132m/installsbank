<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Installs Bank</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        :root {
            --primary: #01BF63;
            --primary-dark: #00a354;
            --primary-light: #e6faf2;
            --sidebar-width: 240px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f9fafb; color: #1f2937; display: flex; min-height: 100vh; }

        .sidebar {
            width: var(--sidebar-width);
            background: white;
            border-right: 1px solid #e5e7eb;
            height: 100vh;
            position: fixed;
            display: flex;
            flex-direction: column;
        }
        .sidebar-logo { padding: 20px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px; }
        .sidebar-logo img { height: 30px; }
        .sidebar-nav { padding: 16px 0; flex: 1; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s;
        }
        .nav-item:hover { background: #f9fafb; color: #111827; }
        .nav-item.active { background: var(--primary-light); color: var(--primary-dark); border-right: 3px solid var(--primary); }
        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }
        .sidebar-footer { padding: 16px 20px; border-top: 1px solid #f3f4f6; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 36px; height: 36px; background: var(--primary-light); color: var(--primary-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }
        .user-name { font-size: 13px; font-weight: 600; }
        .user-role { font-size: 11px; color: #9ca3af; }
        .status-badge { font-size: 10px; padding: 2px 8px; border-radius: 10px; font-weight: 600; }
        .status-active { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }

        .main { margin-left: var(--sidebar-width); flex: 1; }
        .header { height: 64px; background: white; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .header-title { font-size: 18px; font-weight: 700; }
        .content { padding: 32px; }

        .card { background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .card-title { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 20px; }
        .stat-value { font-size: 26px; font-weight: 800; color: #111827; }
        .stat-label { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }

        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-ghost { background: transparent; color: #6b7280; border: 1px solid #d1d5db; }
        .btn-ghost:hover { background: #f3f4f6; }

        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.15s; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(1,191,99,0.1); }

        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f3f4f6; color: #6b7280; }

        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
        tbody td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #f9fafb; }

        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mt-4 { margin-top: 16px; }
        .text-muted { color: #6b7280; }
        .text-sm { font-size: 13px; }

        .contract-box { border: 2px solid var(--primary); border-radius: 12px; padding: 24px; background: var(--primary-light); }
        .contract-box h3 { color: var(--primary-dark); font-size: 16px; font-weight: 700; }

        .code-box { background: #1f2937; color: #e5e7eb; padding: 16px; border-radius: 8px; font-family: monospace; font-size: 13px; overflow-x: auto; white-space: pre-wrap; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank">
            <span style="font-size:14px;font-weight:800;">Installs Bank</span>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('publisher.dashboard') }}" class="nav-item {{ request()->routeIs('publisher.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zm0 9a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1h-4a1 1 0 01-1-1v-5zM4 13a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1v-5z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('publisher.stats') }}" class="nav-item {{ request()->routeIs('publisher.stats') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Stats
            </a>
            <a href="{{ route('publisher.websites.index') }}" class="nav-item {{ request()->routeIs('publisher.websites*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                My Websites
            </a>
            <a href="{{ route('publisher.adcode') }}" class="nav-item {{ request()->routeIs('publisher.adcode*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                Ad Code
            </a>
            <a href="{{ route('publisher.withdrawals.index') }}" class="nav-item {{ request()->routeIs('publisher.withdrawals*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Withdrawals
            </a>
            @if(auth()->user()->publisherProfile?->contract_type === 'installs_base')
            <a href="{{ route('publisher.dashboard') }}" class="nav-item {{ request()->routeIs('publisher.dashboard') && request()->has('section') === false ? '' : '' }}" style="color:#01BF63;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Installs
            </a>
            @endif
            <a href="{{ route('publisher.support.index') }}" class="nav-item {{ request()->routeIs('publisher.support*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Support
            </a>
            <a href="{{ route('publisher.profile') }}" class="nav-item {{ request()->routeIs('publisher.profile*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My Profile
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                @if(auth()->user()->avatar)
                    <img src="/avatars/{{ auth()->user()->avatar }}" alt="avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                @else
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                @endif
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Publisher</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    <div class="main">
        <header class="header">
            <h1 class="header-title">@yield('page-title', 'Dashboard')</h1>
        </header>
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">@foreach($errors->all() as $e){{ $e }}<br>@endforeach</div>
            @endif
            @yield('content')
        </div>
    </div>
    @stack('scripts')

<!-- ========== LIVE CHAT WIDGET (approved publishers only) ========== -->
@if(auth()->user()->status === 'active')
<style>
#chatBubble {
    position:fixed; bottom:24px; right:24px; z-index:9999;
    display:flex; align-items:center; gap:10px;
    background:var(--primary); color:white;
    padding:12px 20px 12px 14px; border-radius:30px;
    cursor:pointer; box-shadow:0 4px 20px rgba(1,191,99,0.4);
    font-size:14px; font-weight:700; transition:all .2s;
    border:none; outline:none;
    animation: chatBounce 3s infinite;
}
#chatBubble:hover { background:var(--primary-dark); transform:translateY(-2px); box-shadow:0 8px 28px rgba(1,191,99,0.5); }
#chatUnread {
    position:absolute; top:-6px; right:-4px;
    background:#ef4444; color:white; font-size:10px; font-weight:800;
    width:18px; height:18px; border-radius:50%; display:none;
    align-items:center; justify-content:center;
    border:2px solid white;
}
@keyframes chatBounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
#chatPanel {
    position:fixed; bottom:88px; right:24px; z-index:9998;
    width:340px; max-height:520px;
    background:white; border-radius:18px;
    box-shadow:0 8px 40px rgba(0,0,0,0.18);
    display:none; flex-direction:column; overflow:hidden;
    border:1px solid #e5e7eb;
}
#chatPanel.open { display:flex; }
#chatHeader {
    background:var(--primary); color:white;
    padding:14px 18px; display:flex; align-items:center; gap:10px;
}
#chatHeader .dot { width:9px; height:9px; background:white; border-radius:50%; opacity:.9; animation:chatBounce 2s infinite; }
#chatHeader span { font-size:15px; font-weight:700; flex:1; }
#chatHeader button { background:none; border:none; color:white; font-size:20px; cursor:pointer; opacity:.8; line-height:1; }
#chatHeader button:hover { opacity:1; }
#chatMessages {
    flex:1; overflow-y:auto; padding:16px;
    display:flex; flex-direction:column; gap:10px;
    background:#f9fafb;
}
.chat-msg { display:flex; gap:8px; max-width:88%; }
.chat-msg.mine { align-self:flex-end; flex-direction:row-reverse; }
.chat-avatar {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    background:var(--primary); display:flex; align-items:center;
    justify-content:center; font-size:12px; font-weight:700; color:white;
}
.chat-avatar.staff { background:#374151; }
.chat-bubble {
    background:white; border:1px solid #e5e7eb; border-radius:14px 14px 14px 2px;
    padding:9px 13px; font-size:13px; color:#111827; line-height:1.5;
    box-shadow:0 1px 3px rgba(0,0,0,0.05);
}
.chat-msg.mine .chat-bubble {
    background:var(--primary); color:white; border:none;
    border-radius:14px 14px 2px 14px;
}
.chat-time { font-size:10px; color:#9ca3af; margin-top:3px; text-align:right; }
.chat-msg.mine .chat-time { text-align:left; }
#chatInputArea {
    padding:12px 14px; background:white;
    border-top:1px solid #f3f4f6;
    display:flex; gap:8px; align-items:center;
}
#chatInput {
    flex:1; border:1px solid #e5e7eb; border-radius:20px;
    padding:9px 16px; font-size:13px; font-family:inherit;
    outline:none; transition:border-color .15s;
}
#chatInput:focus { border-color:var(--primary); }
#chatSendBtn {
    width:36px; height:36px; border-radius:50%; background:var(--primary);
    border:none; cursor:pointer; display:flex; align-items:center;
    justify-content:center; flex-shrink:0; transition:all .15s;
}
#chatSendBtn:hover { background:var(--primary-dark); }
#chatEmpty {
    text-align:center; padding:30px 20px; color:#9ca3af; font-size:13px;
}
</style>

<!-- Bubble -->
<button id="chatBubble" onclick="toggleChat()">
    <span id="chatUnread"></span>
    <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
    </svg>
    Live Chat
</button>

<!-- Panel -->
<div id="chatPanel">
    <div id="chatHeader">
        <div class="dot"></div>
        <span>Message Us</span>
        <button onclick="toggleChat()">×</button>
    </div>
    <div id="chatMessages">
        <div id="chatEmpty">
            <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 10px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Send a message and our team will reply here.
        </div>
    </div>
    <div id="chatInputArea">
        <input id="chatInput" type="text" placeholder="Message..." maxlength="2000"
               onkeydown="if(event.key==='Enter')sendChat()">
        <button id="chatSendBtn" onclick="sendChat()">
            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
        </button>
    </div>
</div>

<script>
let chatOpen = false;
let lastMsgId = 0;
let chatPollTimer = null;

function toggleChat() {
    chatOpen = !chatOpen;
    document.getElementById('chatPanel').classList.toggle('open', chatOpen);
    if (chatOpen) {
        loadMessages();
        document.getElementById('chatUnread').style.display = 'none';
        document.getElementById('chatInput').focus();
    }
}

function renderMsg(m) {
    const mine = !m.is_staff;
    const initials = mine
        ? '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}'
        : 'S';
    return `<div class="chat-msg ${mine ? 'mine' : ''}">
        <div class="chat-avatar ${mine ? '' : 'staff'}">${initials}</div>
        <div>
            <div class="chat-bubble">${escHtml(m.message)}</div>
            <div class="chat-time">${m.time}</div>
        </div>
    </div>`;
}

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function loadMessages() {
    fetch('{{ route("publisher.chat.messages") }}', {
        headers: {'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json'}
    })
    .then(r => r.json())
    .then(data => {
        const box = document.getElementById('chatMessages');
        const empty = document.getElementById('chatEmpty');
        if (!data.messages.length) { empty.style.display = 'block'; return; }
        empty.style.display = 'none';

        // Only re-render if there are new messages
        const newest = data.messages[data.messages.length - 1].id;
        if (newest === lastMsgId) return;
        lastMsgId = newest;

        box.innerHTML = '<div id="chatEmpty" style="display:none"></div>' +
            data.messages.map(renderMsg).join('');
        box.scrollTop = box.scrollHeight;

        // Show unread dot if panel is closed and last msg is from staff
        const last = data.messages[data.messages.length - 1];
        if (!chatOpen && last.is_staff) {
            const unread = document.getElementById('chatUnread');
            unread.style.display = 'flex';
            unread.textContent = '!';
        }
    });
}

function sendChat() {
    const input = document.getElementById('chatInput');
    const msg = input.value.trim();
    if (!msg) return;
    input.value = '';

    fetch('{{ route("publisher.chat.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message: msg })
    })
    .then(r => r.json())
    .then(m => {
        const box = document.getElementById('chatMessages');
        document.getElementById('chatEmpty').style.display = 'none';
        box.insertAdjacentHTML('beforeend', renderMsg(m));
        box.scrollTop = box.scrollHeight;
        lastMsgId = m.id;
    });
}

// Poll every 6 seconds
setInterval(() => { if (chatOpen) loadMessages(); else loadMessages(); }, 6000);
// Initial check for unread
setTimeout(loadMessages, 2000);
</script>
<!-- ========== END LIVE CHAT ========== -->
@endif

</body>
</html>
