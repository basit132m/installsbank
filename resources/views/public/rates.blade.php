<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Country Rates — Installs Bank</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <meta name="description" content="View Installs Bank's current pay-per-click rates by country. See exactly how much you earn per click from each country.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #01BF63; --primary-dark: #00a354; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #111827; background: #fff; }

        /* NAV */
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
        .nav-links a:hover, .nav-links a.active { color: #111827; }
        .nav-links a.active { font-weight: 700; color: var(--primary); }
        .nav-cta { display: flex; gap: 10px; }
        .btn-login { padding: 9px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; color: #374151; border: 1.5px solid #e5e7eb; transition: all 0.15s; }
        .btn-login:hover { background: #f9fafb; }
        .btn-signup { padding: 9px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; color: white; background: var(--primary); transition: all 0.15s; }
        .btn-signup:hover { background: var(--primary-dark); }
        .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 8px; border: none; background: none; }
        .hamburger span { display: block; width: 24px; height: 2px; background: #374151; border-radius: 2px; transition: all 0.3s; }
        .mobile-menu { display: none; position: fixed; top: 70px; left: 0; right: 0; background: white; border-bottom: 1px solid #f3f4f6; padding: 16px 5%; z-index: 999; flex-direction: column; gap: 4px; }
        .mobile-menu a { color: #374151; text-decoration: none; font-size: 15px; font-weight: 500; padding: 10px 0; border-bottom: 1px solid #f9fafb; }
        .mobile-menu .mobile-cta { display: flex; gap: 10px; padding: 12px 0 4px; }
        .mobile-menu .mobile-cta a { border: none; padding: 10px 0; }
        .mobile-menu.open { display: flex; }

        /* HERO */
        .hero {
            padding: 120px 5% 80px;
            background: linear-gradient(135deg, #f0fdf7 0%, #ffffff 40%, #e6faf2 100%);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(1,191,99,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .section-tag { display: inline-block; background: #e6faf2; color: var(--primary); padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 16px; }
        .hero h1 { font-size: clamp(28px, 5vw, 52px); font-weight: 900; line-height: 1.1; color: #111827; margin-bottom: 20px; }
        .hero h1 span { color: var(--primary); }
        .hero p { font-size: 17px; color: #6b7280; max-width: 580px; margin: 0 auto 36px; line-height: 1.7; }
        .hero-stats { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; margin-top: 12px; }
        .hero-stat { text-align: center; }
        .hero-stat-val { font-size: 28px; font-weight: 900; color: #111827; }
        .hero-stat-val span { color: var(--primary); }
        .hero-stat-label { font-size: 12px; color: #9ca3af; font-weight: 500; margin-top: 4px; }

        /* SEARCH */
        .search-wrap { max-width: 520px; margin: 0 auto 48px; position: relative; }
        .search-input { width: 100%; padding: 14px 20px 14px 48px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; font-family: inherit; outline: none; transition: all 0.15s; color: #111827; background: white; box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
        .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(1,191,99,0.08); }
        .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
        .search-count { text-align: center; font-size: 13px; color: #9ca3af; margin-top: -36px; margin-bottom: 36px; }

        /* MAIN CONTENT */
        .content { padding: 0 5% 80px; }
        .content-inner { max-width: 900px; margin: 0 auto; }

        /* RATE CARDS GRID */
        .rates-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; margin-bottom: 48px; }
        .rate-card {
            background: white;
            border: 1.5px solid #f3f4f6;
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.2s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .rate-card:hover { border-color: #a7f3d0; box-shadow: 0 4px 16px rgba(1,191,99,0.1); transform: translateY(-2px); }
        .rate-card.top-rate { border-color: #a7f3d0; background: #f0fdf9; }
        .flag-img { width: 32px; height: 24px; border-radius: 4px; object-fit: cover; box-shadow: 0 1px 4px rgba(0,0,0,0.1); flex-shrink: 0; }
        .flag-fallback { width: 32px; height: 24px; border-radius: 4px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .country-info { flex: 1; min-width: 0; }
        .country-name { font-size: 14px; font-weight: 700; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .country-code { font-size: 11px; color: #9ca3af; font-weight: 600; margin-top: 1px; }
        .rate-amount { text-align: right; flex-shrink: 0; }
        .rate-val { font-size: 18px; font-weight: 800; color: var(--primary); }
        .rate-label { font-size: 10px; color: #9ca3af; font-weight: 500; margin-top: 1px; white-space: nowrap; }
        .top-badge { display: inline-block; background: #e6faf2; color: #065f46; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; margin-top: 3px; }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .empty-state svg { margin: 0 auto 16px; display: block; }
        .empty-state h3 { font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 8px; }

        /* INFO BANNER */
        .info-banner { background: linear-gradient(135deg, #f0fdf7, #e6faf2); border: 1px solid #a7f3d0; border-radius: 14px; padding: 24px 28px; margin-bottom: 36px; display: flex; align-items: flex-start; gap: 16px; }
        .info-icon { width: 42px; height: 42px; background: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

        /* FOOTER */
        footer { background: #0f172a; color: #94a3b8; padding: 60px 5% 30px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 48px; }
        .footer-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .footer-brand-text { font-size: 18px; font-weight: 800; color: white; }
        .footer-desc { font-size: 14px; line-height: 1.7; max-width: 280px; }
        .footer-heading { font-size: 13px; font-weight: 700; color: white; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px; }
        .footer-link { display: block; color: #94a3b8; text-decoration: none; font-size: 14px; margin-bottom: 10px; transition: color 0.15s; }
        .footer-link:hover { color: white; }
        .footer-bottom { border-top: 1px solid #1e293b; padding-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; font-size: 13px; }
        .footer-copy { color: #64748b; }

        /* MOBILE */
        @media(max-width: 768px) {
            .nav-links, .nav-cta { display: none; }
            .hamburger { display: flex; }
            .hero { padding: 100px 5% 60px; }
            .hero p { font-size: 15px; }
            .hero-stats { gap: 24px; }
            .rates-grid { grid-template-columns: 1fr 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
        }
        @media(max-width: 480px) {
            .rates-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .info-banner { flex-direction: column; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="{{ route('home') }}" class="nav-brand">
        <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" onerror="this.style.display='none'">
        <span class="nav-brand-text">Installs Bank</span>
    </a>
    <div class="nav-links">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('home') }}#how-it-works">How It Works</a>
        <a href="{{ route('home') }}#contracts">Contracts</a>
        <a href="{{ route('rates') }}" class="active">Click Rates</a>
        <a href="{{ route('install-rates') }}">Install Rates</a>
        <a href="{{ route('home') }}#contact">Contact</a>
    </div>
    <div class="nav-cta">
        <a href="{{ route('login') }}" class="btn-login">Sign In</a>
        <a href="{{ route('register') }}" class="btn-signup">Join Free →</a>
    </div>
    <button class="hamburger" id="hamburgerBtn" aria-label="Menu" onclick="toggleMenu()">
        <span id="hb1"></span><span id="hb2"></span><span id="hb3"></span>
    </button>
</nav>
<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('home') }}" onclick="closeMenu()">Home</a>
    <a href="{{ route('home') }}#how-it-works" onclick="closeMenu()">How It Works</a>
    <a href="{{ route('home') }}#contracts" onclick="closeMenu()">Contracts</a>
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
    <span class="section-tag">💰 Live Rates</span>
    <h1>Earn <span>More</span> Per Click.<br>Every Country. Every Day.</h1>
    <p>Our rates are among the highest in the PPI market. All rates below are live and updated directly by our team.</p>
    <div class="hero-stats">
        <div class="hero-stat">
            <div class="hero-stat-val"><span>${{ $topRate > 0 ? number_format($topRate, 2) : '0.04' }}</span></div>
            <div class="hero-stat-label">Top Rate Per Click</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-val"><span>{{ $rates->count() }}</span></div>
            <div class="hero-stat-label">Countries Covered</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-val">Win/<span>Mac</span></div>
            <div class="hero-stat-label">Traffic Accepted</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-val">BTC/<span>USDT</span></div>
            <div class="hero-stat-label">Payout Methods</div>
        </div>
    </div>
</section>

<!-- CONTENT -->
<section class="content">
    <div class="content-inner">

        <!-- Info Banner -->
        <div class="info-banner">
            <div class="info-icon">
                <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#065f46;margin-bottom:4px;">How Rates Work</div>
                <p style="font-size:14px;color:#374151;line-height:1.7;margin:0;">
                    Rates shown are per single Windows click from each country. Your exact rate is confirmed after a 48-hour test period.
                    Mac device clicks are paid separately — see the Mac Rates section below. Rates are updated live by our team.
                </p>
            </div>
        </div>

        <!-- Search -->
        <div class="search-wrap">
            <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="searchInput" class="search-input" placeholder="Search country name or code..." oninput="filterRates()">
        </div>
        <div class="search-count" id="searchCount">Showing {{ $rates->count() }} countries</div>

        @if($rates->isEmpty())
            <div class="empty-state">
                <svg width="48" height="48" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                <h3>No Rates Available Yet</h3>
                <p>Our team is setting up rates. Please check back soon.</p>
            </div>
        @else
            <!-- Rates Grid -->
            <div class="rates-grid" id="ratesGrid">
                @foreach($rates as $rate)
                <div class="rate-card {{ $rate->rate_per_click == $topRate ? 'top-rate' : '' }}"
                     data-name="{{ strtolower($rate->country_name) }}"
                     data-code="{{ strtolower($rate->country_code) }}">
                    <img class="flag-img"
                         src="https://flagcdn.com/32x24/{{ strtolower($rate->country_code) }}.png"
                         alt="{{ $rate->country_name }} flag"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="flag-fallback" style="display:none;">🌍</div>
                    <div class="country-info">
                        <div class="country-name">{{ $rate->country_name }}</div>
                        <div class="country-code">{{ strtoupper($rate->country_code) }}</div>
                        @if($rate->rate_per_click == $topRate)
                            <span class="top-badge">TOP RATE</span>
                        @endif
                    </div>
                    <div class="rate-amount">
                        <div class="rate-val">${{ number_format($rate->rate_per_click, 2) }}</div>
                        <div class="rate-label">per click</div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- No results (hidden by default) -->
            <div id="noResults" style="display:none;" class="empty-state">
                <svg width="40" height="40" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3>No countries found</h3>
                <p>Try a different search term.</p>
            </div>
        @endif

        <!-- Mac Rates Section -->
        <div style="margin-top:60px;">
            <div style="text-align:center;margin-bottom:32px;">
                <span style="display:inline-block;background:#f5f3ff;color:#6d28d9;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;margin-bottom:16px;">Mac Rates</span>
                <h2 style="font-size:28px;font-weight:900;color:#111827;line-height:1.2;margin-bottom:12px;">Mac <span style="color:#8b5cf6;">Click</span> Rates by Country</h2>
                <p style="font-size:15px;color:#6b7280;max-width:520px;margin:0 auto;">We now pay for both Windows and Mac device clicks. Rates below apply to valid Mac clicks from each country.</p>
            </div>

            <!-- Mac Info Banner -->
            <div style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);border:1px solid #c4b5fd;border-radius:14px;padding:20px 24px;margin-bottom:28px;display:flex;align-items:flex-start;gap:14px;">
                <div style="width:40px;height:40px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#4c1d95;margin-bottom:4px;">Mac Rates Info</div>
                    <p style="font-size:14px;color:#374151;line-height:1.7;margin:0;">
                        Rates shown are per single Mac/macOS click from each country. Mac clicks are tracked separately from Windows clicks and credited at their own rate.
                    </p>
                </div>
            </div>

            <!-- Mac Search -->
            <div class="search-wrap">
                <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="macSearchInput" class="search-input" placeholder="Search Mac country..." oninput="filterMacRates()" style="border-color:#c4b5fd;">
            </div>
            <div class="search-count" id="macSearchCount">Showing {{ $macRates->count() }} countries</div>

            @if($macRates->isEmpty())
                <div class="empty-state">
                    <svg width="48" height="48" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    <h3>No Mac Rates Available Yet</h3>
                    <p>Our team is setting up Mac rates. Please check back soon.</p>
                </div>
            @else
                <div class="rates-grid" id="macRatesGrid">
                    @foreach($macRates as $rate)
                    <div class="rate-card {{ $rate->mac_rate_per_click == $topMacRate ? 'top-rate' : '' }}"
                         style="{{ $rate->mac_rate_per_click == $topMacRate ? '' : 'border-color:#f3f4f6;' }} {{ $rate->mac_rate_per_click == $topMacRate ? '' : '' }}"
                         data-mac-name="{{ strtolower($rate->country_name) }}"
                         data-mac-code="{{ strtolower($rate->country_code) }}">
                        <img class="flag-img"
                             src="https://flagcdn.com/32x24/{{ strtolower($rate->country_code) }}.png"
                             alt="{{ $rate->country_name }} flag"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="flag-fallback" style="display:none;">🌍</div>
                        <div class="country-info">
                            <div class="country-name">{{ $rate->country_name }}</div>
                            <div class="country-code">{{ strtoupper($rate->country_code) }}</div>
                            @if($rate->mac_rate_per_click == $topMacRate)
                                <span class="top-badge" style="background:#f5f3ff;color:#5b21b6;">TOP MAC</span>
                            @endif
                        </div>
                        <div class="rate-amount">
                            <div class="rate-val" style="color:#8b5cf6;">${{ number_format($rate->mac_rate_per_click, 2) }}</div>
                            <div class="rate-label">per Mac click</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div id="macNoResults" style="display:none;" class="empty-state">
                    <svg width="40" height="40" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3>No Mac countries found</h3>
                    <p>Try a different search term.</p>
                </div>
            @endif
        </div>

        <!-- CTA Banner -->
        <div style="background:linear-gradient(135deg,#0f172a,#064e35);border-radius:20px;padding:40px;text-align:center;margin-top:16px;">
            <div style="font-size:22px;font-weight:800;color:white;margin-bottom:10px;">Ready to Start Earning?</div>
            <p style="font-size:15px;color:rgba(255,255,255,0.7);max-width:480px;margin:0 auto 24px;line-height:1.7;">
                Join our publisher network today. No upfront costs. No hidden fees. Just real earnings paid in crypto.
            </p>
            <a href="{{ route('register') }}" style="display:inline-block;background:var(--primary);color:white;padding:14px 32px;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;transition:all 0.15s;"
               onmouseover="this.style.background='#00a354'" onmouseout="this.style.background='var(--primary)'">
               Create Your Free Account →
            </a>
        </div>
    </div>
</section>

<!-- FOOTER -->
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
            <a href="{{ route('home') }}#how-it-works" class="footer-link">How It Works</a>
            <a href="{{ route('rates') }}" class="footer-link">Click Rates</a>
            <a href="{{ route('install-rates') }}" class="footer-link">Install Rates</a>
        </div>
        <div>
            <div class="footer-heading">Support</div>
            <a href="{{ route('login') }}" class="footer-link">Contact Support</a>
            <a href="{{ route('terms') }}" class="footer-link">Terms of Use</a>
            <a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a>
            <a href="https://www.dropbox.com/scl/fi/r6set9ync4yetcnr69zxl/Installs-Bank.apk?rlkey=krfwfo01ays9elh5xjnnv1xtn&st=hsqgrm9b&dl=1" target="_blank" class="footer-link">📱 Download Android App</a>
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

<script>
function toggleMenu() {
    const menu = document.getElementById('mobileMenu');
    const h1 = document.getElementById('hb1');
    const h2 = document.getElementById('hb2');
    const h3 = document.getElementById('hb3');
    const open = menu.classList.toggle('open');
    h1.style.transform = open ? 'rotate(45deg) translate(5px,5px)' : '';
    h2.style.opacity = open ? '0' : '1';
    h3.style.transform = open ? 'rotate(-45deg) translate(5px,-5px)' : '';
}
function closeMenu() { document.getElementById('mobileMenu').classList.remove('open'); }

function filterRates() {
    const q = document.getElementById('searchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('#ratesGrid .rate-card');
    let visible = 0;
    cards.forEach(card => {
        const match = card.dataset.name.includes(q) || card.dataset.code.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('searchCount').textContent = q
        ? `Showing ${visible} of {{ $rates->count() }} countries`
        : `Showing {{ $rates->count() }} countries`;
    document.getElementById('noResults').style.display = (visible === 0 && q) ? 'block' : 'none';
}

function filterMacRates() {
    const q = document.getElementById('macSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('#macRatesGrid .rate-card');
    let visible = 0;
    cards.forEach(card => {
        const match = card.dataset.macName.includes(q) || card.dataset.macCode.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('macSearchCount').textContent = q
        ? `Showing ${visible} of {{ $macRates->count() }} countries`
        : `Showing {{ $macRates->count() }} countries`;
    const noResults = document.getElementById('macNoResults');
    if (noResults) noResults.style.display = (visible === 0 && q) ? 'block' : 'none';
}
</script>
</body>
</html>
