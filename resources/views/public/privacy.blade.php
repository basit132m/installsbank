<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy — Installs Bank</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <meta name="google-site-verification" content="1s2kQvkZBXZ-CDaAo9Q0LTIH03rruuCToC8IzhTpQrk" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #01BF63; --primary-dark: #00a354; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #111827; background: #f9fafb; }
        nav { position: fixed; top: 0; left: 0; right: 0; background: rgba(255,255,255,0.97); backdrop-filter: blur(10px); border-bottom: 1px solid #f3f4f6; z-index: 1000; padding: 0 5%; display: flex; align-items: center; justify-content: space-between; height: 70px; }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-brand img { height: 34px; }
        .nav-brand-text { font-size: 18px; font-weight: 800; color: #111827; }
        .nav-links { display: flex; gap: 16px; }
        .nav-links a { color: #6b7280; text-decoration: none; font-size: 14px; font-weight: 500; padding: 8px 16px; border-radius: 8px; border: 1.5px solid #e5e7eb; transition: all 0.15s; }
        .nav-links a:hover { background: #f9fafb; color: #111827; }
        .nav-links a.primary { background: var(--primary); color: white; border-color: var(--primary); }
        .nav-links a.primary:hover { background: var(--primary-dark); }

        .page-hero { background: linear-gradient(135deg, #f0fdf7 0%, #ffffff 60%, #e6faf2 100%); padding: 120px 5% 60px; text-align: center; }
        .page-hero h1 { font-size: clamp(28px, 4vw, 44px); font-weight: 900; color: #111827; margin-bottom: 12px; }
        .page-hero p { font-size: 16px; color: #6b7280; }
        .page-hero .updated { display: inline-block; margin-top: 12px; background: #e6faf2; color: #065f46; font-size: 13px; font-weight: 600; padding: 4px 14px; border-radius: 20px; }

        .content-wrap { max-width: 800px; margin: 0 auto; padding: 60px 5%; }
        .section { background: white; border-radius: 16px; padding: 36px; margin-bottom: 24px; border: 1px solid #e5e7eb; }
        .section h2 { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #e6faf2; display: flex; align-items: center; gap: 10px; }
        .section h2 .num { width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; flex-shrink: 0; }
        .section p { font-size: 15px; color: #374151; line-height: 1.8; margin-bottom: 14px; }
        .section p:last-child { margin-bottom: 0; }
        .section ul, .section ol { padding-left: 20px; margin-bottom: 14px; }
        .section ul li, .section ol li { font-size: 15px; color: #374151; line-height: 1.8; margin-bottom: 6px; }
        .highlight { background: #f0fdf7; border-left: 3px solid var(--primary); padding: 14px 18px; border-radius: 0 8px 8px 0; margin-bottom: 14px; font-size: 14px; color: #065f46; }

        footer { background: #111827; color: #9ca3af; padding: 40px 5% 24px; margin-top: 80px; }
        .footer-bottom { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .footer-copy { font-size: 13px; }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a { color: #9ca3af; text-decoration: none; font-size: 13px; transition: color 0.15s; }
        .footer-links a:hover { color: var(--primary); }
    </style>
</head>
<body>

<nav>
    <a href="{{ route('home') }}" class="nav-brand">
        <img src="https://installsbank.com/images/logo.webp" alt="Installs Bank" onerror="this.style.display='none'">
        <span class="nav-brand-text">Installs Bank</span>
    </a>
    <div class="nav-links">
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}" class="primary">Join Network</a>
    </div>
</nav>

<div class="page-hero">
    <h1>Privacy Policy</h1>
    <p>How we collect, use, and protect your information</p>
    <span class="updated">Last updated: April 2026</span>
</div>

<div class="content-wrap">

    <div class="section">
        <h2><span class="num">1</span> Who We Are</h2>
        <p>Installs Bank ("we", "our", "us") is a Pay-Per-Install (PPI) publisher network that connects website publishers with advertising campaigns. Our platform at <strong>installsbank.com</strong> allows approved publishers to embed tracking links on their websites and earn money for valid Windows-device clicks.</p>
        <p>This Privacy Policy explains what information we collect from publishers who register on our platform, how we use that information, and how we protect it.</p>
    </div>

    <div class="section">
        <h2><span class="num">2</span> Information We Collect</h2>
        <p><strong>Account information</strong> — When you register as a publisher, we collect:</p>
        <ul>
            <li>Full name and email address</li>
            <li>Website URL</li>
            <li>Phone number (optional)</li>
            <li>Password (stored in encrypted form — we never store plain-text passwords)</li>
        </ul>
        <p><strong>Click and traffic data</strong> — When visitors click your tracking links, we automatically record:</p>
        <ul>
            <li>IP address and approximate geographic location (country, city)</li>
            <li>Operating system, browser, and device type</li>
            <li>Referrer URL</li>
            <li>Click timestamp</li>
            <li>User-Agent string</li>
        </ul>
        <p><strong>Payment information</strong> — When you request a withdrawal, we collect your cryptocurrency wallet address (USDT or BTC). We do not store any bank card or banking details.</p>
    </div>

    <div class="section">
        <h2><span class="num">3</span> How We Use Your Information</h2>
        <ul>
            <li><strong>Account management</strong> — to create and manage your publisher account, review your application, and communicate with you about approval, contracts, and payments</li>
            <li><strong>Earnings calculation</strong> — to track valid clicks, apply country-based rates, and calculate your earnings accurately</li>
            <li><strong>Fraud detection</strong> — to identify and block fraudulent or bot traffic to protect advertiser budgets and ensure fair earnings for legitimate publishers</li>
            <li><strong>Payments</strong> — to process withdrawal requests to your provided wallet address</li>
            <li><strong>Platform improvement</strong> — to analyse traffic patterns and improve our detection systems</li>
        </ul>
        <div class="highlight">We do not sell, rent, or share your personal information with third parties for marketing purposes.</div>
    </div>

    <div class="section">
        <h2><span class="num">4</span> Click Data and Visitor Privacy</h2>
        <p>When a visitor from your website clicks a tracking link, we record their IP address and device data as described above. This data is used solely for:</p>
        <ul>
            <li>Verifying the click is real (not a bot or duplicate)</li>
            <li>Determining the geographic region for rate calculation (country-based rates)</li>
            <li>Detecting VPN, proxy, or datacenter traffic that violates our terms</li>
        </ul>
        <p>Visitor IP addresses are stored securely and used only for click quality verification. We use the MaxMind GeoLite2 database for geographic lookups.</p>
        <p>Publishers are responsible for informing their website visitors that click tracking may occur, in compliance with applicable data protection laws (such as GDPR).</p>
    </div>

    <div class="section">
        <h2><span class="num">5</span> Data Retention</h2>
        <p>We retain your account data for as long as your publisher account is active. Click data and earnings records are retained for a minimum of 12 months for payment verification and dispute resolution purposes.</p>
        <p>If your account is deleted by an administrator, all associated data — including clicks, earnings, tracking links, withdrawal records, and fraud alerts — is permanently removed from our systems.</p>
    </div>

    <div class="section">
        <h2><span class="num">6</span> Security</h2>
        <p>We take reasonable technical measures to protect your data:</p>
        <ul>
            <li>All passwords are hashed using industry-standard algorithms</li>
            <li>Our platform uses HTTPS (SSL/TLS) for all data transmission</li>
            <li>Click and earnings data is stored in a secured database with restricted access</li>
            <li>Admin and publisher panels require authenticated sessions</li>
        </ul>
        <p>No system is 100% secure. We recommend using a strong, unique password for your account.</p>
    </div>

    <div class="section">
        <h2><span class="num">7</span> Your Rights</h2>
        <p>As a publisher on our platform, you may:</p>
        <ul>
            <li>Request a copy of the personal data we hold about you</li>
            <li>Request correction of inaccurate account information via your dashboard or support</li>
            <li>Request deletion of your account by contacting us through the Support section</li>
        </ul>
    </div>

    <div class="section">
        <h2><span class="num">8</span> Cookies</h2>
        <p>Our platform uses session cookies to keep you logged in to your publisher or admin dashboard. These are strictly necessary cookies and do not track you across other websites.</p>
        <p>We do not use advertising cookies, analytics cookies from third parties, or any tracking cookies on our public homepage.</p>
    </div>

    <div class="section">
        <h2><span class="num">9</span> Contact</h2>
        <p>For any privacy-related questions or data requests, please contact us through the Support section of your publisher dashboard, or email us at <strong>support@installsbank.com</strong>.</p>
    </div>

</div>

<footer>
    <div class="footer-bottom">
        <span class="footer-copy">© {{ date('Y') }} Installs Bank. All rights reserved.</span>
        <div class="footer-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('privacy') }}">Privacy Policy</a>
            <a href="{{ route('terms') }}">Terms of Use</a>
        </div>
    </div>
</footer>

</body>
</html>
