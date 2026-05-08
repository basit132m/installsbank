<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Installs Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
        }

        /* ── LEFT PANEL ───────────────────────────────── */
        .left-panel {
            width: 55%;
            min-height: 100vh;
            background: #060d18;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 64px 72px;
        }

        /* animated blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            animation: blobFloat 10s ease-in-out infinite;
        }
        .blob-1 { width:480px;height:480px;background:#01BF63;opacity:.12;top:-120px;left:-120px;animation-delay:0s; }
        .blob-2 { width:320px;height:320px;background:#00e676;opacity:.09;bottom:-60px;right:-60px;animation-delay:-4s; }
        .blob-3 { width:220px;height:220px;background:#01BF63;opacity:.08;top:45%;left:55%;animation-delay:-7s; }

        @keyframes blobFloat {
            0%,100% { transform:translate(0,0) scale(1); }
            33%      { transform:translate(24px,-18px) scale(1.06); }
            66%      { transform:translate(-12px,16px) scale(0.94); }
        }

        /* subtle dot grid */
        .grid-overlay {
            position:absolute;inset:0;
            background-image:
                radial-gradient(rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .left-content { position:relative;z-index:1; }

        .brand {
            display:flex;align-items:center;gap:12px;
            margin-bottom:60px;
        }
        .brand img { height:38px;object-fit:contain; }
        .brand-name { font-size:20px;font-weight:800;color:#fff;letter-spacing:-.4px; }

        .hero-title {
            font-size:42px;font-weight:900;color:#fff;
            line-height:1.12;letter-spacing:-1.2px;
            margin-bottom:16px;
        }
        .hero-title span { color:#01BF63; }

        .hero-sub {
            font-size:15px;color:rgba(255,255,255,.5);
            line-height:1.65;margin-bottom:52px;max-width:360px;
        }

        .features { display:flex;flex-direction:column;gap:22px; }

        .feature { display:flex;align-items:flex-start;gap:14px; }

        .feature-icon {
            width:38px;height:38px;border-radius:10px;flex-shrink:0;
            background:rgba(1,191,99,.12);
            border:1px solid rgba(1,191,99,.22);
            display:flex;align-items:center;justify-content:center;
        }

        .feature-title { font-size:14px;font-weight:700;color:#fff;margin-bottom:2px; }
        .feature-sub   { font-size:12px;color:rgba(255,255,255,.4);line-height:1.5; }

        .stats-bar {
            display:flex;gap:44px;
            margin-top:56px;padding-top:40px;
            border-top:1px solid rgba(255,255,255,.08);
        }
        .stat-value { font-size:26px;font-weight:900;color:#01BF63;letter-spacing:-.5px; }
        .stat-label { font-size:12px;color:rgba(255,255,255,.35);font-weight:500;margin-top:3px; }

        /* ── RIGHT PANEL ──────────────────────────────── */
        .right-panel {
            width:45%;
            min-height:100vh;
            background:#fff;
            display:flex;align-items:center;justify-content:center;
            padding:60px 52px;
        }

        .form-box { width:100%;max-width:380px; }

        .form-header { margin-bottom:36px; }
        .form-title  { font-size:28px;font-weight:800;color:#111827;letter-spacing:-.6px;margin-bottom:6px; }
        .form-subtitle { font-size:14px;color:#6b7280; }

        .form-group { margin-bottom:20px; }
        .form-label { display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px; }

        .input-wrap { position:relative; }
        .input-icon {
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            color:#9ca3af;pointer-events:none;display:flex;
        }

        .form-control {
            width:100%;padding:13px 14px 13px 42px;
            border:1.5px solid #e5e7eb;border-radius:12px;
            font-size:14px;font-family:inherit;outline:none;
            transition:all .15s;color:#111827;background:#f9fafb;
        }
        .form-control:focus {
            border-color:#01BF63;background:#fff;
            box-shadow:0 0 0 4px rgba(1,191,99,.08);
        }

        .remember-row {
            display:flex;justify-content:space-between;align-items:center;
            margin-bottom:24px;
        }
        .check-label {
            display:flex;align-items:center;gap:7px;
            font-size:13px;color:#374151;cursor:pointer;
        }
        .check-label input { width:15px;height:15px;accent-color:#01BF63; }
        .forgot-link { font-size:13px;color:#01BF63;font-weight:600;text-decoration:none; }

        .btn-submit {
            width:100%;padding:14px;
            background:#01BF63;color:#fff;
            border:none;border-radius:12px;
            font-size:15px;font-weight:700;cursor:pointer;
            font-family:inherit;transition:all .18s;
            display:flex;align-items:center;justify-content:center;gap:8px;
            letter-spacing:-.2px;
        }
        .btn-submit:hover {
            background:#00a854;
            transform:translateY(-1px);
            box-shadow:0 6px 20px rgba(1,191,99,.28);
        }
        .btn-submit:active { transform:translateY(0);box-shadow:none; }

        .divider {
            display:flex;align-items:center;gap:12px;
            margin:24px 0;color:#d1d5db;font-size:12px;font-weight:600;
        }
        .divider::before,.divider::after { content:'';flex:1;height:1px;background:#f3f4f6; }

        .form-footer { text-align:center;font-size:14px;color:#6b7280; }
        .form-footer a { color:#01BF63;font-weight:600;text-decoration:none; }

        .alert {
            padding:12px 14px;border-radius:10px;font-size:13px;
            margin-bottom:20px;display:flex;align-items:flex-start;gap:8px;
        }
        .alert-danger  { background:#fef2f2;color:#b91c1c;border:1px solid #fecaca; }
        .alert-success { background:#f0fdf4;color:#166534;border:1px solid #bbf7d0; }

        .back-link {
            display:flex;align-items:center;justify-content:center;gap:6px;
            color:#9ca3af;font-size:13px;text-decoration:none;
            margin-top:28px;transition:color .15s;
        }
        .back-link:hover { color:#374151; }

        /* ── RESPONSIVE ───────────────────────────────── */
        @media(max-width:900px){
            body { flex-direction:column; }
            .left-panel { width:100%;min-height:auto;padding:40px 32px 44px; }
            .hero-title { font-size:30px; }
            .features,.stats-bar { display:none; }
            .right-panel { width:100%;padding:48px 28px 60px; }
        }
        @media(max-width:480px){
            .left-panel { padding:28px 20px 32px; }
            .hero-title { font-size:26px; }
            .right-panel { padding:36px 20px 48px; }
        }
    </style>
</head>
<body>

    {{-- LEFT PANEL --}}
    <div class="left-panel">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="grid-overlay"></div>

        <div class="left-content">
            <div class="brand">
                <img src="https://installsbank.com/images/logo.webp"
                     alt="Installs Bank"
                     onerror="this.src='https://installsbank.com/images/installs-bank.webp'">
                <div class="brand-name">Installs Bank</div>
            </div>

            <div class="hero-title">
                Your traffic.<br>
                <span>Real earnings.</span>
            </div>
            <div class="hero-sub">
                Turn your website visitors into consistent daily revenue with the most reliable publisher network.
            </div>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Real-time Earnings Tracking</div>
                        <div class="feature-sub">Watch clicks and revenue update live on your dashboard</div>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Multiple Payment Models</div>
                        <div class="feature-sub">Per click, per install, or guaranteed fixed daily rate</div>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Fraud Protection Built-In</div>
                        <div class="feature-sub">Advanced detection keeps your account and earnings clean</div>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Weekly Crypto Payouts</div>
                        <div class="feature-sub">Fast, reliable payments every week without delays</div>
                    </div>
                </div>
            </div>

            <div class="stats-bar">
                <div>
                    <div class="stat-value">500+</div>
                    <div class="stat-label">Active Publishers</div>
                </div>
                <div>
                    <div class="stat-value">$2M+</div>
                    <div class="stat-label">Total Paid Out</div>
                </div>
                <div>
                    <div class="stat-value">50+</div>
                    <div class="stat-label">Countries</div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="right-panel">
        <div class="form-box">
            <div class="form-header">
                <div class="form-title">Welcome back</div>
                <div class="form-subtitle">Sign in to your Installs Bank account</div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" placeholder="you@example.com"
                               required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input type="password" name="password" class="form-control"
                               placeholder="••••••••" required>
                    </div>
                </div>
                <div class="remember-row">
                    <label class="check-label">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>
                <button type="submit" class="btn-submit">
                    Sign In
                    <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>

            <div class="divider">or</div>

            <div class="form-footer">
                Don't have an account? <a href="{{ route('register') }}">Register now</a>
            </div>

            <a href="{{ route('home') }}" class="back-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>
    </div>

</body>
</html>
