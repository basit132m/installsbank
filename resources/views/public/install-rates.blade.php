<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Rates — Installs Bank</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <meta name="description" content="View Installs Bank's current pay-per-install rates by country. See exactly how much you earn per verified install from each country.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #01BF63; --primary-dark: #00a354; --accent: #8b5cf6; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #111827; background: #fff; }

        nav {
            position: fixed; top: 0; left: 0; right: 0;
            background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);
            border-bottom: 1px solid #f3f4f6; z-index: 1000;
            padding: 0 5%; display: flex; align-items: center;
            justify-content: space-between; height: 70px;
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-brand img { height: 34px; }
        .nav-links { display: flex; align-items: center; gap: 28px; }
        .nav-links a { color: #6b7280; text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.15s; }
        .nav-links a:hover { color: #111827; }
        .nav-links a.active { font-weight: 700; color: var(--accent); }
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

        .hero {
            padding: 120px 5% 80px;
            background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 40%, #ede9fe 100%);
            text-align: center; position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(139,92,246,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .section-tag { display: inline-block; background: #ede9fe; color: #6d28d9; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 16px; }
        .hero h1 { font-size: clamp(28px, 5vw, 52px); font-weight: 900; line-height: 1.1; color: #111827; margin-bottom: 20px; }
        .hero h1 span { color: var(--accent); }
        .hero p { font-size: 17px; color: #6b7280; max-width: 580px; margin: 0 auto 36px; line-height: 1.7; }
        .hero-stats { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; margin-top: 12px; }
        .hero-stat { text-align: center; }
        .hero-stat-val { font-size: 28px; font-weight: 900; color: #111827; }
        .hero-stat-val span { color: var(--accent); }
        .hero-stat-label { font-size: 12px; color: #9ca3af; font-weight: 500; margin-top: 4px; }

        .search-wrap { max-width: 520px; margin: 0 auto 48px; position: relative; }
        .search-input { width: 100%; padding: 14px 20px 14px 48px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; font-family: inherit; outline: none; transition: all 0.15s; color: #111827; background: white; box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
        .search-input:focus { border-color: var(--accent); box-shadow: 0 0 0 4px rgba(139,92,246,0.08); }
        .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
        .search-count { text-align: center; font-size: 13px; color: #9ca3af; margin-top: -36px; margin-bottom: 36px; }

        .content { padding: 0 5% 80px; }
        .content-inner { max-width: 900px; margin: 0 auto; }

        .rates-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; margin-bottom: 48px; }
        .rate-card {
            background: white; border: 1.5px solid #f3f4f6; border-radius: 14px;
            padding: 18px 20px; display: flex; align-items: center; gap: 14px;
            transition: all 0.2s; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .rate-card:hover { border-color: #c4b5fd; box-shadow: 0 4px 16px rgba(139,92,246,0.12); transform: translateY(-2px); }
        .rate-card.top-rate { border-color: #c4b5fd; background: #faf5ff; }
        .flag-img { width: 32px; height: 24px; border-radius: 4px; object-fit: cover; box-shadow: 0 1px 4px rgba(0,0,0,0.1); flex-shrink: 0; }
        .flag-fallback { width: 32px; height: 24px; border-radius: 4px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .country-info { flex: 1; min-width: 0; }
        .country-name { font-size: 14px; font-weight: 700; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .country-code { font-size: 11px; color: #9ca3af; font-weight: 600; margin-top: 1px; }
        .rate-amount { text-align: right; flex-shrink: 0; }
        .rate-val { font-size: 18px; font-weight: 800; color: var(--accent); }
        .rate-label { font-size: 10px; color: #9ca3af; font-weight: 500; margin-top: 1px; white-space: nowrap; }
        .top-badge { display: inline-block; background: #ede9fe; color: #5b21b6; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; margin-top: 3px; }

        .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .empty-state svg { margin: 0 auto 16px; display: block; }
        .empty-state h3 { font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 8px; }

        .info-banner { background: linear-gradient(135deg, #f5f3ff, #ede9fe); border: 1px solid #c4b5fd; border-radius: 14px; padding: 24px 28px; margin-bottom: 36px; display: flex; align-items: flex-start; gap: 16px; }
        .info-icon { width: 42px; height: 42px; background: var(--accent); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

        /* How installs work */
        .how-box { background: #faf5ff; border: 1.5px solid #e9d5ff; border-radius: 16px; padding: 28px 32px; margin-bottom: 36px; }
        .how-box h3 { font-size: 17px; font-weight: 800; color: #5b21b6; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .how-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; }
        .how-step { text-align: center; padding: 16px; background: white; border-radius: 12px; border: 1px solid #e9d5ff; }
        .how-step-num { width: 36px; height: 36px; background: var(--accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; margin: 0 auto 10px; }
        .how-step-title { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 4px; }
        .how-step-text { font-size: 12px; color: #6b7280; line-height: 1.5; }

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

        @media(max-width: 768px) {
            .nav-links, .nav-cta { display: none; }
            .hamburger { display: flex; }
            .hero { padding: 100px 5% 60px; }
            .rates-grid { grid-template-columns: 1fr 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
            .how-steps { grid-template-columns: 1fr 1fr; }
        }
        @media(max-width: 480px) {
            .rates-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .info-banner { flex-direction: column; }
            .how-steps { grid-template-columns: 1fr; }
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
        <a href="{{ route('home') }}#contracts">Contracts</a>
        <a href="{{ route('rates') }}">Click Rates</a>
        <a href="{{ route('install-rates') }}" class="active">Install Rates</a>
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
    <span class="section-tag">📲 Install Rates</span>
    <h1>Earn More Per <span>Install</span>.<br>Country-Based Payouts.</h1>
    <p>Publishers on our Installs-Based contract earn every time a verified install is recorded. Rates vary by country — top markets pay significantly more.</p>
    <div class="hero-stats">
        <div class="hero-stat">
            <div class="hero-stat-val"><span>${{ $topRate > 0 ? number_format($topRate, 2) : '—' }}</span></div>
            <div class="hero-stat-label">Top Rate Per Install</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-val"><span>{{ $rates->count() }}</span></div>
            <div class="hero-stat-label">Countries Live</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-val">Win<span>dows</span></div>
            <div class="hero-stat-label">Traffic Counted</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-val">USDT/<span>BTC</span></div>
            <div class="hero-stat-label">Payout Methods</div>
        </div>
    </div>
</section>

<!-- CONTENT -->
<section class="content">
    <div class="content-inner">

        <!-- How Installs Work -->
        <div class="how-box">
            <h3>
                <svg width="18" height="18" fill="none" stroke="#7c3aed" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                How Installs Are Counted
            </h3>
            <div class="how-steps">
                <div class="how-step">
                    <div class="how-step-num">1</div>
                    <div class="how-step-title">Visitor Clicks</div>
                    <div class="how-step-text">A Windows visitor from your site clicks your tracking link and lands on the destination page.</div>
                </div>
                <div class="how-step">
                    <div class="how-step-num">2</div>
                    <div class="how-step-title">Clicks Accumulate</div>
                    <div class="how-step-text">Unique clicks per country accumulate. Once they reach the daily threshold, an install is recorded.</div>
                </div>
                <div class="how-step">
                    <div class="how-step-num">3</div>
                    <div class="how-step-title">Install Recorded</div>
                    <div class="how-step-text">The install is credited to your account at the rate for that visitor's country.</div>
                </div>
                <div class="how-step">
                    <div class="how-step-num">4</div>
                    <div class="how-step-title">Earnings Added</div>
                    <div class="how-step-text">Your balance is updated in real time. Withdraw any time you reach the minimum threshold.</div>
                </div>
            </div>
        </div>

        <!-- Info Banner -->
        <div class="info-banner">
            <div class="info-icon">
                <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#5b21b6;margin-bottom:4px;">Installs-Based Contract</div>
                <p style="font-size:14px;color:#374151;line-height:1.7;margin:0;">
                    These rates apply only to publishers on the <strong>Installs-Based</strong> contract. The number of clicks needed per install varies by day to ensure traffic quality.
                    Rates shown are per single verified install from each country and are updated live by our team.
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
                <h3>Install Rates Coming Soon</h3>
                <p>Our team is configuring install rates. Please check back soon.</p>
            </div>
        @else
            <div class="rates-grid" id="ratesGrid">
                @foreach($rates as $rate)
                <div class="rate-card {{ $rate->rate_usd == $topRate ? 'top-rate' : '' }}"
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
                        @if($rate->rate_usd == $topRate)
                            <span class="top-badge">TOP RATE</span>
                        @endif
                    </div>
                    <div class="rate-amount">
                        <div class="rate-val">${{ number_format($rate->rate_usd, 2) }}</div>
                        <div class="rate-label">per install</div>
                    </div>
                </div>
                @endforeach
            </div>

            <div id="noResults" style="display:none;" class="empty-state">
                <svg width="40" height="40" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3>No countries found</h3>
                <p>Try a different search term.</p>
            </div>
        @endif

        <!-- CTA -->
        <div style="background:linear-gradient(135deg,#1e1b4b,#4c1d95);border-radius:20px;padding:40px;text-align:center;margin-top:16px;">
            <div style="font-size:22px;font-weight:800;color:white;margin-bottom:10px;">Ready to Earn Per Install?</div>
            <p style="font-size:15px;color:rgba(255,255,255,0.7);max-width:480px;margin:0 auto 24px;line-height:1.7;">
                Apply for the Installs-Based contract and start earning every time a verified install is counted from your traffic.
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
            <p class="footer-desc">A premium PPI network connecting publishers with high-paying click and install opportunities. Trusted, transparent, and built for growth.</p>
        </div>
        <div>
            <div class="footer-heading">Publishers</div>
            <a href="{{ route('register') }}" class="footer-link">Register</a>
            <a href="{{ route('login') }}" class="footer-link">Login</a>
            <a href="{{ route('home') }}#how-it-works" class="footer-link">How It Works</a>
            <a href="{{ route('home') }}#contracts" class="footer-link">Contracts</a>
        </div>
        <div>
            <div class="footer-heading">Rates</div>
            <a href="{{ route('rates') }}" class="footer-link">Click Rates</a>
            <a href="{{ route('install-rates') }}" class="footer-link">Install Rates</a>
        </div>
        <div>
            <div class="footer-heading">Contact Us</div>
            <a href="https://wa.me/19707426488" target="_blank" class="footer-link">💬 WhatsApp</a>
            <a href="https://t.me/installsbank" target="_blank" class="footer-link">✈️ Telegram @installsbank</a>
            <a href="{{ route('terms') }}" class="footer-link">Terms of Use</a>
            <a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a>
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
    const h1 = document.getElementById('hb1'), h2 = document.getElementById('hb2'), h3 = document.getElementById('hb3');
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
</script>
</body>
</html>
