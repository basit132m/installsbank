<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Use — Installs Bank</title>
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
        .warning { background: #fff7ed; border-left: 3px solid #f59e0b; padding: 14px 18px; border-radius: 0 8px 8px 0; margin-bottom: 14px; font-size: 14px; color: #92400e; }

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
    <h1>Terms of Use</h1>
    <p>Please read these terms carefully before using our platform</p>
    <span class="updated">Last updated: April 2026</span>
</div>

<div class="content-wrap">

    <div class="section">
        <h2><span class="num">1</span> Acceptance of Terms</h2>
        <p>By registering an account or using the Installs Bank platform ("Platform"), you agree to be bound by these Terms of Use. If you do not agree, do not register or use the Platform.</p>
        <p>Installs Bank reserves the right to update these terms at any time. Continued use of the Platform after changes are posted constitutes acceptance of the updated terms.</p>
    </div>

    <div class="section">
        <h2><span class="num">2</span> How the Platform Works</h2>
        <p>Installs Bank is a <strong>Pay-Per-Install (PPI) publisher network</strong>. Here is how the system operates:</p>
        <ol>
            <li><strong>Registration &amp; Review</strong> — Publishers apply by registering an account. All accounts are reviewed manually by our team before activation. Your website must have at least <strong>500 unique visitors per day</strong> to qualify.</li>
            <li><strong>48-Hour Test Period</strong> — Once approved, your account enters a test period where we evaluate your traffic quality, volume, and geographic distribution.</li>
            <li><strong>Contract Offer</strong> — Based on your test results, we offer you a rate contract. You may accept or decline the contract from your dashboard.</li>
            <li><strong>Earning Clicks</strong> — After accepting your contract, you embed our tracking link on your website. Valid clicks (Windows device, non-fraudulent, within your geo and rate settings) are credited to your account.</li>
            <li><strong>Payments</strong> — Earnings accumulate in your balance. You may request a withdrawal once your balance reaches the minimum threshold. Payments are made in USDT (TRC20 / ERC20) or Bitcoin (BTC).</li>
        </ol>
        <div class="highlight">Only clicks from <strong>Windows desktop devices</strong> are counted for earnings. Clicks from Android, iOS, Mac, and other devices are tracked but do not generate earnings.</div>
    </div>

    <div class="section">
        <h2><span class="num">3</span> Publisher Contracts</h2>
        <p>Publisher contracts define your earning rate and conditions. There are two contract types:</p>
        <ul>
            <li><strong>Per-Click Contract</strong> — You earn a fixed rate per 1,000 valid unique Windows clicks. Rates vary by country and are set by our team based on advertiser demand.</li>
            <li><strong>Fixed Rate Contract</strong> — You earn a fixed daily or agreed amount regardless of click volume. This contract type is offered to select high-volume publishers. Under a fixed rate contract, per-click earnings are not tracked in your dashboard — your payment is handled directly.</li>
        </ul>
        <p>Contract offers are sent to you via your dashboard. You have the right to accept or decline any offer. Contracts are binding once accepted. If you decline, our team may issue a revised offer or close your account.</p>
        <p>Installs Bank reserves the right to modify, suspend, or terminate a contract if fraudulent traffic is detected or if you violate these terms.</p>
        <div class="warning"><strong>Important:</strong> Attempting to negotiate rates, generate fake clicks, or manipulate traffic to increase earnings is grounds for immediate account termination and forfeiture of any unpaid balance.</div>
    </div>

    <div class="section">
        <h2><span class="num">4</span> Earnings, Rates, and Country Rates</h2>
        <p>Earning rates are set per country by Installs Bank and are subject to change. The following conditions apply:</p>
        <ul>
            <li>Only clicks from countries with an active rate are credited with earnings. Clicks from countries with no rate set earn <strong>$0.00</strong> until a rate is assigned.</li>
            <li>If a rate is set for a country after some unrated clicks have already occurred, those past clicks <strong>are retroactively credited</strong> at the new rate.</li>
            <li>Installs Bank reserves the right to change rates at any time. Rate changes do not affect clicks already credited.</li>
            <li>Each unique click is counted once per 24-hour window. Subsequent clicks from the same browser session (same fingerprint) within 24 hours are not counted.</li>
        </ul>
    </div>

    <div class="section">
        <h2><span class="num">5</span> Fraud Policy and Traffic Quality</h2>
        <p>Installs Bank operates an automated fraud detection system. The following are considered fraudulent and will not be credited:</p>
        <ul>
            <li>Clicks from VPNs, datacenter IP ranges, or known proxy services</li>
            <li>Duplicate clicks from the same browser session within 24 hours</li>
            <li>Clicks from bots, crawlers, scripts, or headless browsers (e.g. Selenium, Puppeteer)</li>
            <li>Clicks generated by traffic exchanges, paid-to-click sites, or any automated traffic source</li>
            <li>Clicks where the IP geolocation does not match the browser language (country mismatch — when enabled per-publisher)</li>
            <li>Self-clicks — publishers clicking their own tracking links</li>
        </ul>
        <p>Publishers sending consistently low-quality or fraudulent traffic may have their account suspended, their balance forfeited, or their contract terminated without notice.</p>
        <div class="highlight">Our fraud system is designed to protect both advertiser budgets and legitimate publishers. Fraud detection settings can be adjusted per-publisher by our team based on your traffic profile.</div>
    </div>

    <div class="section">
        <h2><span class="num">6</span> Tracking Links and Ad Code</h2>
        <p>Upon approval and contract acceptance, you are assigned unique tracking links. These links must be used according to the following rules:</p>
        <ul>
            <li>Tracking links must only be placed on the website you registered with</li>
            <li>Links must be placed as <strong>user-clickable links</strong> only — not as automatic impression pixels, iframes, or background requests</li>
            <li>Do not place links in email campaigns, pop-ups, or any deceptive format</li>
            <li>Do not disguise or obscure the tracking link to mislead users</li>
            <li>Tracking links may support device-specific destination URLs (Windows, Android, Mac, iOS)</li>
        </ul>
        <p>Publishers may use custom tracking domains to route traffic through their own domain. This is supported via our Tracking Domains panel. All traffic ultimately flows through and is recorded by Installs Bank systems.</p>
    </div>

    <div class="section">
        <h2><span class="num">7</span> Payments and Withdrawals</h2>
        <ul>
            <li>Minimum withdrawal amount is <strong>$10.00</strong></li>
            <li>Withdrawal requests are reviewed and approved manually by our team</li>
            <li>Approved payments are sent in <strong>USDT (TRC20 or ERC20)</strong> or <strong>Bitcoin (BTC)</strong> to the wallet address you provide</li>
            <li>Once a withdrawal is marked as paid, it is final. Installs Bank is not responsible for errors in wallet addresses provided by the publisher</li>
            <li>Installs Bank reserves the right to withhold payment if fraud is detected or if the account is under investigation</li>
            <li>Balances from terminated accounts due to fraud violations are forfeited</li>
        </ul>
    </div>

    <div class="section">
        <h2><span class="num">8</span> Prohibited Content</h2>
        <p>Publishers may not place Installs Bank tracking links on websites containing:</p>
        <ul>
            <li>Adult or pornographic content</li>
            <li>Pirated software, movies, or copyrighted material</li>
            <li>Illegal content of any kind</li>
            <li>Malware, phishing, or deceptive content</li>
            <li>Content targeting minors</li>
        </ul>
        <p>Violation of this policy will result in immediate account termination without payment of any pending balance.</p>
    </div>

    <div class="section">
        <h2><span class="num">9</span> Account Suspension and Termination</h2>
        <p>Installs Bank may suspend or terminate a publisher account at any time for:</p>
        <ul>
            <li>Violation of any section of these Terms</li>
            <li>Fraudulent or low-quality traffic</li>
            <li>Inactivity (no clicks for 60+ days after approval)</li>
            <li>At our discretion, with or without prior notice</li>
        </ul>
        <p>Publishers may request account deletion via the Support section. Upon deletion, all account data — including clicks, earnings, and withdrawal history — is permanently removed.</p>
    </div>

    <div class="section">
        <h2><span class="num">10</span> Limitation of Liability</h2>
        <p>Installs Bank is provided "as is". We make no guarantees regarding earnings, uptime, or advertiser availability. Click rates and country availability may change at any time based on advertiser demand.</p>
        <p>We are not liable for losses arising from rate changes, account suspension, fraud flags, or technical issues beyond our control.</p>
    </div>

    <div class="section">
        <h2><span class="num">11</span> Contact</h2>
        <p>For questions about these terms, please contact us through the Support section of your publisher dashboard. You can also email us at <strong>support@installsbank.com</strong>.</p>
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
