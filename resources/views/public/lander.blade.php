<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->page_title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, {{ $scheme['bg_from'] }} 0%, {{ $scheme['bg_to'] }} 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.12;
            pointer-events: none;
            animation: drift 18s ease-in-out infinite;
        }
        .orb-1 {
            width: 600px; height: 600px;
            background: {{ $scheme['accent'] }};
            top: -200px; left: -200px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 500px; height: 500px;
            background: {{ $scheme['accent2'] }};
            bottom: -150px; right: -150px;
            animation-delay: -9s;
        }
        .orb-3 {
            width: 300px; height: 300px;
            background: {{ $scheme['accent'] }};
            top: 40%; left: 60%;
            animation-delay: -4s;
        }
        @keyframes drift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(40px, -30px) scale(1.05); }
            66%       { transform: translate(-25px, 20px) scale(0.95); }
        }

        /* Grid lines overlay */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient({{ $scheme['accent'] }}08 1px, transparent 1px),
                linear-gradient(90deg, {{ $scheme['accent'] }}08 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        /* Scan line effect */
        .scan-line {
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 3px,
                rgba(0,0,0,0.03) 3px,
                rgba(0,0,0,0.03) 4px
            );
            pointer-events: none;
        }

        .wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 560px;
            padding: 20px;
        }

        /* Top badge */
        .top-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
        }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: {{ $scheme['card'] }};
            border: 1px solid {{ $scheme['accent'] }}40;
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            color: {{ $scheme['text_accent'] }};
            letter-spacing: 0.06em;
            text-transform: uppercase;
            backdrop-filter: blur(8px);
        }
        .badge-dot {
            width: 7px; height: 7px;
            background: {{ $scheme['accent'] }};
            border-radius: 50%;
            box-shadow: 0 0 8px {{ $scheme['accent'] }};
            animation: blink 1.8s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.3;} }

        /* Main card */
        .card {
            background: {{ $scheme['card'] }};
            border: 1px solid {{ $scheme['accent'] }}30;
            border-radius: 24px;
            padding: 40px;
            backdrop-filter: blur(20px);
            box-shadow:
                0 0 60px {{ $scheme['glow'] }},
                0 32px 64px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.06);
            position: relative;
            overflow: hidden;
        }

        /* Accent line at top of card */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, {{ $scheme['accent'] }}, transparent);
        }

        /* Icon */
        .icon-wrap {
            width: 72px; height: 72px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, {{ $scheme['accent'] }}30, {{ $scheme['accent2'] }}20);
            border: 1px solid {{ $scheme['accent'] }}40;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 30px {{ $scheme['glow'] }};
        }
        .icon-wrap svg {
            width: 36px; height: 36px;
            color: {{ $scheme['accent'] }};
        }

        h1 {
            text-align: center;
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .subtitle {
            text-align: center;
            font-size: 14px;
            color: rgba(255,255,255,0.45);
            margin-bottom: 32px;
            font-weight: 400;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, {{ $scheme['accent'] }}30, transparent);
            margin: 28px 0;
        }

        /* Download count */
        .download-count {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
        }
        .count-label {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .count-value {
            font-size: 22px;
            font-weight: 800;
            color: {{ $scheme['text_accent'] }};
            font-variant-numeric: tabular-nums;
        }
        .count-icon {
            width: 20px; height: 20px;
            color: {{ $scheme['accent'] }};
        }

        /* Download button */
        .btn-download {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 16px 28px;
            background: linear-gradient(135deg, {{ $scheme['accent'] }}, {{ $scheme['accent2'] }});
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            letter-spacing: -0.01em;
            transition: all 0.25s ease;
            box-shadow:
                0 0 30px {{ $scheme['glow'] }},
                0 8px 24px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }
        .btn-download::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow:
                0 0 50px {{ $scheme['glow'] }},
                0 16px 40px rgba(0,0,0,0.4);
        }
        .btn-download:active { transform: translateY(0); }
        .btn-download svg { width: 20px; height: 20px; flex-shrink: 0; }

        /* Copy row */
        @if($settings->mega_url)
        .copy-row {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(0,0,0,0.3);
            border: 1px solid {{ $scheme['accent'] }}20;
            border-radius: 10px;
            padding: 12px 14px;
            margin-top: 14px;
        }
        .copy-url {
            flex: 1;
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-family: monospace;
        }
        .btn-copy {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            background: {{ $scheme['accent'] }}20;
            border: 1px solid {{ $scheme['accent'] }}40;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: {{ $scheme['text_accent'] }};
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.15s;
        }
        .btn-copy:hover { background: {{ $scheme['accent'] }}35; }
        .btn-copy svg { width: 12px; height: 12px; }
        @endif

        /* Password box */
        @if($settings->show_password && $settings->archive_password)
        .password-box {
            background: rgba(0,0,0,0.25);
            border: 1px solid {{ $scheme['accent'] }}25;
            border-radius: 12px;
            padding: 16px 18px;
            margin-top: 20px;
        }
        .pw-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.35);
            margin-bottom: 8px;
        }
        .pw-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .pw-value {
            flex: 1;
            font-family: 'Courier New', monospace;
            font-size: 15px;
            font-weight: 700;
            color: {{ $scheme['text_accent'] }};
            letter-spacing: 0.08em;
        }
        .btn-copy-pw {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            background: {{ $scheme['accent'] }}20;
            border: 1px solid {{ $scheme['accent'] }}40;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: {{ $scheme['text_accent'] }};
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.15s;
        }
        .btn-copy-pw:hover { background: {{ $scheme['accent'] }}35; }
        .btn-copy-pw svg { width: 12px; height: 12px; }
        @endif

        /* System checks */
        @if($settings->show_checks)
        .checks {
            margin-top: 24px;
        }
        .checks-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.3);
            margin-bottom: 12px;
        }
        .check-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
        }
        .check-item + .check-item {
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .check-icon {
            width: 20px; height: 20px;
            background: {{ $scheme['accent'] }}20;
            border: 1px solid {{ $scheme['accent'] }}40;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .check-icon svg { width: 11px; height: 11px; color: {{ $scheme['accent'] }}; }
        .check-text {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            font-weight: 500;
        }
        .check-status {
            margin-left: auto;
            font-size: 11px;
            font-weight: 600;
            color: {{ $scheme['accent'] }};
        }
        @endif

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: rgba(255,255,255,0.2);
        }

        @media (max-width: 600px) {
            .card { padding: 28px 22px; }
            h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="grid-overlay"></div>
    <div class="scan-line"></div>

    <div class="wrapper">
        <div class="top-badge">
            <span class="badge-pill">
                <span class="badge-dot"></span>
                Secure Download
            </span>
        </div>

        <div class="card">
            <div class="icon-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
            </div>

            <h1>{{ $settings->page_title }}</h1>
            <p class="subtitle">Click the button below to start your secure download</p>

            <div class="download-count">
                <svg class="count-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="count-value">{{ number_format($settings->download_count) }}</span>
                <span class="count-label">downloads</span>
            </div>

            @if($settings->mega_url)
                <a href="{{ $settings->mega_url }}" class="btn-download" target="_blank" rel="noopener">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Download File
                </a>

                <div class="copy-row">
                    <span class="copy-url" id="megaUrl">{{ $settings->mega_url }}</span>
                    <button class="btn-copy" onclick="copyUrl()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span id="copyLabel">Copy Link</span>
                    </button>
                </div>
            @else
                <div style="text-align:center;padding:20px;background:rgba(0,0,0,0.2);border-radius:12px;border:1px dashed rgba(255,255,255,0.1);">
                    <p style="color:rgba(255,255,255,0.3);font-size:14px;">Download link coming soon</p>
                </div>
            @endif

            @if($settings->show_password && $settings->archive_password)
            <div class="password-box">
                <div class="pw-label">Archive Password</div>
                <div class="pw-row">
                    <span class="pw-value" id="archivePass">{{ $settings->archive_password }}</span>
                    <button class="btn-copy-pw" onclick="copyPassword()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span id="pwLabel">Copy</span>
                    </button>
                </div>
            </div>
            @endif

            @if($settings->show_checks)
            <div class="divider"></div>
            <div class="checks">
                <div class="checks-title">System Checks</div>
                <div class="check-item">
                    <div class="check-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="check-text">Virus & Malware Scan</span>
                    <span class="check-status">Passed</span>
                </div>
                <div class="check-item">
                    <div class="check-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="check-text">File Integrity Verified</span>
                    <span class="check-status">Passed</span>
                </div>
                <div class="check-item">
                    <div class="check-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="check-text">Secure Connection</span>
                    <span class="check-status">Active</span>
                </div>
                <div class="check-item">
                    <div class="check-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="check-text">No Adware Detected</span>
                    <span class="check-status">Clean</span>
                </div>
            </div>
            @endif
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} &mdash; Secure File Distribution
        </div>
    </div>

    <script>
        function copyUrl() {
            const url = document.getElementById('megaUrl').textContent.trim();
            navigator.clipboard.writeText(url).then(() => {
                const label = document.getElementById('copyLabel');
                label.textContent = 'Copied!';
                setTimeout(() => label.textContent = 'Copy Link', 2000);
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = url;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                const label = document.getElementById('copyLabel');
                label.textContent = 'Copied!';
                setTimeout(() => label.textContent = 'Copy Link', 2000);
            });
        }

        function copyPassword() {
            const pw = document.getElementById('archivePass').textContent.trim();
            navigator.clipboard.writeText(pw).then(() => {
                const label = document.getElementById('pwLabel');
                label.textContent = 'Copied!';
                setTimeout(() => label.textContent = 'Copy', 2000);
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = pw;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                const label = document.getElementById('pwLabel');
                label.textContent = 'Copied!';
                setTimeout(() => label.textContent = 'Copy', 2000);
            });
        }
    </script>
</body>
</html>
