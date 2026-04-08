<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installs Bank — Premium PPI Network | Earn With Every Click</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <meta name="description" content="Installs Bank is a premium Pay-Per-Install network offering the highest rates per click. Join thousands of publishers earning passive income daily.">
    <meta name="google-site-verification" content="1s2kQvkZBXZ-CDaAo9Q0LTIH03rruuCToC8IzhTpQrk" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #01BF63; --primary-dark: #00a354; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #111827; }

        /* NAVBAR */
        nav {
            position: fixed; top: 0; left: 0; right: 0;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #f3f4f6;
            z-index: 1000;
            padding: 0 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-brand img { height: 34px; }
        .nav-brand-text { display: none; }
        .nav-links { display: flex; align-items: center; gap: 32px; }
        .nav-links a { color: #6b7280; text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.15s; }
        .nav-links a:hover { color: #111827; }
        .nav-cta { display: flex; gap: 10px; }
        .btn-login { padding: 9px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; color: #374151; border: 1.5px solid #e5e7eb; transition: all 0.15s; }
        .btn-login:hover { background: #f9fafb; }
        .btn-signup { padding: 9px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; color: white; background: var(--primary); transition: all 0.15s; }
        .btn-signup:hover { background: var(--primary-dark); }

        /* HERO */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #f0fdf7 0%, #ffffff 40%, #e6faf2 100%);
            display: flex;
            align-items: center;
            padding: 100px 5% 80px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(1,191,99,0.1) 0%, transparent 70%);
            top: -100px; right: -100px;
            border-radius: 50%;
        }
        .hero-inner { display: flex; align-items: center; gap: 60px; width: 100%; max-width: 1200px; }
        .hero-content { max-width: 580px; position: relative; flex-shrink: 0; }
        .hero-image { flex: 1; display: flex; justify-content: flex-end; }
        .hero-image img { max-width: 480px; width: 100%; }
        @media (max-width: 900px) { .hero-inner { flex-direction: column; } .hero-image { display: none; } }
        .hero-badge { display: inline-flex; align-items: center; gap: 6px; background: #e6faf2; color: var(--primary-dark); padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; margin-bottom: 24px; border: 1px solid #a7f3d0; }
        .hero-badge::before { content: ''; width: 6px; height: 6px; background: var(--primary); border-radius: 50%; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;}50%{opacity:0.4;} }
        h1 { font-size: clamp(40px, 5vw, 64px); font-weight: 900; line-height: 1.1; color: #111827; margin-bottom: 20px; }
        h1 span { color: var(--primary); }
        .hero-desc { font-size: 18px; color: #6b7280; line-height: 1.7; margin-bottom: 36px; max-width: 520px; }
        .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; }
        .btn-hero { padding: 15px 32px; border-radius: 10px; font-size: 16px; font-weight: 700; text-decoration: none; transition: all 0.2s; }
        .btn-hero-primary { background: var(--primary); color: white; box-shadow: 0 4px 20px rgba(1,191,99,0.35); }
        .btn-hero-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 6px 24px rgba(1,191,99,0.45); }
        .btn-hero-ghost { background: white; color: #374151; border: 1.5px solid #e5e7eb; }
        .btn-hero-ghost:hover { background: #f9fafb; transform: translateY(-2px); }
        .hero-stats { display: flex; gap: 40px; margin-top: 52px; flex-wrap: wrap; }
        .hero-stat-value { font-size: 28px; font-weight: 800; color: #111827; }
        .hero-stat-label { font-size: 13px; color: #9ca3af; margin-top: 2px; }

        /* SECTION */
        .section { padding: 100px 5%; }
        .section-tag { display: inline-block; background: #e6faf2; color: var(--primary-dark); padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 16px; }
        .section-title { font-size: clamp(28px, 3vw, 42px); font-weight: 800; color: #111827; margin-bottom: 12px; }
        .section-sub { font-size: 17px; color: #6b7280; max-width: 560px; line-height: 1.7; }
        .section-center { text-align: center; }
        .section-center .section-sub { margin: 0 auto; }

        /* FEATURES */
        .features-bg { background: #f9fafb; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-top: 56px; }
        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid #f3f4f6;
            transition: all 0.2s;
        }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.08); border-color: #e5e7eb; }
        .feature-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .feature-title { font-size: 17px; font-weight: 700; margin-bottom: 8px; color: #111827; }
        .feature-text { font-size: 14px; color: #6b7280; line-height: 1.7; }

        /* HOW IT WORKS */
        .steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; margin-top: 56px; }
        .step { text-align: center; }
        .step-num { width: 56px; height: 56px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; margin: 0 auto 20px; }
        .step-title { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .step-text { font-size: 14px; color: #6b7280; line-height: 1.6; }

        /* RATES */
        .rates-table-wrap { overflow-x: auto; margin-top: 40px; }
        .rates-table { width: 100%; border-collapse: collapse; max-width: 700px; margin: 0 auto; }
        .rates-table th { padding: 14px 20px; background: #111827; color: white; font-size: 13px; font-weight: 600; text-align: left; }
        .rates-table td { padding: 14px 20px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .rates-table tr:last-child td { border-bottom: none; }
        .rates-table tr:hover td { background: #f9fafb; }
        .rate-val { color: var(--primary); font-weight: 700; }

        /* CTA SECTION */
        .cta-section {
            background: linear-gradient(135deg, #059652, #01BF63, #00d46e);
            padding: 80px 5%;
            text-align: center;
            color: white;
        }
        .cta-section h2 { font-size: clamp(28px, 4vw, 48px); font-weight: 900; margin-bottom: 16px; }
        .cta-section p { font-size: 18px; opacity: 0.9; margin-bottom: 36px; }
        .btn-cta { padding: 16px 40px; background: white; color: var(--primary-dark); border-radius: 10px; font-size: 16px; font-weight: 800; text-decoration: none; display: inline-block; transition: transform 0.2s; }
        .btn-cta:hover { transform: translateY(-2px); }

        /* FOOTER */
        footer {
            background: #111827;
            color: #9ca3af;
            padding: 56px 5% 32px;
        }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .footer-brand img { height: 30px; filter: brightness(0) invert(1); }
        .footer-brand-text { font-size: 16px; font-weight: 800; color: white; }
        .footer-desc { font-size: 14px; line-height: 1.7; max-width: 280px; }
        .footer-heading { font-size: 13px; font-weight: 700; color: white; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 16px; }
        .footer-link { display: block; font-size: 14px; color: #9ca3af; text-decoration: none; margin-bottom: 8px; transition: color 0.15s; }
        .footer-link:hover { color: var(--primary); }
        .footer-bottom { border-top: 1px solid #1f2937; padding-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .footer-copy { font-size: 13px; }

        /* APP SECTION */
        .app-section { padding: 100px 5%; background: linear-gradient(160deg, #f0fdf7 0%, #ffffff 60%); }
        .app-inner { display: flex; align-items: center; gap: 72px; max-width: 1200px; margin: 0 auto; }
        .app-content { flex: 1; }
        .app-image-wrap { flex: 1; display: flex; justify-content: center; }
        .app-image-wrap img { max-width: 340px; width: 100%; background: transparent; }
        @media (max-width: 900px) { .app-inner { flex-direction: column-reverse; } .app-image-wrap img { max-width: 240px; } }
        .app-badges { display: flex; gap: 10px; flex-wrap: wrap; margin: 24px 0 32px; }
        .app-badge { display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .badge-safe { background: #e6faf2; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-android { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-first { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .btn-download { display: inline-flex; align-items: center; gap: 10px; background: #111827; color: white; padding: 14px 28px; border-radius: 12px; font-size: 15px; font-weight: 700; text-decoration: none; transition: all 0.2s; }
        .btn-download:hover { background: #1f2937; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.18); }
        .btn-download svg { flex-shrink: 0; }

        /* CONTACT SECTION */
        .contact-section { padding: 80px 5%; background: #f9fafb; }
        .contact-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 44px; }
        .contact-card { background: white; border-radius: 16px; padding: 28px; border: 1px solid #f3f4f6; text-align: center; transition: all 0.2s; text-decoration: none; color: inherit; display: block; }
        .contact-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.08); }
        .contact-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .contact-name { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
        .contact-id { font-size: 14px; color: #6b7280; font-weight: 500; }
        .contact-action { margin-top: 14px; padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; display: inline-block; }

        /* HAMBURGER */
        .hamburger { display: none; flex-direction: column; justify-content: center; gap: 5px; cursor: pointer; padding: 6px; background: none; border: none; z-index: 1001; }
        .hamburger span { display: block; width: 24px; height: 2.5px; background: #111827; border-radius: 3px; transition: all 0.3s; }
        .hamburger.open span:nth-child(1) { transform: translateY(7.5px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7.5px) rotate(-45deg); }

        .mobile-menu {
            display: none;
            position: fixed; top: 70px; left: 0; right: 0;
            background: white;
            border-bottom: 1px solid #f3f4f6;
            z-index: 998;
            padding: 8px 5% 20px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
        }
        .mobile-menu.open { display: block; }
        .mobile-menu a { display: block; padding: 13px 0; font-size: 15px; font-weight: 600; color: #374151; text-decoration: none; border-bottom: 1px solid #f9fafb; }
        .mobile-menu a:last-child { border-bottom: none; }
        .mobile-menu .mob-cta { margin-top: 12px; display: flex; flex-direction: column; gap: 8px; }
        .mobile-menu .mob-cta a { border: none; text-align: center; border-radius: 10px; padding: 13px; }
        .mobile-menu .mob-btn-login { background: #f9fafb; border: 1.5px solid #e5e7eb !important; color: #374151; }
        .mobile-menu .mob-btn-signup { background: var(--primary); color: white; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .nav-cta { display: none; }
            .hamburger { display: flex; }
            .nav-brand-text { display: none; }

            /* Hero */
            .hero { padding: 90px 5% 60px; min-height: auto; }
            .hero-inner { flex-direction: column; gap: 32px; }
            .hero-content { max-width: 100%; }
            .hero-image { display: flex !important; justify-content: center; width: 100%; }
            .hero-image img { max-width: 280px; }
            h1 { font-size: 36px; }
            .hero-desc { font-size: 16px; }
            .hero-stats { gap: 24px; }
            .hero-stat-value { font-size: 22px; }

            /* Sections */
            .section { padding: 60px 5%; }
            .app-section { padding: 60px 5%; }
            .contact-section { padding: 60px 5%; }

            /* Footer */
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }

            /* App inner */
            .app-inner { gap: 40px; }
        }

        @media (max-width: 480px) {
            h1 { font-size: 30px; }
            .hero-image img { max-width: 220px; }
            .footer-grid { grid-template-columns: 1fr; }
            .btn-hero { padding: 13px 22px; font-size: 15px; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <a href="{{ route('home') }}" class="nav-brand">
            <img src="https://installsbank.com/images/logo.webp" alt="Installs Bank" onerror="this.style.display='none'">
            <span class="nav-brand-text">Installs Bank</span>
        </a>
        <div class="nav-links">
            <a href="{{ route('contracts') }}">Contracts</a>
            <a href="{{ route('rates') }}">Click Rates</a>
            <a href="{{ route('install-rates') }}">Install Rates</a>
        </div>
        <div class="nav-cta">
            <a href="{{ route('login') }}" class="btn-login">Sign In</a>
            <a href="{{ route('register') }}" class="btn-signup">Join Now</a>
        </div>
        <button class="hamburger" id="hamburger" aria-label="Menu" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ route('contracts') }}" onclick="closeMenu()">Contracts</a>
        <a href="{{ route('rates') }}" onclick="closeMenu()">Click Rates</a>
        <a href="{{ route('install-rates') }}" onclick="closeMenu()">Install Rates</a>
        <div class="mob-cta">
            <a href="{{ route('login') }}" class="mob-btn-login">Sign In</a>
            <a href="{{ route('register') }}" class="mob-btn-signup">Join Now — Free</a>
        </div>
    </div>
    <script>
        function toggleMenu() {
            document.getElementById('hamburger').classList.toggle('open');
            document.getElementById('mobileMenu').classList.toggle('open');
        }
        function closeMenu() {
            document.getElementById('hamburger').classList.remove('open');
            document.getElementById('mobileMenu').classList.remove('open');
        }
    </script>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-inner">
        <div class="hero-content">
            <div class="hero-badge">Live Network · Tracking Active</div>
            <h1>Earn More With <span>Every Click</span> You Drive</h1>
            <p class="hero-desc">Installs Bank is a premium Pay-Per-Install network that pays publishers for quality clicks. Get competitive rates, real-time tracking, and fast crypto payouts.</p>
            <div class="hero-cta">
                <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">Start Earning Free →</a>
                <a href="#how-it-works" class="btn-hero btn-hero-ghost">How It Works</a>
            </div>
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-value">$0.04</div>
                    <div class="hero-stat-label">Per Click (US)</div>
                </div>
                <div>
                    <div class="hero-stat-value">48h</div>
                    <div class="hero-stat-label">Test Period</div>
                </div>
                <div>
                    <div class="hero-stat-value">Crypto</div>
                    <div class="hero-stat-label">USDT / BTC Payouts</div>
                </div>
                <div>
                    <div class="hero-stat-value">24/7</div>
                    <div class="hero-stat-label">Live Tracking</div>
                </div>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://installsbank.com/images/installs-bank-banner.webp" alt="Installs Bank Platform">
        </div>
        </div>
    </section>

    <!-- Features -->
    <section class="section features-bg" id="features">
        <div class="section-center">
            <span class="section-tag">Why Choose Us</span>
            <h2 class="section-title">Everything You Need to Maximize Earnings</h2>
            <p class="section-sub">Our platform is built for publishers who want transparency, fair rates, and the tools to grow their revenue.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background:#e6faf2;">
                    <svg width="24" height="24" fill="none" stroke="#01BF63" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                </div>
                <div class="feature-title">Real-Time Click Tracking</div>
                <div class="feature-text">Monitor your click performance live. See OS, country, and device breakdowns updated in real time on your dashboard.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#dbeafe;">
                    <svg width="24" height="24" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="feature-title">Advanced Fraud Protection</div>
                <div class="feature-text">Our system detects VPNs, proxies, bots, and duplicate clicks so only genuine traffic is counted. You earn fair, you earn right.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#fef3c7;">
                    <svg width="24" height="24" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <div class="feature-title">Country-Based Rates</div>
                <div class="feature-text">We pay different rates based on where your traffic comes from. US, UK, CA, AU clicks earn premium rates.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#ede9fe;">
                    <svg width="24" height="24" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="feature-title">Crypto Payouts</div>
                <div class="feature-text">Withdraw your earnings in USDT (TRC20/ERC20) or Bitcoin. Fast, secure, borderless payments worldwide.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#fce7f3;">
                    <svg width="24" height="24" fill="none" stroke="#ec4899" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="feature-title">Flexible Contracts</div>
                <div class="feature-text">After your 48-hour test, choose between per-1000-clicks pricing or a guaranteed fixed daily rate. You decide what works best.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#f0fdf4;">
                    <svg width="24" height="24" fill="none" stroke="#22c55e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <div class="feature-title">Simple Embed Code</div>
                <div class="feature-text">Get a beautiful, customizable button code to embed on your website. No coding knowledge required — just copy and paste.</div>
            </div>
        </div>
    </section>

    <!-- Mobile App -->
    <section class="app-section" id="mobile-app">
        <div class="app-inner">
            <div class="app-content">
                <span class="section-tag">📱 Exclusive Feature</span>
                <h2 class="section-title" style="margin-top:12px;">The <span style="color:var(--primary);">First & Only</span> PPI Network With a Built-In Android App</h2>
                <p style="font-size:17px;color:#6b7280;line-height:1.7;max-width:520px;margin-top:12px;">
                    Track your clicks, check earnings, manage withdrawals, and contact support — all from your phone. No other PPI network gives you this level of control on the go.
                </p>
                <div class="app-badges">
                    <span class="app-badge badge-first">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                        First in the Industry
                    </span>
                    <span class="app-badge badge-safe">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        100% Safe &amp; Scanned
                    </span>
                    <span class="app-badge badge-android">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Android
                    </span>
                </div>
                <a href="https://www.dropbox.com/scl/fi/yzst8544qtrao2jyh2i6m/Installs-Bank.apk?rlkey=52qgw1sai1ofwu2z1g88afn4y&st=dfzys0ri&dl=1" target="_blank" rel="noopener" class="btn-download">
                    <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M17.523 15.341a.75.75 0 01-1.06 0l-3.713-3.714v7.623a.75.75 0 01-1.5 0V11.627L7.537 15.341a.75.75 0 11-1.06-1.061l5-5a.75.75 0 011.06 0l5 5a.75.75 0 010 1.061zM4.5 3.75A.75.75 0 015.25 3h13.5a.75.75 0 010 1.5H5.25a.75.75 0 01-.75-.75z"/></svg>
                    Download APK — Free
                </a>
                <p style="font-size:12px;color:#9ca3af;margin-top:12px;">✓ No registration required to download &nbsp;·&nbsp; ✓ Virus scanned &nbsp;·&nbsp; ✓ Android 7.0+</p>
            </div>
            <div class="app-image-wrap">
                <img src="https://installsbank.com/images/installsbank-app.webp" alt="Installs Bank Android App" loading="lazy" onerror="this.style.opacity='0.3'">
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section" id="how-it-works">
        <div class="section-center">
            <span class="section-tag">Process</span>
            <h2 class="section-title">Get Started in 4 Simple Steps</h2>
            <p class="section-sub">From registration to earning — our streamlined process gets you monetizing traffic fast.</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-title">Register Free</div>
                <div class="step-text">Create your publisher account. Our team reviews and approves your application within hours.</div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-title">48-Hour Test</div>
                <div class="step-text">We analyze your traffic quality — source, OS, country, and click patterns — over 48 hours.</div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-title">Accept Your Contract</div>
                <div class="step-text">Based on your test results, we offer you a customized per-click or fixed daily rate contract.</div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-title">Earn & Withdraw</div>
                <div class="step-text">Embed your ad code, drive traffic, watch earnings grow in real time, and withdraw to crypto.</div>
            </div>
        </div>
    </section>

    <!-- Contracts -->
    <section class="section" id="contracts" style="background:#fff;">
        <div class="section-center">
            <span class="section-tag">📋 Contracts</span>
            <h2 class="section-title">Three Ways to <span style="color:var(--primary);">Earn</span></h2>
            <p class="section-sub">After your traffic test, we offer you the contract type that best fits your traffic. You can receive multiple offers and choose the one that works for you.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:24px;max-width:1100px;margin:40px auto 0;padding:0 5%;">

            <!-- Per-Click Contract -->
            <div style="background:white;border:2px solid #e5e7eb;border-radius:20px;padding:32px;position:relative;overflow:hidden;transition:all .2s;" onmouseover="this.style.borderColor='#a7f3d0';this.style.boxShadow='0 8px 32px rgba(1,191,99,0.12)'" onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow=''">
                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#01BF63,#00a354);"></div>
                <div style="width:52px;height:52px;background:#e6faf2;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                    <svg width="26" height="26" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                </div>
                <div style="font-size:11px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Per-Click Contract</div>
                <div style="font-size:22px;font-weight:900;color:#111827;margin-bottom:12px;">Earn Per 1,000 Clicks</div>
                <p style="font-size:14px;color:#6b7280;line-height:1.7;margin-bottom:20px;">You earn a fixed dollar amount for every 1,000 unique Windows clicks you deliver. The rate depends on your traffic countries — Tier 1 countries (US, UK, CA, AU) pay the most.</p>
                <ul style="list-style:none;padding:0;margin-bottom:24px;">
                    <li style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:#374151;"><span style="color:#01BF63;font-weight:700;flex-shrink:0;">✓</span> Rate based on country of traffic</li>
                    <li style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:#374151;"><span style="color:#01BF63;font-weight:700;flex-shrink:0;">✓</span> Windows clicks only</li>
                    <li style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:#374151;"><span style="color:#01BF63;font-weight:700;flex-shrink:0;">✓</span> Real-time earnings dashboard</li>
                    <li style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#374151;"><span style="color:#01BF63;font-weight:700;flex-shrink:0;">✓</span> Weekly USDT / BTC payouts</li>
                </ul>
                <a href="{{ route('rates') }}" style="display:inline-flex;align-items:center;gap:6px;background:#e6faf2;color:#065f46;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;">
                    View Click Rates →
                </a>
            </div>

            <!-- Fixed Daily Rate Contract -->
            <div style="background:linear-gradient(160deg,#0f172a 0%,#1e3a5f 100%);border:2px solid #1e3a5f;border-radius:20px;padding:32px;position:relative;overflow:hidden;transition:all .2s;" onmouseover="this.style.boxShadow='0 8px 32px rgba(0,0,0,0.3)'" onmouseout="this.style.boxShadow=''">
                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#f59e0b,#d97706);"></div>
                <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,0.03);border-radius:50%;"></div>
                <div style="width:52px;height:52px;background:rgba(245,158,11,0.15);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;border:1px solid rgba(245,158,11,0.3);">
                    <svg width="26" height="26" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div style="font-size:11px;font-weight:700;color:#f59e0b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Fixed Daily Contract</div>
                <div style="font-size:22px;font-weight:900;color:white;margin-bottom:12px;">Guaranteed Daily Rate</div>
                <p style="font-size:14px;color:rgba(255,255,255,0.65);line-height:1.7;margin-bottom:20px;">After a successful 48-hour traffic test, we offer you a fixed amount paid every single day — regardless of click volume. Predictable, stable income you can count on.</p>
                <ul style="list-style:none;padding:0;margin-bottom:24px;">
                    <li style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:rgba(255,255,255,0.8);"><span style="color:#f59e0b;font-weight:700;flex-shrink:0;">✓</span> Fixed daily amount — no surprises</li>
                    <li style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:rgba(255,255,255,0.8);"><span style="color:#f59e0b;font-weight:700;flex-shrink:0;">✓</span> Offered after 48-hour test period</li>
                    <li style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:rgba(255,255,255,0.8);"><span style="color:#f59e0b;font-weight:700;flex-shrink:0;">✓</span> Test period payment paid regardless</li>
                    <li style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:rgba(255,255,255,0.8);"><span style="color:#f59e0b;font-weight:700;flex-shrink:0;">✓</span> Rate is non-negotiable — final offer</li>
                </ul>
                <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:12px 16px;font-size:12px;color:rgba(255,255,255,0.6);line-height:1.6;">
                    💡 Whether you accept or reject the offer, your 2-day test period payment will always be sent to you.
                </div>
            </div>

            <!-- Installs-Based Contract -->
            <div style="background:white;border:2px solid #e5e7eb;border-radius:20px;padding:32px;position:relative;overflow:hidden;transition:all .2s;" onmouseover="this.style.borderColor='#c4b5fd';this.style.boxShadow='0 8px 32px rgba(139,92,246,0.12)'" onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow=''">
                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#8b5cf6,#6d28d9);"></div>
                <div style="width:52px;height:52px;background:#ede9fe;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                    <svg width="26" height="26" fill="none" stroke="#7c3aed" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div style="font-size:11px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Installs-Based Contract</div>
                <div style="font-size:22px;font-weight:900;color:#111827;margin-bottom:12px;">Earn Per Verified Install</div>
                <p style="font-size:14px;color:#6b7280;line-height:1.7;margin-bottom:20px;">Your earnings are based on verified installs. As your Windows clicks accumulate by country, installs are registered and you're paid the install rate for that country.</p>
                <ul style="list-style:none;padding:0;margin-bottom:24px;">
                    <li style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:#374151;"><span style="color:#7c3aed;font-weight:700;flex-shrink:0;">✓</span> Country-specific install rates</li>
                    <li style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:#374151;"><span style="color:#7c3aed;font-weight:700;flex-shrink:0;">✓</span> Higher rates for premium countries</li>
                    <li style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:#374151;"><span style="color:#7c3aed;font-weight:700;flex-shrink:0;">✓</span> Real-time install & earnings tracking</li>
                    <li style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#374151;"><span style="color:#7c3aed;font-weight:700;flex-shrink:0;">✓</span> Weekly USDT / BTC payouts</li>
                </ul>
                <a href="{{ route('install-rates') }}" style="display:inline-flex;align-items:center;gap:6px;background:#ede9fe;color:#5b21b6;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;">
                    View Install Rates →
                </a>
            </div>

        </div>
    </section>

    <!-- Rates -->
    <section class="section features-bg" id="rates">
        <div class="section-center">
            <span class="section-tag">Earnings</span>
            <h2 class="section-title">Competitive Rates by Country</h2>
            <p class="section-sub">Our rates are among the highest in the PPI market. The more premium traffic you send, the more you earn.</p>
        </div>
        <div class="rates-table-wrap">
            <table class="rates-table">
                <thead>
                    <tr><th>Tier</th><th>Countries</th><th>Rate per Click</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span style="background:#e6faf2;color:#065f46;padding:4px 14px;border-radius:12px;font-size:13px;font-weight:700;">Tier 1</span></td>
                        <td style="color:#374151;">US, UK, CA, AU, DE, FR, NL, SE, NO, DK</td>
                        <td class="rate-val" style="font-size:18px;">$0.04 <span style="font-size:13px;color:#9ca3af;font-weight:500;">per click</span></td>
                    </tr>
                    <tr>
                        <td><span style="background:#dbeafe;color:#1e40af;padding:4px 14px;border-radius:12px;font-size:13px;font-weight:700;">Tier 2</span></td>
                        <td style="color:#374151;">ES, IT, PT, PL, CZ, HU, RO, GR, TR, AE</td>
                        <td class="rate-val" style="font-size:18px;">$0.03 <span style="font-size:13px;color:#9ca3af;font-weight:500;">per click</span></td>
                    </tr>
                    <tr>
                        <td><span style="background:#f3f4f6;color:#6b7280;padding:4px 14px;border-radius:12px;font-size:13px;font-weight:700;">Tier 3</span></td>
                        <td style="color:#374151;">All other countries</td>
                        <td class="rate-val" style="font-size:18px;">$0.02 <span style="font-size:13px;color:#9ca3af;font-weight:500;">per click</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="text-align:center;margin-top:24px;font-size:13px;color:#9ca3af;">Rates apply to Windows clicks only. Your exact tier is confirmed after the 48-hour test period.</div>
    </section>


    <!-- Contact -->
    <section class="contact-section" id="contact">
        <div class="section-center">
            <span class="section-tag">Get In Touch</span>
            <h2 class="section-title">Contact Our Team</h2>
            <p class="section-sub">Have questions? Reach us directly on your preferred platform. We typically respond within a few hours.</p>
        </div>
        <div class="contact-cards">
            <a href="https://wa.me/19707426488?text=Hello%20Installs%20Bank%20team%2C%20I%20found%20your%20network%20and%20would%20like%20to%20know%20more." target="_blank" rel="noopener" class="contact-card">
                <div class="contact-icon" style="background:#d1fae5;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <div class="contact-name">WhatsApp</div>
                <div class="contact-id">+1 (970) 742-6488</div>
                <span class="contact-action" style="background:#d1fae5;color:#065f46;">Message Us</span>
            </a>
            <a href="https://t.me/installsbank" target="_blank" rel="noopener" class="contact-card">
                <div class="contact-icon" style="background:#dbeafe;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="#229ED9"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                </div>
                <div class="contact-name">Telegram</div>
                <div class="contact-id">@installsbank</div>
                <span class="contact-action" style="background:#dbeafe;color:#1e40af;">Open Chat</span>
            </a>
            <div class="contact-card" style="cursor:default;">
                <div class="contact-icon" style="background:#d1fae5;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="#07C160"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 01.213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 00.167-.054l1.903-1.114a.864.864 0 01.717-.098 10.16 10.16 0 002.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348zM5.785 5.991c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 01-1.162 1.178A1.17 1.17 0 014.623 7.17c0-.651.52-1.18 1.162-1.18zm5.813 0c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 01-1.162 1.178 1.17 1.17 0 01-1.162-1.178c0-.651.52-1.18 1.162-1.18zm5.34 2.867c-1.797-.052-3.746.512-5.28 1.786-1.72 1.428-2.687 3.72-1.78 6.22.942 2.453 3.666 4.229 6.884 4.229.826 0 1.622-.12 2.361-.336a.722.722 0 01.598.082l1.584.926a.272.272 0 00.14.045c.134 0 .24-.111.24-.247 0-.06-.023-.12-.038-.177l-.327-1.233a.582.582 0 01-.023-.156.49.49 0 01.201-.398C23.024 18.48 24 16.82 24 14.98c0-3.21-2.931-5.837-7.062-6.122zm-3.518 3.064c.535 0 .969.44.969.982a.976.976 0 01-.969.983.976.976 0 01-.969-.983c0-.542.434-.982.969-.982zm4.965 0c.535 0 .969.44.969.982a.976.976 0 01-.969.983.976.976 0 01-.969-.983c0-.542.434-.982.969-.982z"/></svg>
                </div>
                <div class="contact-name">WeChat</div>
                <div class="contact-id">+1 (970) 742-6488</div>
                <span class="contact-action" style="background:#d1fae5;color:#065f46;" onclick="navigator.clipboard.writeText('+19707426488');this.textContent='Copied!';setTimeout(()=>this.textContent='Copy ID',1500);">Copy ID</span>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <h2>Ready to Start Earning?</h2>
        <p>Join our publisher network today. No upfront costs. No hidden fees. Just real earnings.</p>
        <a href="{{ route('register') }}" class="btn-cta">Create Your Free Account →</a>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <span class="footer-brand-text">Installs Bank</span>
                </div>
                <p class="footer-desc">A premium PPI network connecting publishers with high-paying click opportunities. Trusted, transparent, and built for growth.</p>
            </div>
            <div>
                <div class="footer-heading">Publishers</div>
                <a href="{{ route('register') }}" class="footer-link">Register</a>
                <a href="{{ route('login') }}" class="footer-link">Login</a>
                <a href="#how-it-works" class="footer-link">How It Works</a>
                <a href="{{ route('rates') }}" class="footer-link">Click Rates</a>
            <a href="{{ route('install-rates') }}" class="footer-link">Install Rates</a>
            </div>
            <div>
                <div class="footer-heading">Support</div>
                <a href="{{ route('login') }}" class="footer-link">Contact Support</a>
                <a href="{{ route('terms') }}" class="footer-link">Terms of Use</a>
                <a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a>
                <a href="https://www.dropbox.com/scl/fi/yzst8544qtrao2jyh2i6m/Installs-Bank.apk?rlkey=52qgw1sai1ofwu2z1g88afn4y&st=dfzys0ri&dl=1" target="_blank" class="footer-link">📱 Download Android App</a>
            </div>
            <div>
                <div class="footer-heading">Contact Us</div>
                <a href="https://wa.me/19707426488" target="_blank" class="footer-link">💬 WhatsApp</a>
                <a href="https://t.me/installsbank" target="_blank" class="footer-link">✈️ Telegram @installsbank</a>
                <a href="#" class="footer-link" onclick="navigator.clipboard.writeText('+19707426488');return false;">💚 WeChat: +19707426488</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span class="footer-copy">© {{ date('Y') }} Installs Bank. All rights reserved.</span>
            <span style="font-size:13px;">USDT · BTC Payments Accepted</span>
        </div>
    </footer>
</body>
</html>
