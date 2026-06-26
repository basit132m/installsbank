<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Installs Bank</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%234f46e5'/><text x='16' y='23' font-family='Arial,sans-serif' font-size='13' font-weight='800' fill='white' text-anchor='middle'>IB</text></svg>">
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
        @php $approved = auth()->user()->status === 'active'; @endphp
        <nav class="sidebar-nav">
            <a href="{{ route('publisher.dashboard') }}" class="nav-item {{ request()->routeIs('publisher.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zm0 9a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1h-4a1 1 0 01-1-1v-5zM4 13a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1v-5z"/></svg>
                Dashboard
            </a>

            @if($approved)
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
            <a href="{{ route('publisher.contracts') }}" class="nav-item {{ request()->routeIs('publisher.contracts') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Contracts
            </a>
            @if(auth()->user()->publisherProfile?->contract_type === 'installs_base')
            <a href="{{ route('publisher.dashboard') }}" class="nav-item" style="color:#01BF63;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Installs
            </a>
            @endif
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
        <header class="header" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <h1 class="header-title" style="margin:0;">@yield('page-title', 'Dashboard')</h1>
            @php
                $unreadCount = \App\Models\PublisherNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            <div style="position:relative;display:flex;align-items:center;gap:8px;">
                <a href="{{ route('publisher.notifications.index') }}" id="notifBell" title="Notifications"
                   style="position:relative;display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:#f3f4f6;color:#374151;text-decoration:none;transition:background .15s;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span id="notifBadge" style="position:absolute;top:2px;right:2px;min-width:16px;height:16px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;border-radius:9999px;display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </a>
            </div>
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

@if(session('show_welcome'))
{{-- Door panels --}}
<div id="doorLeft" style="
    position:fixed;top:0;left:0;width:50%;height:100vh;z-index:100001;
    background:#01BF63;
    display:flex;align-items:center;justify-content:flex-end;padding-right:32px;
    transition:transform 1s cubic-bezier(0.76,0,0.24,1);
    will-change:transform;
">
    <div style="text-align:right;color:rgba(255,255,255,.15);font-size:80px;font-weight:900;letter-spacing:-4px;user-select:none;">IB</div>
</div>
<div id="doorRight" style="
    position:fixed;top:0;right:0;width:50%;height:100vh;z-index:100001;
    background:#01BF63;
    display:flex;align-items:center;justify-content:flex-start;padding-left:32px;
    transition:transform 1s cubic-bezier(0.76,0,0.24,1);
    will-change:transform;
">
    <div style="text-align:left;color:rgba(255,255,255,.15);font-size:80px;font-weight:900;letter-spacing:-4px;user-select:none;">IB</div>
</div>
{{-- Thin center line between doors --}}
<div id="doorLine" style="
    position:fixed;top:0;left:50%;width:1px;height:100vh;z-index:100002;
    background:rgba(0,0,0,.12);transform:translateX(-50%);
    transition:opacity .3s;
"></div>

{{-- Welcome content (fades in after door opens) --}}
<div id="welcomeOverlay" style="
    position:fixed;inset:0;z-index:99999;
    background:rgba(255,255,255,0.97);
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:24px;opacity:0;transition:opacity .4s ease;pointer-events:none;
">
    <img src="https://installsbank.com/images/waving-fox.webp"
         alt="Welcome"
         style="width:220px;max-width:60vw;object-fit:contain;animation:wBounce 1s ease infinite alternate;">
    <div style="text-align:center;">
        <div style="font-size:26px;font-weight:800;color:#111827;letter-spacing:-0.5px;">Welcome Back to Installs Bank</div>
        <div style="font-size:15px;color:#6b7280;margin-top:8px;">Good to see you again, {{ auth()->user()->name }}!</div>
    </div>
</div>
<style>
@keyframes wBounce  { from{transform:translateY(0)} to{transform:translateY(-10px)} }
@keyframes wFadeOut { from{opacity:1} to{opacity:0} }
</style>
<script>
(function(){
    var dL  = document.getElementById('doorLeft');
    var dR  = document.getElementById('doorRight');
    var dLn = document.getElementById('doorLine');
    var ov  = document.getElementById('welcomeOverlay');
    if(!dL || !dR) return;

    // Open the doors after a short pause
    setTimeout(function(){
        dL.style.transform = 'translateX(-100%)';
        dR.style.transform = 'translateX(100%)';
        dLn.style.opacity  = '0';

        // Show welcome overlay as doors finish opening
        setTimeout(function(){
            dL.remove(); dR.remove(); dLn.remove();
            ov.style.opacity        = '1';
            ov.style.pointerEvents  = 'all';

            // Fade out welcome after 3s
            setTimeout(function(){
                ov.style.animation = 'wFadeOut .5s ease forwards';
                setTimeout(function(){ ov.remove(); }, 500);
            }, 3000);
        }, 1000);
    }, 250);
})();
</script>
@endif

@if(session('show_approval_welcome'))
<div id="approvalOverlay" style="
    position:fixed;inset:0;z-index:100000;
    background:rgba(255,255,255,0.98);
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:20px;padding:24px;
    animation:wFadeIn .4s ease;
">
    <img src="https://installsbank.com/images/waving-fox.webp"
         alt="Congratulations"
         style="width:200px;max-width:55vw;object-fit:contain;animation:wBounce 1s ease infinite alternate;">
    <div style="text-align:center;max-width:480px;">
        <div style="display:inline-block;background:#f0fdf4;border:1.5px solid #86efac;border-radius:20px;padding:5px 18px;font-size:13px;font-weight:700;color:#166534;margin-bottom:14px;">
            🎉 Account Approved
        </div>
        <div style="font-size:26px;font-weight:800;color:#111827;letter-spacing:-0.5px;line-height:1.2;margin-bottom:10px;">
            Congratulations, {{ auth()->user()->name }}!
        </div>
        <div style="font-size:15px;color:#4b5563;line-height:1.7;margin-bottom:6px;">
            Your account has been approved at <strong>Installs Bank</strong>.
        </div>
        <div style="font-size:14px;color:#6b7280;line-height:1.7;">
            Choose your well-suited contract type and request for your ad code to start earning.
        </div>
    </div>
    <a href="{{ route('publisher.contracts') }}"
       style="background:#01BF63;color:white;text-decoration:none;border-radius:12px;padding:13px 32px;font-size:15px;font-weight:700;margin-top:4px;">
        Choose Contract →
    </a>
    <button onclick="closeApprovalOverlay()" style="background:none;border:none;font-size:13px;color:#9ca3af;cursor:pointer;margin-top:-8px;">
        Skip for now
    </button>
</div>
<script>
function closeApprovalOverlay() {
    var el = document.getElementById('approvalOverlay');
    if (!el) return;
    el.style.animation = 'wFadeOut .5s ease forwards';
    setTimeout(function(){ el.remove(); }, 500);
}
// Auto-dismiss after 8 seconds
setTimeout(closeApprovalOverlay, 8000);
</script>
@endif

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
    width:420px; max-height:620px;
    background:white; border-radius:18px;
    box-shadow:0 8px 40px rgba(0,0,0,0.18);
    display:none; flex-direction:column; overflow:hidden;
    border:1px solid #e5e7eb;
}
#chatPanel.open { display:flex; }
#chatLogoBar {
    background:white; padding:14px 18px 10px;
    border-bottom:1px solid #f3f4f6;
    display:flex; align-items:center; justify-content:center; position:relative;
}
#chatLogoBar img { height:34px; object-fit:contain; }
#chatLogoBar button {
    position:absolute; right:14px; top:50%; transform:translateY(-50%);
    background:none; border:none; color:#9ca3af; font-size:22px; cursor:pointer; line-height:1;
}
#chatLogoBar button:hover { color:#374151; }
#chatHeader {
    background:var(--primary); color:white;
    padding:10px 18px; display:flex; align-items:center; gap:10px;
}
#chatHeader .dot { width:8px; height:8px; background:white; border-radius:50%; opacity:.9; animation:chatBounce 2s infinite; }
#chatHeader span { font-size:13px; font-weight:600; flex:1; }
#chatMessages {
    flex:1; overflow-y:auto; padding:16px;
    display:flex; flex-direction:column; gap:10px;
    background:#f9fafb; min-height:320px;
}
.chat-msg { display:flex; gap:8px; max-width:88%; }
.chat-msg.mine { align-self:flex-end; flex-direction:row-reverse; }
.chat-avatar {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    background:var(--primary); display:flex; align-items:center;
    justify-content:center; font-size:12px; font-weight:700; color:white;
    overflow:hidden;
}
.chat-avatar.staff { background:#f3f4f6; padding:3px; }
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
    <div id="chatLogoBar">
        <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" onerror="this.style.display='none'">
        <button onclick="toggleChat()">×</button>
    </div>
    <div id="chatHeader">
        <div class="dot"></div>
        <span>Support Team · We reply quickly</span>
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
    <div id="chatClosedBanner" style="display:none;padding:12px 16px;background:#f3f4f6;border-top:1px solid #e5e7eb;text-align:center;">
        <div style="font-size:12px;color:#6b7280;margin-bottom:8px;">This chat session has been closed.</div>
        <button onclick="startNewChat()" style="background:#01BF63;color:#fff;border:none;border-radius:8px;padding:7px 18px;font-size:13px;font-weight:600;cursor:pointer;">Start New Chat</button>
    </div>
</div>

<script>
let chatOpen    = false;
let lastMsgId   = 0;
let currentTicketId = null;
let chatClosed  = false;

function toggleChat() {
    chatOpen = !chatOpen;
    document.getElementById('chatPanel').classList.toggle('open', chatOpen);
    if (chatOpen) {
        loadMessages();
        document.getElementById('chatUnread').style.display = 'none';
        if (!chatClosed) setTimeout(() => document.getElementById('chatInput').focus(), 50);
    }
}

function renderMsg(m) {
    const mine = !m.is_staff;
    const avatarContent = mine
        ? '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}'
        : '<img src="https://installsbank.com/images/installs-bank.webp" style="width:24px;height:24px;object-fit:contain;" onerror="this.parentNode.textContent=\'S\'">';
    return `<div class="chat-msg ${mine ? 'mine' : ''}">
        <div class="chat-avatar ${mine ? '' : 'staff'}">${avatarContent}</div>
        <div>
            <div class="chat-bubble">${escHtml(m.message)}</div>
            <div class="chat-time">${m.time}</div>
        </div>
    </div>`;
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function setChatClosed(closed) {
    chatClosed = closed;
    const inputArea  = document.getElementById('chatInputArea');
    const closedBanner = document.getElementById('chatClosedBanner');
    if (closed) {
        inputArea.style.display   = 'none';
        closedBanner.style.display = 'block';
    } else {
        inputArea.style.display    = '';
        closedBanner.style.display = 'none';
    }
}

function loadMessages() {
    fetch('{{ route("publisher.chat.messages") }}', {
        headers: {'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json'}
    })
    .then(r => r.json())
    .then(data => {
        const box   = document.getElementById('chatMessages');
        const empty = document.getElementById('chatEmpty');

        // Handle ticket switch (new chat after closure)
        if (data.ticket_id && data.ticket_id !== currentTicketId) {
            currentTicketId = data.ticket_id;
            lastMsgId = 0;
        }

        // Reflect closed status
        setChatClosed(data.status === 'closed');

        if (!data.messages || !data.messages.length) {
            empty.style.display = 'block';
            return;
        }
        empty.style.display = 'none';

        const newest = data.messages[data.messages.length - 1].id;
        if (newest === lastMsgId) return;
        lastMsgId = newest;

        box.innerHTML = '<div id="chatEmpty" style="display:none"></div>' +
            data.messages.map(renderMsg).join('');
        box.scrollTop = box.scrollHeight;

        // Unread dot if panel closed and latest is from staff
        const last = data.messages[data.messages.length - 1];
        if (!chatOpen && last.is_staff) {
            const dot = document.getElementById('chatUnread');
            dot.style.display = 'flex';
            dot.textContent   = '!';
        }
    });
}

function sendChat() {
    const input = document.getElementById('chatInput');
    const msg   = input.value.trim();
    if (!msg) return;
    input.value = '';

    fetch('{{ route("publisher.chat.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
            'Accept':        'application/json'
        },
        body: JSON.stringify({ message: msg })
    })
    .then(r => r.json())
    .then(m => {
        // If server created a new ticket (after closure), reset state
        if (m.ticket_id && m.ticket_id !== currentTicketId) {
            currentTicketId = m.ticket_id;
            lastMsgId = 0;
            setChatClosed(false);
            // Reload all messages for the new ticket (includes auto-reply)
            setTimeout(loadMessages, 800);
            return;
        }
        const box = document.getElementById('chatMessages');
        document.getElementById('chatEmpty').style.display = 'none';
        box.insertAdjacentHTML('beforeend', renderMsg(m));
        box.scrollTop = box.scrollHeight;
        lastMsgId = m.id;
    });
}

function startNewChat() {
    // Reset UI and let user type a first message
    setChatClosed(false);
    currentTicketId = null;
    lastMsgId       = 0;
    const box = document.getElementById('chatMessages');
    box.innerHTML = '<div id="chatEmpty" style="display:none"></div>';
    document.getElementById('chatEmpty').style.display = 'block';
    document.getElementById('chatInput').focus();
}

// Poll every 6 seconds
setInterval(loadMessages, 6000);
// Initial check for unread (slight delay so page loads first)
setTimeout(loadMessages, 1500);
</script>
<!-- ========== END LIVE CHAT ========== -->
@endif

</body>
</html>
