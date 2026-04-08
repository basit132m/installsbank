<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publisher Contracts — Installs Bank</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <meta name="description" content="Learn how Installs Bank publisher contracts work — Per-Click, Fixed Daily Rate, and Installs-Based. Understand your earnings before you apply.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #01BF63; --primary-dark: #00a354; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #111827; background: #fff; }

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
        .nav-links { display: flex; align-items: center; gap: 32px; }
        .nav-links a { color: #6b7280; text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.15s; }
        .nav-links a:hover { color: #111827; }
        .nav-links a.active { font-weight: 700; color: var(--primary); }
        .nav-cta { display: flex; gap: 10px; }
        .btn-login { padding: 9px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; color: #374151; border: 1.5px solid #e5e7eb; }
        .btn-login:hover { background: #f9fafb; }
        .btn-signup { padding: 9px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; color: white; background: var(--primary); }
        .btn-signup:hover { background: var(--primary-dark); }
        .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 8px; border: none; background: none; }
        .hamburger span { display: block; width: 24px; height: 2px; background: #374151; border-radius: 2px; transition: all 0.3s; }
        .mobile-menu { display: none; position: fixed; top: 70px; left: 0; right: 0; background: white; border-bottom: 1px solid #f3f4f6; padding: 16px 5%; z-index: 999; flex-direction: column; gap: 4px; }
        .mobile-menu a { color: #374151; text-decoration: none; font-size: 15px; font-weight: 500; padding: 10px 0; border-bottom: 1px solid #f9fafb; }
        .mobile-menu .mobile-cta { display: flex; gap: 10px; padding: 12px 0 4px; }
        .mobile-menu.open { display: flex; }

        .hero { padding: 130px 5% 80px; text-align: center; background: linear-gradient(180deg, #f0fdf4 0%, #fff 100%); }
        .hero-tag { display: inline-block; background: #d1fae5; color: #065f46; font-size: 12px; font-weight: 700; padding: 5px 14px; border-radius: 20px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 18px; }
        .hero h1 { font-size: clamp(34px, 5vw, 56px); font-weight: 900; color: #0f172a; line-height: 1.15; letter-spacing: -1.5px; margin-bottom: 18px; }
        .hero h1 span { color: var(--primary); }
        .hero p { font-size: 17px; color: #6b7280; line-height: 1.7; max-width: 620px; margin: 0 auto 36px; }

        .section { padding: 80px 5%; }
        .section-center { text-align: center; max-width: 700px; margin: 0 auto 50px; }
        .section-tag { display: inline-block; background: #f3f4f6; color: #374151; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px; }
        .section-title { font-size: clamp(26px, 4vw, 38px); font-weight: 900; color: #0f172a; letter-spacing: -0.8px; margin-bottom: 14px; }
        .section-sub { font-size: 15px; color: #6b7280; line-height: 1.7; }

        .contracts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 28px; max-width: 1100px; margin: 0 auto; }

        .contract-card { border-radius: 22px; overflow: hidden; border: 2px solid #e5e7eb; transition: transform 0.2s, box-shadow 0.2s; }
        .contract-card:hover { transform: translateY(-4px); box-shadow: 0 20px 48px rgba(0,0,0,0.1); }
        .contract-card-header { padding: 32px 32px 24px; }
        .contract-card-body { padding: 0 32px 32px; background: white; }

        .check-list { list-style: none; padding: 0; margin: 0 0 24px; }
        .check-list li { display: flex; align-items: flex-start; gap: 10px; padding: 7px 0; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6; }
        .check-list li:last-child { border-bottom: none; }
        .check-icon { width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; font-size: 11px; font-weight: 800; }

        .comparison-table { max-width: 1000px; margin: 0 auto; border-radius: 16px; overflow: hidden; border: 1.5px solid #e5e7eb; }
        .comparison-table table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .comparison-table th { padding: 16px 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; background: #f9fafb; text-align: left; }
        .comparison-table td { padding: 14px 20px; border-top: 1px solid #f3f4f6; color: #374151; vertical-align: top; }
        .comparison-table tr:hover td { background: #fafafa; }

        .faq { max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
        details { border: 1.5px solid #e5e7eb; border-radius: 14px; overflow: hidden; background: white; }
        details summary { padding: 20px 24px; font-size: 15px; font-weight: 700; color: #111827; cursor: pointer; list-style: none; display: flex; justify-content: space-between; align-items: center; gap: 12px; user-select: none; }
        details summary::-webkit-details-marker { display: none; }
        details summary svg { flex-shrink: 0; transition: transform 0.2s; }
        details[open] summary svg { transform: rotate(180deg); }
        details .faq-body { padding: 0 24px 20px; font-size: 14px; color: #6b7280; line-height: 1.75; border-top: 1px solid #f3f4f6; padding-top: 16px; margin-top: 0; }

        .cta-section { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); padding: 80px 5%; text-align: center; }
        .cta-section h2 { font-size: clamp(28px, 4vw, 42px); font-weight: 900; color: white; letter-spacing: -0.8px; margin-bottom: 14px; }
        .cta-section p { font-size: 16px; color: rgba(255,255,255,0.65); margin-bottom: 32px; }
        .cta-btn { display: inline-block; background: var(--primary); color: white; padding: 16px 40px; border-radius: 14px; font-size: 16px; font-weight: 700; text-decoration: none; transition: background 0.15s; }
        .cta-btn:hover { background: var(--primary-dark); }

        .footer { background: #0f172a; padding: 48px 5% 32px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 40px; max-width: 1100px; margin: 0 auto 40px; }
        .footer-brand p { font-size: 13px; color: rgba(255,255,255,0.5); line-height: 1.7; margin-top: 10px; }
        .footer-col h4 { color: white; font-size: 13px; font-weight: 700; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.06em; }
        .footer-col a { display: block; color: rgba(255,255,255,0.5); font-size: 13px; text-decoration: none; margin-bottom: 8px; }
        .footer-col a:hover { color: white; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.08); padding-top: 24px; text-align: center; font-size: 12px; color: rgba(255,255,255,0.3); max-width: 1100px; margin: 0 auto; }

        @media(max-width:768px) {
            .nav-links, .nav-cta { display: none; }
            .hamburger { display: flex; }
            .hero { padding: 100px 5% 60px; }
            .contracts-grid { grid-template-columns: 1fr; }
            .comparison-table { overflow-x: auto; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
        }
        @media(max-width:480px) {
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<nav>
    <a href="{{ route('home') }}" class="nav-brand">
        <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" onerror="this.style.display='none'">
    </a>
    <div class="nav-links">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('home') }}#how-it-works">How It Works</a>
        <a href="{{ route('contracts') }}" class="active">Contracts</a>
        <a href="{{ route('rates') }}">Click Rates</a>
        <a href="{{ route('install-rates') }}">Install Rates</a>
        <a href="{{ route('home') }}#contact">Contact</a>
    </div>
    <div class="nav-cta">
        <a href="{{ route('login') }}" class="btn-login">Sign In</a>
        <a href="{{ route('register') }}" class="btn-signup">Join Free →</a>
    </div>
    <button class="hamburger" id="hamburgerBtn" aria-label="Menu" onclick="toggleMenu()">
        <span></span><span></span><span></span>
    </button>
</nav>
<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('home') }}" onclick="closeMenu()">Home</a>
    <a href="{{ route('home') }}#how-it-works" onclick="closeMenu()">How It Works</a>
    <a href="{{ route('contracts') }}" onclick="closeMenu()">Contracts</a>
    <a href="{{ route('rates') }}" onclick="closeMenu()">Click Rates</a>
    <a href="{{ route('install-rates') }}" onclick="closeMenu()">Install Rates</a>
    <a href="{{ route('home') }}#contact" onclick="closeMenu()">Contact</a>
    <div class="mobile-cta">
        <a href="{{ route('login') }}" class="btn-login">Sign In</a>
        <a href="{{ route('register') }}" class="btn-signup">Join Free →</a>
    </div>
</div>

<!-- HERO -->
<section class="hero">
    <span class="hero-tag">📋 Publisher Contracts</span>
    <h1>Three Ways to <span>Earn</span><br>With Installs Bank</h1>
    <p>Every publisher is different. We offer three contract types tailored to your traffic and audience. Here's exactly how each one works — before you apply.</p>
    <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
        <a href="#contracts" style="background:var(--primary);color:white;padding:13px 28px;border-radius:10px;font-weight:700;text-decoration:none;font-size:15px;">See Contract Types</a>
        <a href="{{ route('register') }}" style="background:white;color:#374151;padding:13px 28px;border-radius:10px;font-weight:700;text-decoration:none;font-size:15px;border:1.5px solid #e5e7eb;">Apply Now — Free</a>
    </div>
</section>

<!-- CONTRACT TYPES -->
<section class="section" id="contracts" style="background:#f9fafb;">
    <div class="section-center">
        <span class="section-tag">Contract Types</span>
        <h2 class="section-title">Which Contract Will You Receive?</h2>
        <p class="section-sub">After a 48-hour traffic test, our team reviews your results and sends the most suitable contract offer for your traffic type. You can accept, decline, or request a change any time.</p>
    </div>

    <div class="contracts-grid">

        <!-- Per-Click -->
        <div class="contract-card" style="border-color:#bfdbfe;">
            <div class="contract-card-header" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                <div style="width:50px;height:50px;background:rgba(255,255,255,0.2);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                </div>
                <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px;">Type 1</div>
                <div style="font-size:24px;font-weight:900;color:white;margin-bottom:6px;">Per-Click</div>
                <div style="font-size:14px;color:rgba(255,255,255,0.75);">Earn for every 1,000 valid Windows clicks</div>
            </div>
            <div class="contract-card-body" style="padding-top:26px;">
                <p style="font-size:14px;color:#4b5563;line-height:1.75;margin-bottom:20px;">
                    You earn a rate for every <strong>1,000 unique valid Windows clicks</strong> that pass through your ad code. Rates vary by country — Tier 1 countries like the US, UK, Canada, and Australia pay significantly more than Tier 2/3.
                </p>
                <ul class="check-list">
                    <li>
                        <span class="check-icon" style="background:#dbeafe;color:#1d4ed8;">✓</span>
                        <div><strong>Country-based rate</strong> — Each visitor's country determines your per-click rate</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#dbeafe;color:#1d4ed8;">✓</span>
                        <div><strong>Windows clicks only</strong> — Mobile, Mac, and Linux visits do not count</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#dbeafe;color:#1d4ed8;">✓</span>
                        <div><strong>Fraud filtered</strong> — Duplicate IPs within 24h and bot traffic are removed automatically</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#dbeafe;color:#1d4ed8;">✓</span>
                        <div><strong>Real-time dashboard</strong> — Watch clicks and earnings update live as they come in</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#dbeafe;color:#1d4ed8;">✓</span>
                        <div><strong>USDT / BTC payouts</strong> — Request withdrawal once minimum threshold is reached</div>
                    </li>
                </ul>
                <div style="background:#eff6ff;border-radius:12px;padding:16px 18px;">
                    <div style="font-size:11px;font-weight:700;color:#1e40af;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.06em;">Earnings Formula</div>
                    <div style="font-size:14px;color:#1e40af;font-family:monospace;font-weight:600;">
                        Earnings = (Valid Clicks ÷ 1,000) × Country Rate
                    </div>
                    <div style="font-size:12px;color:#3b82f6;margin-top:8px;">Example: 10,000 US clicks × $0.04/click = <strong>$400</strong></div>
                </div>
                <div style="margin-top:18px;">
                    <a href="{{ route('rates') }}" style="display:inline-flex;align-items:center;gap:6px;background:#eff6ff;color:#1d4ed8;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;">
                        View All Country Rates →
                    </a>
                </div>
            </div>
        </div>

        <!-- Fixed Daily -->
        <div class="contract-card" style="border-color:#bbf7d0;">
            <div class="contract-card-header" style="background:linear-gradient(135deg,#01BF63,#007a40);">
                <div style="width:50px;height:50px;background:rgba(255,255,255,0.2);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px;">Type 2</div>
                <div style="font-size:24px;font-weight:900;color:white;margin-bottom:6px;">Fixed Daily Rate</div>
                <div style="font-size:14px;color:rgba(255,255,255,0.75);">A fixed amount credited to you every day</div>
            </div>
            <div class="contract-card-body" style="padding-top:26px;">
                <p style="font-size:14px;color:#4b5563;line-height:1.75;margin-bottom:20px;">
                    A <strong>fixed dollar amount is added to your balance every day at 00:05 UTC</strong> — regardless of click volume. Your daily rate is set when our team reviews your 48-hour test results. This is the most stable and predictable earning model.
                </p>
                <ul class="check-list">
                    <li>
                        <span class="check-icon" style="background:#dcfce7;color:#166534;">✓</span>
                        <div><strong>Guaranteed daily income</strong> — No fluctuations, no volatility</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#dcfce7;color:#166534;">✓</span>
                        <div><strong>Auto-credited at midnight</strong> — Balance updates at 00:05 UTC every day</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#dcfce7;color:#166534;">✓</span>
                        <div><strong>Test period is always paid</strong> — Even if you decline the contract, 2-day test earnings are sent</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#dcfce7;color:#166534;">✓</span>
                        <div><strong>Rate is personalized</strong> — Based on your traffic volume, countries, and quality</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#dcfce7;color:#166534;">✓</span>
                        <div><strong>Ad code must stay live</strong> — Rate may be revised if traffic drops significantly</div>
                    </li>
                </ul>
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 18px;">
                    <div style="font-size:12px;font-weight:700;color:#166534;margin-bottom:6px;">💡 Good to Know</div>
                    <div style="font-size:13px;color:#166534;line-height:1.6;">
                        Whether you accept or decline a fixed daily offer, your 2-day test period payment is always sent to your balance. You can withdraw it at any time with no minimum threshold.
                    </div>
                </div>
            </div>
        </div>

        <!-- Installs Base -->
        <div class="contract-card" style="border-color:#e9d5ff;">
            <div class="contract-card-header" style="background:linear-gradient(135deg,#7c3aed,#4c1d95);">
                <div style="width:50px;height:50px;background:rgba(255,255,255,0.2);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
                <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px;">Type 3</div>
                <div style="font-size:24px;font-weight:900;color:white;margin-bottom:6px;">Installs Base</div>
                <div style="font-size:14px;color:rgba(255,255,255,0.75);">Earn per verified app install from your traffic</div>
            </div>
            <div class="contract-card-body" style="padding-top:26px;">
                <p style="font-size:14px;color:#4b5563;line-height:1.75;margin-bottom:20px;">
                    As your Windows clicks accumulate by country, <strong>verified installs are registered</strong> and you earn the install rate for that country. This is a performance-based model for publishers who drive high-quality, conversion-ready traffic.
                </p>
                <ul class="check-list">
                    <li>
                        <span class="check-icon" style="background:#ede9fe;color:#5b21b6;">✓</span>
                        <div><strong>Per-install earnings</strong> — Paid for each verified install, not each click</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#ede9fe;color:#5b21b6;">✓</span>
                        <div><strong>Country-specific rates</strong> — Premium countries like US, UK, AU pay higher install rates</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#ede9fe;color:#5b21b6;">✓</span>
                        <div><strong>Fraud-verified installs only</strong> — Only quality installs pass our verification</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#ede9fe;color:#5b21b6;">✓</span>
                        <div><strong>Dashboard install tracker</strong> — See pending and completed installs in real-time</div>
                    </li>
                    <li>
                        <span class="check-icon" style="background:#ede9fe;color:#5b21b6;">✓</span>
                        <div><strong>USDT / BTC payouts</strong> — Withdraw once minimum threshold is met</div>
                    </li>
                </ul>
                <div style="margin-top:4px;">
                    <a href="{{ route('install-rates') }}" style="display:inline-flex;align-items:center;gap:6px;background:#ede9fe;color:#5b21b6;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;">
                        View Install Rates →
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- COMPARISON TABLE -->
<section class="section" style="background:white;">
    <div class="section-center">
        <span class="section-tag">Side by Side</span>
        <h2 class="section-title">Contract Comparison</h2>
        <p class="section-sub">Not sure which contract suits you? Here's a quick comparison of all three types.</p>
    </div>
    <div class="comparison-table">
        <table>
            <thead>
                <tr>
                    <th style="min-width:180px;">Feature</th>
                    <th style="color:#2563eb;">Per-Click</th>
                    <th style="color:#059669;">Fixed Daily</th>
                    <th style="color:#7c3aed;">Installs Base</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:600;color:#111827;">How You Earn</td>
                    <td>Per 1,000 valid clicks</td>
                    <td>Fixed amount daily</td>
                    <td>Per verified install</td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:#111827;">Earning Predictability</td>
                    <td>Variable — depends on traffic</td>
                    <td style="color:#059669;font-weight:600;">Fully predictable ✓</td>
                    <td>Variable — depends on installs</td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:#111827;">Traffic Requirement</td>
                    <td>Windows visitors</td>
                    <td>High-volume verified traffic</td>
                    <td>Windows visitors (install-intent)</td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:#111827;">Rate Basis</td>
                    <td>Country of visitor</td>
                    <td>Negotiated per publisher</td>
                    <td>Country of install</td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:#111827;">Real-time Dashboard</td>
                    <td style="color:#059669;">✓ Yes</td>
                    <td style="color:#059669;">✓ Yes</td>
                    <td style="color:#059669;">✓ Yes</td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:#111827;">Test Period Payout</td>
                    <td>N/A</td>
                    <td style="color:#059669;font-weight:600;">Always paid ✓</td>
                    <td>N/A</td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:#111827;">Best For</td>
                    <td>Sites with steady Windows traffic</td>
                    <td>High-traffic publishers wanting stability</td>
                    <td>Publishers with conversion-ready traffic</td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:#111827;">Can Request Change</td>
                    <td style="color:#059669;">✓ Any time</td>
                    <td style="color:#059669;">✓ Any time</td>
                    <td style="color:#059669;">✓ Any time</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section" style="background:#f9fafb;">
    <div class="section-center">
        <span class="section-tag">The Process</span>
        <h2 class="section-title">How Contracts Are Assigned</h2>
        <p class="section-sub">From registration to earning — here's the journey every publisher goes through.</p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;max-width:1000px;margin:0 auto;">
        @foreach([
            ['step'=>'1','color'=>'#3b82f6','bg'=>'#eff6ff','title'=>'Apply & Register','desc'=>'Sign up for free. Submit your website(s) and basic traffic info. Our team reviews your application within a few days.'],
            ['step'=>'2','color'=>'#f59e0b','bg'=>'#fffbeb','title'=>'48-Hour Test','desc'=>'Once approved, you embed our ad code and run a 48-hour traffic test. We evaluate your click volume, countries, and quality.'],
            ['step'=>'3','color'=>'#01BF63','bg'=>'#f0fdf4','title'=>'Contract Offer','desc'=>'Based on your test results, we send a tailored contract offer. You can accept, decline, or request a different type any time.'],
            ['step'=>'4','color'=>'#7c3aed','bg'=>'#faf5ff','title'=>'Start Earning','desc'=>'Once accepted, your ad goes live. Earnings track in real time on your dashboard. Withdraw to USDT or BTC any time.'],
        ] as $step)
        <div style="background:white;border:1.5px solid #e5e7eb;border-radius:18px;padding:26px;position:relative;">
            <div style="width:40px;height:40px;background:{{ $step['bg'] }};border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                <span style="font-size:18px;font-weight:900;color:{{ $step['color'] }};">{{ $step['step'] }}</span>
            </div>
            <div style="font-size:16px;font-weight:800;color:#111827;margin-bottom:8px;">{{ $step['title'] }}</div>
            <p style="font-size:13px;color:#6b7280;line-height:1.7;">{{ $step['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

<!-- FAQ -->
<section class="section" style="background:white;">
    <div class="section-center">
        <span class="section-tag">FAQ</span>
        <h2 class="section-title">Common Contract Questions</h2>
    </div>
    <div class="faq">
        @foreach([
            ['q'=>'Do I get to choose my contract type?','a'=>'Not at the start — our team assigns the most appropriate contract based on your 48-hour test results. However, once your contract is active, you can request a change to a different type from your publisher dashboard at any time. Admin reviews all change requests.'],
            ['q'=>'What happens during the 48-hour test?','a'=>'You embed our ad code and run it for 48 hours with your normal traffic. We measure click volume, visitor countries, quality signals, and fraud rate. Based on this, our team determines which contract type and rate is appropriate for your traffic.'],
            ['q'=>'Is the test period paid?','a'=>'Yes. If you are offered a Fixed Daily Rate contract and either accept or decline it, your 2-day test period earnings are always credited to your balance — with no minimum threshold to withdraw them.'],
            ['q'=>'Can I have multiple contracts active at once?','a'=>'No, you hold one active contract at a time. If you receive multiple offers, you choose one. Once a new contract is activated (including after a change request), any previous contract is archived.'],
            ['q'=>'Can my contract be terminated?','a'=>'Yes. Installs Bank reserves the right to suspend or terminate a contract if fraudulent traffic is detected, if your website no longer meets quality standards, or if you violate our Terms of Service.'],
            ['q'=>'How do I request a contract change?','a'=>'Log in to your publisher dashboard, go to Contracts in the sidebar, and use the "Request Contract Change" form. Select the contract type you want, optionally add a reason, and submit. Our admin team will review and respond.'],
            ['q'=>'What happens to my earnings when my contract changes?','a'=>'Your existing balance is untouched. A snapshot of your previous contract data (earnings, click totals, contract type) is saved permanently in your Contracts page under "Previous Contract Data". Tracking then restarts fresh under the new contract type.'],
            ['q'=>'What payment methods are available?','a'=>'We pay via USDT (TRC-20 or ERC-20) and Bitcoin (BTC). Enter your wallet address in your profile. Payments are processed manually by our team within a few business days of your withdrawal request.'],
        ] as $faq)
        <details>
            <summary>{{ $faq['q'] }}
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <div class="faq-body">{{ $faq['a'] }}</div>
        </details>
        @endforeach
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <h2>Ready to Start Earning?</h2>
    <p>Join thousands of publishers monetizing their Windows traffic with Installs Bank.</p>
    <a href="{{ route('register') }}" class="cta-btn">Create Free Account →</a>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" style="height:30px;margin-bottom:10px;" onerror="this.style.display='none'">
            <p>The leading Windows PPI network for website publishers. Trusted payouts, transparent contracts, real-time tracking.</p>
        </div>
        <div class="footer-col">
            <h4>Resources</h4>
            <a href="{{ route('contracts') }}">Contracts</a>
            <a href="{{ route('rates') }}">Click Rates</a>
            <a href="{{ route('install-rates') }}">Install Rates</a>
            <a href="{{ route('terms') }}">Terms of Service</a>
        </div>
        <div class="footer-col">
            <h4>Account</h4>
            <a href="{{ route('register') }}">Join as Publisher</a>
            <a href="{{ route('login') }}">Sign In</a>
            <a href="{{ route('home') }}#contact">Contact Us</a>
        </div>
    </div>
    <div class="footer-bottom">© {{ date('Y') }} Installs Bank. All rights reserved.</div>
</footer>

<script>
function toggleMenu() {
    document.getElementById('mobileMenu').classList.toggle('open');
}
function closeMenu() {
    document.getElementById('mobileMenu').classList.remove('open');
}
</script>
</body>
</html>
