<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Installs Bank Admin</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        :root {
            --primary: #01BF63;
            --primary-dark: #00a354;
            --primary-light: #e6faf2;
            --sidebar-width: 260px;
            --header-height: 64px;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--gray-50); color: var(--gray-800); display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid var(--gray-200);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            overflow-y: auto;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        .sidebar-logo {
            padding: 20px 24px;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-logo img { height: 32px; }
        .sidebar-nav { padding: 16px 0; flex: 1; }
        .nav-section-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gray-400);
            padding: 8px 24px 4px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            color: var(--gray-600);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 0;
            transition: all 0.15s;
            position: relative;
        }
        .nav-item:hover { background: var(--gray-50); color: var(--gray-900); }
        .nav-item.active {
            background: var(--primary-light);
            color: var(--primary-dark);
            border-right: 3px solid var(--primary);
        }
        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }
        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 10px;
        }
        .nav-badge.warning { background: var(--warning); }
        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--gray-100);
        }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 36px; height: 36px;
            background: var(--primary-light);
            color: var(--primary-dark);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        .user-name { font-size: 13px; font-weight: 600; color: var(--gray-800); }
        .user-role { font-size: 11px; color: var(--gray-400); text-transform: capitalize; }

        /* Main content */
        .main { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; }
        .header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .header-title { font-size: 18px; font-weight: 700; color: var(--gray-900); }
        .header-actions { display: flex; align-items: center; gap: 12px; }
        .btn-icon {
            width: 36px; height: 36px;
            background: var(--gray-100);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--gray-600);
            text-decoration: none;
            transition: background 0.15s;
            position: relative;
        }
        .btn-icon:hover { background: var(--gray-200); }
        .notification-dot {
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            position: absolute;
            top: 6px; right: 6px;
            border: 2px solid white;
        }
        .content { padding: 32px; flex: 1; }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .card-title { font-size: 15px; font-weight: 700; color: var(--gray-900); margin-bottom: 4px; }
        .card-subtitle { font-size: 13px; color: var(--gray-500); }

        /* Stat cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .stat-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .stat-value { font-size: 28px; font-weight: 800; color: var(--gray-900); line-height: 1; }
        .stat-label { font-size: 13px; color: var(--gray-500); font-weight: 500; }
        .stat-change { font-size: 12px; font-weight: 600; }
        .stat-change.up { color: var(--success); }
        .stat-change.down { color: var(--danger); }

        /* Tables */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-500);
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }
        tbody td {
            padding: 14px 16px;
            font-size: 14px;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-100);
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: var(--gray-50); }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: var(--gray-100); color: var(--gray-600); }
        .badge-primary { background: var(--primary-light); color: var(--primary-dark); }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s;
        }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #dc2626; color: white; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-ghost { background: transparent; color: var(--gray-600); border: 1px solid var(--gray-300); }
        .btn-ghost:hover { background: var(--gray-100); }
        .btn-success { background: var(--success); color: white; }

        /* Forms */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            color: var(--gray-800);
            background: white;
            transition: border-color 0.15s;
            outline: none;
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(1,191,99,0.1); }
        .form-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }

        /* Alerts */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        /* Grid */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .flex { display: flex; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .flex-gap { display: flex; align-items: center; gap: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mt-4 { margin-top: 16px; }
        .text-sm { font-size: 13px; }
        .text-xs { font-size: 12px; }
        .text-muted { color: var(--gray-500); }
        .font-bold { font-weight: 700; }
        .text-right { text-align: right; }
        .gap-2 { gap: 8px; }

        /* Pagination */
        .pagination { display: flex; gap: 4px; align-items: center; justify-content: center; margin-top: 20px; }
        .pagination a, .pagination span {
            padding: 7px 12px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }
        .pagination .active span { background: var(--primary); color: white; border-color: var(--primary); }
        .pagination a:hover { background: var(--gray-100); }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }

        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 16px; padding: 32px; max-width: 480px; width: 90%; }
        .modal-title { font-size: 18px; font-weight: 700; margin-bottom: 16px; }

        /* Toggle */
        .toggle-wrap { display: flex; align-items: center; gap: 8px; }
        .toggle { position: relative; width: 44px; height: 24px; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; cursor: pointer; inset: 0;
            background: var(--gray-300); border-radius: 24px;
            transition: 0.3s;
        }
        .toggle-slider:before {
            content: ''; position: absolute;
            width: 18px; height: 18px;
            left: 3px; bottom: 3px;
            background: white; border-radius: 50%;
            transition: 0.3s;
        }
        .toggle input:checked + .toggle-slider { background: var(--primary); }
        .toggle input:checked + .toggle-slider:before { transform: translateX(20px); }

        /* Live indicator */
        .live-dot { width: 8px; height: 8px; background: var(--success); border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" onerror="this.style.display='none'">
            <span style="font-size:15px;font-weight:800;color:var(--gray-900);">Installs Bank</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zm0 9a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1h-4a1 1 0 01-1-1v-5zM4 13a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1v-5z"/></svg>
                Dashboard
            </a>

            <div class="nav-section-title">Publishers</div>
            <a href="{{ route('admin.publishers.index') }}" class="nav-item {{ request()->routeIs('admin.publishers.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                Publishers
                @php $pendingCount = \App\Models\User::where('role','publisher')->where('status','pending')->count(); @endphp
                @if($pendingCount > 0)<span class="nav-badge warning">{{ $pendingCount }}</span>@endif
            </a>
            <a href="{{ route('admin.contracts.index') }}" class="nav-item {{ request()->routeIs('admin.contracts.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Contracts
            </a>

            <div class="nav-section-title">Tracking</div>
            <a href="{{ route('admin.tracking.index') }}" class="nav-item {{ request()->routeIs('admin.tracking.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Tracking Links
            </a>
            <a href="{{ route('admin.tracking-domains.index') }}" class="nav-item {{ request()->routeIs('admin.tracking-domains.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                Tracking Domains
            </a>
            <a href="{{ route('admin.ad-presets.index') }}" class="nav-item {{ request()->routeIs('admin.ad-presets.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                Ad Presets
            </a>

            <div class="nav-section-title">Finance</div>
            <a href="{{ route('admin.rates.index') }}" class="nav-item {{ request()->routeIs('admin.rates.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                Country Rates
                @php $unratedCount = \App\Models\CountryRate::where('needs_rate_update', true)->count(); @endphp
                @if($unratedCount > 0)<span class="nav-badge warning">{{ $unratedCount }}</span>@endif
            </a>
            <a href="{{ route('admin.withdrawals.index') }}" class="nav-item {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Withdrawals
                @php $pendingW = \App\Models\Withdrawal::where('status','pending')->count(); @endphp
                @if($pendingW > 0)<span class="nav-badge">{{ $pendingW }}</span>@endif
            </a>

            <div class="nav-section-title">Security</div>
            <a href="{{ route('admin.fraud.index') }}" class="nav-item {{ request()->routeIs('admin.fraud.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Fraud Alerts
                @php $fraudCount = \App\Models\FraudAlert::where('is_resolved',false)->count(); @endphp
                @if($fraudCount > 0)<span class="nav-badge">{{ $fraudCount }}</span>@endif
            </a>

            <div class="nav-section-title">Support</div>
            <a href="{{ route('admin.support.index') }}" class="nav-item {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Support
                @php $openTickets = \App\Models\SupportTicket::where('status','open')->count(); @endphp
                @if($openTickets > 0)<span class="nav-badge warning">{{ $openTickets }}</span>@endif
            </a>

            @if(auth()->user()->isAdmin())
            <div class="nav-section-title">Team</div>
            <a href="{{ route('admin.managers.index') }}" class="nav-item {{ request()->routeIs('admin.managers.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Managers
            </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--gray-400);padding:4px;" title="Logout">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main">
        <header class="header">
            <h1 class="header-title">@yield('page-title', 'Dashboard')</h1>
            <div class="header-actions">
                <span style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--gray-500);">
                    <span class="live-dot"></span> Live
                </span>
                <a href="{{ route('admin.fraud.index') }}" class="btn-icon" title="Fraud Alerts">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @php $fa = \App\Models\FraudAlert::where('is_resolved',false)->count(); @endphp
                    @if($fa > 0)<span class="notification-dot"></span>@endif
                </a>
            </div>
        </header>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
