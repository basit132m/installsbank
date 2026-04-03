<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installs Bank — Premium PPI Network | Earn With Every Click</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <meta name="description" content="Installs Bank is a premium Pay-Per-Install network offering the highest rates per click. Join thousands of publishers earning passive income daily.">
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
        .nav-brand-text { font-size: 18px; font-weight: 800; color: #111827; }
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
        .hero-image img { max-width: 480px; width: 100%; border-radius: 16px; box-shadow: 0 24px 80px rgba(1,191,99,0.18); }
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
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .footer-brand img { height: 30px; filter: brightness(0) invert(1); }
        .footer-brand-text { font-size: 16px; font-weight: 800; color: white; }
        .footer-desc { font-size: 14px; line-height: 1.7; max-width: 280px; }
        .footer-heading { font-size: 13px; font-weight: 700; color: white; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 16px; }
        .footer-link { display: block; font-size: 14px; color: #9ca3af; text-decoration: none; margin-bottom: 8px; transition: color 0.15s; }
        .footer-link:hover { color: var(--primary); }
        .footer-bottom { border-top: 1px solid #1f2937; padding-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .footer-copy { font-size: 13px; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .footer-grid { grid-template-columns: 1fr; }
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
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#rates">Rates</a>
        </div>
        <div class="nav-cta">
            <a href="{{ route('login') }}" class="btn-login">Sign In</a>
            <a href="{{ route('register') }}" class="btn-signup">Join Now</a>
        </div>
    </nav>

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
                    <div class="hero-stat-value">$0.50+</div>
                    <div class="hero-stat-label">Per 1K Clicks (US)</div>
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
            <img src="https://installsbank.com/images/installs-bank-hero.webp" alt="Installs Bank Platform">
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
                    <tr><th>Country</th><th>Rate per 1,000 Clicks</th><th>Tier</th></tr>
                </thead>
                <tbody>
                    <tr><td>🇺🇸 United States</td><td class="rate-val">Up to $0.80</td><td><span style="background:#e6faf2;color:#065f46;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">Tier 1</span></td></tr>
                    <tr><td>🇬🇧 United Kingdom</td><td class="rate-val">Up to $0.70</td><td><span style="background:#e6faf2;color:#065f46;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">Tier 1</span></td></tr>
                    <tr><td>🇨🇦 Canada</td><td class="rate-val">Up to $0.65</td><td><span style="background:#e6faf2;color:#065f46;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">Tier 1</span></td></tr>
                    <tr><td>🇦🇺 Australia</td><td class="rate-val">Up to $0.60</td><td><span style="background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">Tier 2</span></td></tr>
                    <tr><td>🇩🇪 Germany</td><td class="rate-val">Up to $0.55</td><td><span style="background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">Tier 2</span></td></tr>
                    <tr><td>🌍 Other Countries</td><td class="rate-val">Custom Rate</td><td><span style="background:#f3f4f6;color:#6b7280;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">Tier 3</span></td></tr>
                </tbody>
            </table>
        </div>
        <div style="text-align:center;margin-top:24px;font-size:13px;color:#9ca3af;">Rates shown are indicative. Your exact rate is determined after your 48-hour test period.</div>
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
                    <img src="https://installsbank.com/images/logo.webp" alt="Installs Bank" onerror="this.style.display='none'">
                    <span class="footer-brand-text">Installs Bank</span>
                </div>
                <p class="footer-desc">A premium PPI network connecting publishers with high-paying click opportunities. Trusted, transparent, and built for growth.</p>
            </div>
            <div>
                <div class="footer-heading">Publishers</div>
                <a href="{{ route('register') }}" class="footer-link">Register</a>
                <a href="{{ route('login') }}" class="footer-link">Login</a>
                <a href="#how-it-works" class="footer-link">How It Works</a>
                <a href="#rates" class="footer-link">Rates</a>
            </div>
            <div>
                <div class="footer-heading">Support</div>
                <a href="{{ route('login') }}" class="footer-link">Contact Support</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Privacy Policy</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span class="footer-copy">© {{ date('Y') }} Installs Bank. All rights reserved.</span>
            <span style="font-size:13px;">USDT · BTC Payments Accepted</span>
        </div>
    </footer>
</body>
</html>
