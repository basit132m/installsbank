<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check your email — Installs Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0;padding:0;box-sizing:border-box; }
        body { font-family:'Inter',sans-serif;background:#f4f6f8;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px 16px; }
        .card { background:white;border-radius:20px;padding:48px 40px;max-width:480px;width:100%;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.07);border:1px solid #e5e7eb; }
        .logo { margin-bottom:32px; }
        .logo img { height:36px; }
        .icon-wrap { width:72px;height:72px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px; }
        h1 { font-size:22px;font-weight:800;color:#111827;margin-bottom:12px;line-height:1.3; }
        .subtitle { font-size:15px;color:#6b7280;line-height:1.7;margin-bottom:32px; }
        .email-pill { display:inline-block;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:8px 16px;font-size:14px;font-weight:600;color:#166534;margin-bottom:32px; }
        .steps { background:#f9fafb;border-radius:12px;padding:20px 24px;margin-bottom:28px;text-align:left; }
        .step { display:flex;gap:12px;align-items:flex-start;margin-bottom:14px; }
        .step:last-child { margin-bottom:0; }
        .step-num { width:24px;height:24px;background:#01BF63;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;margin-top:1px; }
        .step-text { font-size:13px;color:#374151;line-height:1.6; }
        .divider { border:none;border-top:1px solid #f3f4f6;margin:28px 0; }
        .resend-form label { display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;text-align:left; }
        .resend-form input { width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;outline:none;transition:border-color .15s; }
        .resend-form input:focus { border-color:#01BF63;box-shadow:0 0 0 3px rgba(1,191,99,0.1); }
        .btn { width:100%;padding:12px;background:#01BF63;color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;margin-top:12px;transition:background .15s; }
        .btn:hover { background:#00a354; }
        .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;font-size:13px;color:#166534;font-weight:600;margin-bottom:20px; }
        .alert-error { background:#fff1f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;font-size:13px;color:#991b1b;font-weight:600;margin-bottom:20px; }
        .spam-tip { font-size:12px;color:#9ca3af;margin-top:16px;line-height:1.6; }
        a.login-link { font-size:13px;color:#6b7280;text-decoration:none;margin-top:20px;display:inline-block; }
        a.login-link:hover { color:#111827; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank"
             onerror="this.style.display='none'">
    </div>

    <div class="icon-wrap">
        <svg width="32" height="32" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>

    <h1>Check your inbox</h1>
    <p class="subtitle">We've sent a verification link to your email address. Click it to verify your account and continue.</p>

    @if(session('email'))
    <div class="email-pill">{{ session('email') }}</div>
    @endif

    @if(session('resent'))
    <div class="alert-success">Verification email resent! Please check your inbox.</div>
    @endif
    @if($errors->any())
    <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="steps">
        <div class="step">
            <div class="step-num">1</div>
            <div class="step-text">Open the email from <strong>Installs Bank</strong> in your inbox.</div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-text">Click the <strong>"Verify Email Address"</strong> button.</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-text">You'll be logged in and your account will be submitted for review.</div>
        </div>
    </div>

    <hr class="divider">

    <p style="font-size:13px;color:#6b7280;margin-bottom:16px;">Didn't receive the email? Enter your address to resend.</p>

    <form class="resend-form" method="POST" action="{{ route('email.resend') }}">
        @csrf
        <label>Email Address</label>
        <input type="email" name="email" value="{{ session('email') ?? old('email') }}" placeholder="you@example.com" required>
        <button type="submit" class="btn">Resend Verification Email</button>
    </form>

    <p class="spam-tip">Not in your inbox? Check your <strong>Spam</strong> or <strong>Junk</strong> folder.</p>

    <a href="{{ route('login') }}" class="login-link">← Back to login</a>
</div>
</body>
</html>
