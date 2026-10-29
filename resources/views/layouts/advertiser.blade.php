<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Installs Bank</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%2301BF63'/><text x='16' y='23' font-family='Arial,sans-serif' font-size='13' font-weight='800' fill='white' text-anchor='middle'>IB</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        :root {
            --primary: #01BF63;
            --primary-dark: #00a354;
            --primary-light: #e6faf2;
            --accent: #3b82f6;
            --accent-dark: #1e40af;
            --accent-light: #eff6ff;
            --gray-50: #f8fafc;
            --sidebar-width: 256px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--gray-50); color: #1f2937; display: flex; min-height: 100vh; -webkit-font-smoothing: antialiased; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid #eef0f3;
            height: 100vh; position: fixed; z-index: 60;
            display: flex; flex-direction: column;
        }
        .sidebar-logo { padding: 20px 22px; display: flex; align-items: center; gap: 11px; border-bottom: 1px solid #f3f4f6; }
        .sidebar-logo .logo-mark { width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, var(--primary), #00d873); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 15px; box-shadow: 0 4px 10px rgba(1,191,99,.25); }
        .sidebar-logo .logo-text { font-size: 15px; font-weight: 800; letter-spacing: -0.01em; }

        .sidebar-nav { padding: 14px 0; flex: 1; overflow-y: auto; }
        .nav-section { padding: 14px 24px 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #b6bcc6; }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            margin: 2px 12px; padding: 10px 14px; border-radius: 10px;
            color: #5b6472; text-decoration: none; font-size: 14px; font-weight: 500;
            transition: all 0.15s;
        }
        .nav-item i { width: 18px; text-align: center; font-size: 15px; color: #9aa2b1; transition: color .15s; }
        .nav-item:hover { background: #f5f7fa; color: #111827; }
        .nav-item:hover i { color: #5b6472; }
        .nav-item.active { background: var(--primary-light); color: var(--primary-dark); font-weight: 600; }
        .nav-item.active i { color: var(--primary); }
        .nav-badge { margin-left: auto; background: var(--accent); color: #fff; font-size: 11px; font-weight: 700; padding: 1px 8px; border-radius: 20px; }

        .nav-logout-btn {
            display: flex; align-items: center; gap: 12px; width: calc(100% - 24px);
            margin: 2px 12px; padding: 10px 14px; border-radius: 10px;
            color: #5b6472; background: none; border: none; font-size: 14px; font-weight: 500;
            font-family: inherit; cursor: pointer; text-align: left; transition: all 0.15s;
        }
        .nav-logout-btn i { width: 18px; text-align: center; font-size: 15px; color: #9aa2b1; }
        .nav-logout-btn:hover { background: #fef2f2; color: #ef4444; }
        .nav-logout-btn:hover i { color: #ef4444; }

        .sidebar-footer { padding: 14px 18px; border-top: 1px solid #f3f4f6; }
        .user-info { display: flex; align-items: center; gap: 11px; }
        .user-avatar { width: 38px; height: 38px; background: linear-gradient(135deg, var(--accent), #6366f1); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; }
        .user-role { font-size: 11px; color: #9ca3af; }

        /* ── Main ── */
        .main { margin-left: var(--sidebar-width); flex: 1; min-width: 0; }
        .header { height: 66px; background: rgba(255,255,255,0.85); backdrop-filter: saturate(180%) blur(8px); border-bottom: 1px solid #eef0f3; display: flex; align-items: center; justify-content: space-between; padding: 0 28px; position: sticky; top: 0; z-index: 50; }
        .header-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
        .header-title { font-size: 18px; font-weight: 700; letter-spacing: -0.01em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .header-right { display: flex; align-items: center; gap: 12px; }
        .menu-toggle { display: none; background: none; border: none; font-size: 20px; color: #374151; cursor: pointer; }
        .pill { display: inline-flex; align-items: center; gap: 6px; background: var(--accent-light); color: var(--accent-dark); font-size: 11px; font-weight: 700; padding: 6px 12px; border-radius: 20px; }
        .content { padding: 28px; max-width: 1280px; }

        /* ── Components ── */
        .card { background: white; border-radius: 14px; border: 1px solid #eef0f3; padding: 24px; box-shadow: 0 1px 2px rgba(16,24,40,0.04); }
        .card-title { font-size: 15px; font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
        .card-title i { color: var(--accent); }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 14px; border: 1px solid #eef0f3; padding: 20px; box-shadow: 0 1px 2px rgba(16,24,40,0.04); transition: box-shadow .15s, transform .15s; }
        .stat-card:hover { box-shadow: 0 6px 18px rgba(16,24,40,0.07); transform: translateY(-1px); }
        .stat-value { font-size: 28px; font-weight: 800; color: #111827; line-height: 1.1; }
        .stat-label { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .stat-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; font-size: 17px; }

        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 18px; border-radius: 9px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
        .btn-sm { padding: 7px 13px; font-size: 12.5px; border-radius: 8px; }
        .btn-primary { background: var(--primary); color: white; box-shadow: 0 2px 6px rgba(1,191,99,.25); }
        .btn-primary:hover { background: var(--primary-dark); color: white; box-shadow: 0 4px 12px rgba(1,191,99,.3); }
        .btn-blue { background: var(--accent); color: white; }
        .btn-blue:hover { background: #2563eb; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; color: white; }
        .btn-ghost { background: #fff; color: #4b5563; border: 1px solid #e5e7eb; }
        .btn-ghost:hover { background: #f9fafb; border-color: #d1d5db; }
        .btn[disabled] { opacity: .55; cursor: not-allowed; box-shadow: none; }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px; }
        .form-control { width: 100%; padding: 11px 14px; border: 1px solid #dde1e7; border-radius: 9px; font-size: 14px; font-family: inherit; outline: none; transition: all 0.15s; background: #fff; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
        .form-hint { font-size: 12px; color: #9ca3af; margin-top: 5px; }

        .alert { padding: 13px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
        .alert i { font-size: 15px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 11px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f3f4f6; color: #6b7280; }
        .badge-blue { background: #dbeafe; color: #1e40af; }

        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 12px 16px; text-align: left; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #8a93a2; background: #f8fafc; border-bottom: 1px solid #eef0f3; }
        tbody td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }

        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .mb-4 { margin-bottom: 16px; } .mb-6 { margin-bottom: 24px; } .mt-4 { margin-top: 16px; }
        .text-muted { color: #6b7280; } .text-sm { font-size: 13px; }

        .sidebar-backdrop { display: none; }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); transition: transform .2s; }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .menu-toggle { display: inline-flex; }
            .content { padding: 18px 16px; }
            .header { padding: 0 16px; }
            .sidebar-backdrop.show { display: block; position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 55; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar(false)"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-mark">IB</div>
            <span class="logo-text">Installs Bank</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Menu</div>
            <a href="{{ route('advertiser.dashboard') }}" class="nav-item {{ request()->routeIs('advertiser.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            <a href="{{ route('advertiser.campaigns.index') }}" class="nav-item {{ request()->routeIs('advertiser.campaigns*') ? 'active' : '' }}">
                <i class="fa-solid fa-bullhorn"></i> Campaigns
            </a>
            <a href="{{ route('advertiser.profile') }}" class="nav-item {{ request()->routeIs('advertiser.profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i> Profile
            </a>

            <div class="nav-section">Account</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <div class="user-role">Advertiser</div>
                </div>
            </div>
        </div>
    </aside>
    <div class="main">
        <header class="header">
            <div class="header-left">
                <button class="menu-toggle" onclick="toggleSidebar(true)"><i class="fa-solid fa-bars"></i></button>
                <h1 class="header-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="header-right">
                @yield('header-actions')
                <span class="pill"><i class="fa-solid fa-circle" style="font-size:7px;color:var(--accent);"></i> Advertiser</span>
            </div>
        </header>
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i><span>@foreach($errors->all() as $e){{ $e }}<br>@endforeach</span></div>
            @endif
            @yield('content')
        </div>
    </div>
    <script>
        function toggleSidebar(open){
            document.getElementById('sidebar').classList.toggle('open', open);
            document.getElementById('sidebarBackdrop').classList.toggle('show', open);
        }
    </script>
    @stack('scripts')
</body>
</html>
