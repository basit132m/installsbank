<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Advertiser — Installs Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 50%, #f9fafb 100%); min-height: 100vh; display: flex; align-items: flex-start; justify-content: center; padding: 32px 16px; }
        .auth-container { width: 100%; max-width: 600px; }
        .logo-wrap { text-align: center; margin-bottom: 28px; }
        .logo-wrap img { height: 40px; margin-bottom: 8px; }
        .logo-text { font-size: 22px; font-weight: 800; color: #111827; }
        .card { background: white; border-radius: 16px; padding: 36px; box-shadow: 0 4px 24px rgba(0,0,0,0.07); border: 1px solid #e5e7eb; margin-bottom: 16px; }

        /* Info banner */
        .info-banner { background: linear-gradient(135deg, #1e40af, #3b82f6); border-radius: 14px; padding: 24px 28px; margin-bottom: 16px; color: white; }
        .info-banner-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75; margin-bottom: 6px; }
        .info-banner-title { font-size: 20px; font-weight: 800; margin-bottom: 10px; }
        .info-banner-text { font-size: 13px; line-height: 1.7; opacity: 0.9; }
        .info-banner-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .info-pill { background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.25); border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 6px; }

        .card-title { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .card-sub { font-size: 14px; color: #6b7280; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-label .req { color: #ef4444; margin-left: 2px; }
        .form-label .opt { color: #9ca3af; font-weight: 400; font-size: 11px; margin-left: 4px; }
        .form-control { width: 100%; padding: 11px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: all 0.15s; color: #111827; background: white; }
        .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .form-hint { font-size: 12px; color: #6b7280; margin-top: 5px; display: flex; align-items: flex-start; gap: 5px; line-height: 1.5; }
        .form-hint svg { flex-shrink: 0; margin-top: 1px; }
        .form-hint.required-hint { color: #3b82f6; font-weight: 500; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media(max-width: 520px) { .grid-2 { grid-template-columns: 1fr; } }

        .section-divider { border-top: 1px solid #f3f4f6; margin: 24px 0; padding-top: 24px; }
        .section-title { font-size: 14px; font-weight: 700; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .section-title-icon { width: 28px; height: 28px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

        .contact-notice { background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 14px 16px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px; }
        .contact-notice p { font-size: 13px; color: #1e40af; line-height: 1.6; }
        .contact-notice strong { font-weight: 700; }

        .btn-submit { width: 100%; padding: 14px; background: #3b82f6; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-submit:hover { background: #2563eb; }
        .form-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; }
        .form-footer a { color: #3b82f6; font-weight: 600; text-decoration: none; }
        .form-footer a:hover { color: #2563eb; text-decoration: underline; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
        .alert-danger ul { padding-left: 18px; margin-top: 4px; }

        .switch-role { background: #f9fafb; border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
        .switch-role-text { font-size: 13px; color: #6b7280; }
        .switch-role-text strong { color: #374151; }
        .switch-role-link { display: inline-flex; align-items: center; gap: 6px; color: #01BF63; font-size: 13px; font-weight: 700; text-decoration: none; white-space: nowrap; }
        .switch-role-link:hover { color: #00a354; }

        .back-link { text-align: center; margin-top: 12px; }
        .back-link a { color: #6b7280; font-size: 13px; text-decoration: none; }
        .back-link a:hover { color: #374151; }
    </style>
</head>
<body>
<div class="auth-container">

    <div class="logo-wrap">
        <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" onerror="this.style.display='none'">
        <div class="logo-text">Installs Bank</div>
    </div>

    <!-- Info Banner -->
    <div class="info-banner">
        <div class="info-banner-label">Advertiser Account</div>
        <div class="info-banner-title">Join as Advertiser</div>
        <div class="info-banner-text">
            Drive targeted installs for your app with our premium publisher network. We offer per-click pricing by country or flat rate for mixed traffic.
        </div>
        <div class="info-banner-pills">
            <div class="info-pill">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Per-click country targeting
            </div>
            <div class="info-pill">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Flat rate mixed traffic
            </div>
            <div class="info-pill">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Premium publisher network
            </div>
            <div class="info-pill">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Real-time campaign reporting
            </div>
        </div>
    </div>

    <!-- Switch to Publisher -->
    <div class="switch-role">
        <div class="switch-role-text"><strong>Want to earn money instead?</strong> Join as a publisher and monetize your website traffic.</div>
        <a href="{{ route('register') }}" class="switch-role-link">
            Register as Publisher
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    @if($errors->any())
    <div class="alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="card">
        <div class="card-title">Create Your Advertiser Account</div>
        <div class="card-sub" style="margin-bottom:28px;">Fill in your details below to get started</div>

        <form method="POST" action="{{ route('advertiser.register') }}">
            @csrf

            <!-- Basic Info -->
            <div class="section-title">
                <div class="section-title-icon">
                    <svg width="14" height="14" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                Account Details
            </div>

            <div class="form-group">
                <label class="form-label">Full Name <span class="req">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="John Doe" required autocomplete="name">
            </div>

            <div class="form-group">
                <label class="form-label">Email Address <span class="req">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="you@company.com" required autocomplete="email">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Company Name <span class="opt">(optional)</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="Acme Inc.">
                </div>
                <div class="form-group">
                    <label class="form-label">Website <span class="opt">(optional)</span></label>
                    <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://yourapp.com">
                </div>
            </div>

            <!-- Contact -->
            <div class="section-divider">
                <div class="section-title">
                    <div class="section-title-icon">
                        <svg width="14" height="14" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    Contact &amp; Communication
                </div>

                <div class="contact-notice">
                    <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p><strong>Telegram and WhatsApp are required</strong> for account management. Our team will use these channels to communicate campaign setup, billing, and important updates.</p>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Telegram Username <span class="req">*</span></label>
                        <input type="text" name="telegram" class="form-control" value="{{ old('telegram') }}" placeholder="@username" required>
                        <div class="form-hint required-hint">
                            <svg width="12" height="12" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Required for account management
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp Number <span class="req">*</span></label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}" placeholder="+1 234 567 8900" required>
                        <div class="form-hint required-hint">
                            <svg width="12" height="12" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Required for account management
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number <span class="opt">(optional)</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 234 567 8900" autocomplete="tel">
                </div>
            </div>

            <!-- Password -->
            <div class="section-divider">
                <div class="section-title">
                    <div class="section-title-icon">
                        <svg width="14" height="14" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    Password
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Password <span class="req">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required minlength="8" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password <span class="req">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required autocomplete="new-password">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Create Advertiser Account
            </button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in here</a>
        </div>
    </div>

    <div class="back-link">
        <a href="{{ route('home') }}">← Back to Home</a>
    </div>

</div>
</body>
</html>
