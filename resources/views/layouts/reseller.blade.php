<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Installs Bank Reseller</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%237c3aed'/><text x='16' y='23' font-family='Arial,sans-serif' font-size='13' font-weight='800' fill='white' text-anchor='middle'>IB</text></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --primary-light: #f5f3ff;
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
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(124,58,237,0.1); }

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
            <div>
                <span style="font-size:14px;font-weight:800;display:block;">Installs Bank</span>
                <span style="font-size:10px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.08em;">Reseller</span>
            </div>
        </div>
        @php $approved = auth()->user()->status === 'active'; @endphp
        <nav class="sidebar-nav">
            <a href="{{ route('reseller.dashboard') }}" class="nav-item {{ request()->routeIs('reseller.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zm0 9a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1h-4a1 1 0 01-1-1v-5zM4 13a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1v-5z"/></svg>
                Dashboard
            </a>

            @if($approved)
            <a href="{{ route('reseller.stats') }}" class="nav-item {{ request()->routeIs('reseller.stats') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Stats
            </a>
            <a href="{{ route('reseller.websites.index') }}" class="nav-item {{ request()->routeIs('reseller.websites*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                My Websites
            </a>
            @endif

            <a href="{{ route('reseller.profile') }}" class="nav-item {{ request()->routeIs('reseller.profile*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My Profile
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Reseller</div>
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
            <h1 class="header-title" style="margin:0;">@yield('page-title', 'Dashboard')</h1>
            @php
                $unreadCount = \App\Models\PublisherNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            <div style="position:relative;display:flex;align-items:center;gap:8px;">
                <a href="{{ route('reseller.notifications.index') }}" title="Notifications"
                   style="position:relative;display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:#f3f4f6;color:#374151;text-decoration:none;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span style="position:absolute;top:2px;right:2px;min-width:16px;height:16px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;border-radius:9999px;display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
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
</body>
</html>
