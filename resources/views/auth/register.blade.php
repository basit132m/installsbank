<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Installs Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body { font-family:'Inter',sans-serif; min-height:100vh; display:flex; }

        /* ── LEFT PANEL (sticky) ──────────────────────── */
        .left-panel {
            width:42%;
            height:100vh;
            position:sticky;top:0;
            background:#060d18;
            overflow:hidden;
            display:flex;flex-direction:column;justify-content:center;
            padding:56px 60px;
            flex-shrink:0;
        }

        .blob { position:absolute;border-radius:50%;filter:blur(90px);animation:blobFloat 10s ease-in-out infinite; }
        .blob-1 { width:420px;height:420px;background:#01BF63;opacity:.11;top:-100px;left:-100px;animation-delay:0s; }
        .blob-2 { width:280px;height:280px;background:#00e676;opacity:.08;bottom:-60px;right:-40px;animation-delay:-4s; }
        .blob-3 { width:200px;height:200px;background:#01BF63;opacity:.07;top:50%;left:50%;animation-delay:-7s; }

        @keyframes blobFloat {
            0%,100% { transform:translate(0,0) scale(1); }
            33%      { transform:translate(20px,-16px) scale(1.06); }
            66%      { transform:translate(-10px,14px) scale(0.94); }
        }

        .grid-overlay {
            position:absolute;inset:0;
            background-image:radial-gradient(rgba(255,255,255,.055) 1px, transparent 1px);
            background-size:28px 28px;
        }

        .left-content { position:relative;z-index:1; }

        .brand { display:flex;align-items:center;gap:12px;margin-bottom:52px; }
        .brand img { height:36px;object-fit:contain; }
        .brand-name { font-size:19px;font-weight:800;color:#fff;letter-spacing:-.4px; }

        .hero-title { font-size:36px;font-weight:900;color:#fff;line-height:1.14;letter-spacing:-1px;margin-bottom:14px; }
        .hero-title span { color:#01BF63; }

        .hero-sub { font-size:14px;color:rgba(255,255,255,.48);line-height:1.65;margin-bottom:44px;max-width:320px; }

        .features { display:flex;flex-direction:column;gap:20px; }
        .feature { display:flex;align-items:flex-start;gap:13px; }
        .feature-icon {
            width:36px;height:36px;border-radius:9px;flex-shrink:0;
            background:rgba(1,191,99,.12);border:1px solid rgba(1,191,99,.22);
            display:flex;align-items:center;justify-content:center;
        }
        .feature-title { font-size:13px;font-weight:700;color:#fff;margin-bottom:2px; }
        .feature-sub   { font-size:12px;color:rgba(255,255,255,.38);line-height:1.5; }

        .stats-bar {
            display:flex;gap:36px;margin-top:48px;padding-top:36px;
            border-top:1px solid rgba(255,255,255,.08);
        }
        .stat-value { font-size:24px;font-weight:900;color:#01BF63;letter-spacing:-.5px; }
        .stat-label { font-size:11px;color:rgba(255,255,255,.33);font-weight:500;margin-top:3px; }

        /* ── RIGHT PANEL (scrollable) ─────────────────── */
        .right-panel {
            flex:1;
            background:#f9fafb;
            padding:48px 52px 64px;
            overflow-y:auto;
        }

        .right-inner { max-width:540px;margin:0 auto; }

        /* Role selector */
        .role-selector { display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px; }
        .role-card {
            background:#fff;border:2px solid #e5e7eb;border-radius:14px;
            padding:18px 16px;cursor:pointer;transition:all .2s;position:relative;
        }
        .role-card:hover { border-color:#d1d5db;box-shadow:0 2px 12px rgba(0,0,0,.06); }
        .role-card.publisher.selected  { border-color:#01BF63;background:#f0fdf7; }
        .role-card.advertiser.selected { border-color:#3b82f6;background:#eff6ff; }
        .role-card-icon {
            width:38px;height:38px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;margin-bottom:9px;
        }
        .role-card.publisher  .role-card-icon { background:#dcfce7; }
        .role-card.advertiser .role-card-icon { background:#dbeafe; }
        .role-card-title { font-size:14px;font-weight:700;color:#111827;margin-bottom:3px; }
        .role-card-sub   { font-size:12px;color:#6b7280;line-height:1.5; }
        .role-card .check {
            position:absolute;top:11px;right:11px;
            width:20px;height:20px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;
            opacity:0;transition:opacity .2s;
        }
        .role-card.publisher.selected  .check { background:#01BF63;opacity:1; }
        .role-card.advertiser.selected .check { background:#3b82f6;opacity:1; }

        /* Form panel */
        .form-panel { display:none; }
        .form-panel.active { display:block; }

        .card {
            background:#fff;border-radius:16px;padding:28px 28px 32px;
            box-shadow:0 2px 16px rgba(0,0,0,.06);border:1px solid #e5e7eb;
            margin-bottom:14px;
        }
        .card-header {
            margin-bottom:22px;padding-bottom:16px;
            border-bottom:1px solid #f3f4f6;
            display:flex;align-items:center;justify-content:space-between;
            flex-wrap:wrap;gap:10px;
        }
        .card-title { font-size:17px;font-weight:700;color:#111827; }
        .card-sub   { font-size:13px;color:#6b7280;margin-top:2px; }
        .read-before-link {
            display:inline-flex;align-items:center;gap:5px;
            font-size:12px;font-weight:600;color:#d97706;
            text-decoration:none;border:1px solid #fcd34d;background:#fffbeb;
            padding:5px 10px;border-radius:20px;white-space:nowrap;transition:all .15s;
        }
        .read-before-link:hover { background:#fef3c7; }

        .form-group { margin-bottom:16px; }
        .form-label { display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px; }
        .form-label .req { color:#ef4444;margin-left:2px; }
        .form-label .opt { color:#9ca3af;font-weight:400;font-size:11px;margin-left:4px; }
        .form-control {
            width:100%;padding:11px 13px;
            border:1.5px solid #e5e7eb;border-radius:10px;
            font-size:14px;font-family:inherit;outline:none;
            transition:all .15s;color:#111827;background:#fff;
        }
        .form-control.green:focus { border-color:#01BF63;box-shadow:0 0 0 3px rgba(1,191,99,.1); }
        .form-control.blue:focus  { border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.1); }
        .form-hint { font-size:12px;color:#6b7280;margin-top:4px;display:flex;align-items:flex-start;gap:5px;line-height:1.5; }
        .grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
        @media(max-width:520px){ .grid-2 { grid-template-columns:1fr; } }

        .section-divider { border-top:1px solid #f3f4f6;margin:18px 0;padding-top:18px; }
        .section-label { font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px; }

        .contact-notice {
            background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:10px;
            padding:11px 13px;margin-bottom:16px;font-size:12px;color:#1e40af;line-height:1.6;
        }

        .btn-submit {
            width:100%;padding:13px;border:none;border-radius:10px;
            font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;
            transition:all .15s;display:flex;align-items:center;justify-content:center;
            gap:8px;color:#fff;
        }
        .btn-submit.green { background:#01BF63; }
        .btn-submit.green:hover { background:#00a354;transform:translateY(-1px);box-shadow:0 4px 14px rgba(1,191,99,.28); }
        .btn-submit.blue  { background:#3b82f6; }
        .btn-submit.blue:hover  { background:#2563eb;transform:translateY(-1px);box-shadow:0 4px 14px rgba(59,130,246,.28); }

        .form-footer { text-align:center;margin-top:16px;font-size:13px;color:#6b7280; }
        .form-footer a.green { color:#01BF63;font-weight:600;text-decoration:none; }
        .form-footer a.blue  { color:#3b82f6;font-weight:600;text-decoration:none; }

        .alert-danger {
            background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;
            padding:12px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;
        }
        .alert-danger ul { padding-left:18px;margin-top:4px; }

        .back-link { text-align:center;margin-top:10px; }
        .back-link a { color:#9ca3af;font-size:13px;text-decoration:none; }
        .back-link a:hover { color:#374151; }

        /* Upload */
        .upload-area {
            border:2px dashed #d1d5db;border-radius:12px;padding:22px 18px;
            text-align:center;cursor:pointer;transition:all .15s;background:#fafafa;position:relative;
        }
        .upload-area:hover,.upload-area.drag-over { border-color:#01BF63;background:#f0fdf7; }
        .upload-area input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
        .upload-icon { width:42px;height:42px;background:#e6faf2;border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 9px; }
        .upload-text { font-size:13px;font-weight:600;color:#374151;margin-bottom:3px; }
        .upload-sub  { font-size:11px;color:#9ca3af; }
        .preview-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:10px; }
        .preview-item { position:relative;border-radius:8px;overflow:hidden;aspect-ratio:16/9;background:#f3f4f6; }
        .preview-item img { width:100%;height:100%;object-fit:cover; }
        .preview-remove {
            position:absolute;top:4px;right:4px;width:20px;height:20px;
            background:rgba(0,0,0,.6);border-radius:50%;display:flex;align-items:center;
            justify-content:center;cursor:pointer;color:#fff;font-size:12px;font-weight:700;z-index:1;
        }
        .upload-count {
            display:inline-flex;align-items:center;gap:6px;margin-top:7px;
            font-size:12px;font-weight:600;color:#374151;background:#f3f4f6;
            padding:3px 10px;border-radius:20px;
        }

        /* Modal */
        .modal-overlay {
            position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;
            display:flex;align-items:center;justify-content:center;padding:20px;
            opacity:0;pointer-events:none;transition:opacity .2s;
        }
        .modal-overlay.open { opacity:1;pointer-events:all; }
        .modal {
            background:#fff;border-radius:16px;max-width:540px;width:100%;
            max-height:85vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);
            transform:translateY(20px);transition:transform .2s;
        }
        .modal-overlay.open .modal { transform:translateY(0); }
        .modal-head {
            padding:20px 22px 14px;border-bottom:1px solid #f3f4f6;
            display:flex;align-items:center;justify-content:space-between;
            position:sticky;top:0;background:#fff;border-radius:16px 16px 0 0;z-index:1;
        }
        .modal-title { font-size:15px;font-weight:700;color:#111827; }
        .modal-close {
            width:30px;height:30px;border-radius:8px;border:none;background:#f3f4f6;
            cursor:pointer;display:flex;align-items:center;justify-content:center;
            font-size:15px;color:#6b7280;transition:background .15s;
        }
        .modal-close:hover { background:#e5e7eb; }
        .modal-body { padding:18px 22px 22px; }

        .req-box { background:#f0fdf9;border:1.5px solid #a7f3d0;border-radius:12px;padding:14px 16px;margin-bottom:16px; }
        .req-box-title { font-size:13px;font-weight:700;color:#065f46;margin-bottom:10px;display:flex;align-items:center;gap:6px; }
        .req-item { display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#374151;margin-bottom:7px; }
        .req-icon { width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px; }
        .req-icon.green { background:#dcfce7; }
        .req-icon.red   { background:#fee2e2; }
        .stats-grid { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px; }
        .stats-card { border-radius:10px;padding:12px 13px; }
        .stats-card.accepted { background:#f0fdf4;border:1.5px solid #86efac; }
        .stats-card.rejected { background:#fef2f2;border:1.5px solid #fca5a5; }
        .stats-card-title { font-size:12px;font-weight:700;margin-bottom:7px;display:flex;align-items:center;gap:5px; }
        .stats-card.accepted .stats-card-title { color:#15803d; }
        .stats-card.rejected .stats-card-title { color:#b91c1c; }
        .stats-item { font-size:12px;color:#374151;margin-bottom:3px; }

        /* Responsive */
        @media(max-width:900px){
            body { flex-direction:column; }
            .left-panel { width:100%;height:auto;position:static;padding:36px 28px 40px; }
            .features,.stats-bar { display:none; }
            .right-panel { padding:32px 20px 56px; }
        }
        @media(max-width:480px){
            .left-panel { padding:28px 18px 32px; }
            .hero-title { font-size:26px; }
        }
    </style>
</head>
<body>

    {{-- LEFT PANEL --}}
    <div class="left-panel">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="grid-overlay"></div>

        <div class="left-content">
            <div class="brand">
                <img src="https://installsbank.com/images/logo.webp" alt="Installs Bank"
                     onerror="this.src='https://installsbank.com/images/installs-bank.webp'">
                <div class="brand-name">Installs Bank</div>
            </div>

            <div class="hero-title">
                Join the network.<br>
                <span>Start earning today.</span>
            </div>
            <div class="hero-sub">
                Thousands of publishers already earn daily from their website traffic. It takes less than 2 minutes to apply.
            </div>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Approved in 24–48 Hours</div>
                        <div class="feature-sub">Fast review process, get started quickly</div>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Earn From Day One</div>
                        <div class="feature-sub">Start generating revenue as soon as you're approved</div>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Dedicated Account Manager</div>
                        <div class="feature-sub">Personal support to maximise your earnings</div>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Multiple Payout Methods</div>
                        <div class="feature-sub">Crypto & more — paid every week</div>
                    </div>
                </div>
            </div>

            <div class="stats-bar">
                <div>
                    <div class="stat-value">500+</div>
                    <div class="stat-label">Active Publishers</div>
                </div>
                <div>
                    <div class="stat-value">$2M+</div>
                    <div class="stat-label">Total Paid Out</div>
                </div>
                <div>
                    <div class="stat-value">50+</div>
                    <div class="stat-label">Countries</div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="right-panel">
        <div class="right-inner">

            @if($errors->any())
            <div class="alert-danger">
                <strong>Please fix the following:</strong>
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            {{-- Role Selector --}}
            <div class="role-selector">
                <div class="role-card publisher {{ old('_role','publisher') === 'publisher' ? 'selected' : '' }}"
                     onclick="selectRole('publisher')" id="card-publisher">
                    <div class="check">
                        <svg width="11" height="11" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="role-card-icon">
                        <svg width="19" height="19" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="role-card-title">Publisher</div>
                    <div class="role-card-sub">Earn money by placing ads on your website</div>
                </div>
                <div class="role-card advertiser {{ old('_role') === 'advertiser' ? 'selected' : '' }}"
                     onclick="selectRole('advertiser')" id="card-advertiser">
                    <div class="check">
                        <svg width="11" height="11" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="role-card-icon">
                        <svg width="19" height="19" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div class="role-card-title">Advertiser</div>
                    <div class="role-card-sub">Run campaigns and drive targeted installs</div>
                </div>
            </div>

            {{-- Publisher Form --}}
            <div class="form-panel {{ old('_role','publisher') === 'publisher' ? 'active' : '' }}" id="panel-publisher">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Publisher Registration</div>
                            <div class="card-sub">Submit your website for review</div>
                        </div>
                        <a class="read-before-link" onclick="openModal()">
                            <svg width="13" height="13" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Read before Registering
                        </a>
                    </div>
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_role" value="publisher">
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="req">*</span></label>
                            <input type="text" name="name" class="form-control green" value="{{ old('name') }}" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address <span class="req">*</span></label>
                            <input type="email" name="email" class="form-control green" value="{{ old('email') }}" placeholder="you@example.com" required>
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Password <span class="req">*</span></label>
                                <input type="password" name="password" class="form-control green" placeholder="Min 8 characters" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm Password <span class="req">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control green" placeholder="Repeat password" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Website URL <span class="req">*</span></label>
                            <input type="url" name="website" class="form-control green" value="{{ old('website') }}" placeholder="https://yoursite.com" required>
                            <div class="form-hint">
                                <svg width="12" height="12" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span style="color:#d97706">Your ad code will only work on this domain.</span>
                            </div>
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">WhatsApp <span class="opt">(optional)</span></label>
                                <input type="text" name="phone" class="form-control green" value="{{ old('phone') }}" placeholder="+1 234 567 8900">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Telegram <span class="opt">(optional)</span></label>
                                <input type="text" name="telegram" class="form-control green" value="{{ old('telegram') }}" placeholder="@username">
                            </div>
                        </div>
                        <div class="section-divider">
                            <div class="section-label">Traffic Screenshots</div>
                            <div class="upload-area" id="uploadArea">
                                <input type="file" name="screenshots[]" id="screenshotInput" accept="image/jpeg,image/png,image/webp" multiple>
                                <div class="upload-icon">
                                    <svg width="22" height="22" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                </div>
                                <div class="upload-text">Click to upload or drag & drop</div>
                                <div class="upload-sub">JPG, PNG or WebP · Max 5MB each · Up to 4 screenshots</div>
                            </div>
                            <div id="previewGrid" class="preview-grid" style="display:none;"></div>
                            <div style="text-align:center">
                                <span class="upload-count" id="uploadCount" style="display:none">
                                    <svg width="12" height="12" fill="none" stroke="#374151" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                                    <span id="uploadCountText">0 / 4 screenshots</span>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn-submit green" style="margin-top:8px">
                            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Submit Application
                        </button>
                    </form>
                    <div class="form-footer">Already have an account? <a href="{{ route('login') }}" class="green">Sign in</a></div>
                </div>
            </div>

            {{-- Advertiser Form --}}
            <div class="form-panel {{ old('_role') === 'advertiser' ? 'active' : '' }}" id="panel-advertiser">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Advertiser Registration</div>
                            <div class="card-sub">Create your advertiser account</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('advertiser.register') }}">
                        @csrf
                        <input type="hidden" name="_role" value="advertiser">
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="req">*</span></label>
                            <input type="text" name="name" class="form-control blue" value="{{ old('name') }}" placeholder="John Doe" required autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address <span class="req">*</span></label>
                            <input type="email" name="email" class="form-control blue" value="{{ old('email') }}" placeholder="you@company.com" required autocomplete="email">
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Company Name <span class="opt">(optional)</span></label>
                                <input type="text" name="company_name" class="form-control blue" value="{{ old('company_name') }}" placeholder="Acme Inc.">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Website <span class="opt">(optional)</span></label>
                                <input type="url" name="website" class="form-control blue" value="{{ old('website') }}" placeholder="https://yourapp.com">
                            </div>
                        </div>
                        <div class="section-divider">
                            <div class="section-label">Contact &amp; Communication</div>
                            <div class="contact-notice">
                                <strong>Telegram and WhatsApp are required</strong> for campaign management. Our team uses these to coordinate campaign setup, billing, and updates.
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label class="form-label">Telegram Username <span class="req">*</span></label>
                                    <input type="text" name="telegram" class="form-control blue" value="{{ old('telegram') }}" placeholder="@username" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">WhatsApp Number <span class="req">*</span></label>
                                    <input type="text" name="whatsapp" class="form-control blue" value="{{ old('whatsapp') }}" placeholder="+1 234 567 8900" required>
                                </div>
                            </div>
                        </div>
                        <div class="section-divider">
                            <div class="section-label">Password</div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label class="form-label">Password <span class="req">*</span></label>
                                    <input type="password" name="password" class="form-control blue" placeholder="Min 8 characters" required minlength="8" autocomplete="new-password">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Confirm Password <span class="req">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control blue" placeholder="Repeat password" required autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn-submit blue">
                            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Create Advertiser Account
                        </button>
                    </form>
                    <div class="form-footer">Already have an account? <a href="{{ route('login') }}" class="blue">Sign in</a></div>
                </div>
            </div>

            <div class="back-link"><a href="{{ route('home') }}">← Back to Home</a></div>
        </div>
    </div>

    {{-- Publisher Info Modal --}}
    <div class="modal-overlay" id="infoModal" onclick="closeModalOutside(event)">
        <div class="modal">
            <div class="modal-head">
                <div class="modal-title">Publisher Requirements</div>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="req-box">
                    <div class="req-box-title">
                        <svg width="14" height="14" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Acceptance Requirements
                    </div>
                    <div class="req-item">
                        <div class="req-icon green"><svg width="10" height="10" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                        <span>Your website must have <strong>at least 500 unique visitors per day</strong></span>
                    </div>
                    <div class="req-item">
                        <div class="req-icon green"><svg width="10" height="10" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                        <span>Traffic must be organic and real — no bots, click farms, or paid traffic</span>
                    </div>
                    <div class="req-item">
                        <div class="req-icon red"><svg width="10" height="10" fill="none" stroke="#dc2626" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></div>
                        <span>No adult, illegal, pirated, or harmful content</span>
                    </div>
                    <div class="req-item" style="margin-bottom:0">
                        <div class="req-icon red"><svg width="10" height="10" fill="none" stroke="#dc2626" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></div>
                        <span>Ad code will <strong>only work on your registered website</strong></span>
                    </div>
                </div>
                <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:10px">Which statistics screenshots we accept:</div>
                <div class="stats-grid">
                    <div class="stats-card accepted">
                        <div class="stats-card-title">
                            <svg width="12" height="12" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Accepted
                        </div>
                        <div class="stats-item">✓ Google Analytics</div>
                        <div class="stats-item">✓ Google Search Console</div>
                        <div class="stats-item">✓ Yandex Metrica</div>
                        <div class="stats-item">✓ Bing Webmaster Tools</div>
                    </div>
                    <div class="stats-card rejected">
                        <div class="stats-card-title">
                            <svg width="12" height="12" fill="none" stroke="#dc2626" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Not Accepted
                        </div>
                        <div class="stats-item">✗ Cloudflare Analytics</div>
                        <div class="stats-item">✗ Hosting panel stats</div>
                        <div class="stats-item">✗ AWStats / Webalizer</div>
                        <div class="stats-item">✗ Third-party counters</div>
                    </div>
                </div>
                <div style="background:#fffbeb;border-radius:8px;padding:10px 13px;font-size:12px;color:#92400e;">
                    <strong>Required:</strong> Screenshots must show the <strong>last 28 days</strong> of traffic data including unique visitors/users per day. Make sure the date range is visible.
                </div>
            </div>
        </div>
    </div>

<script>
function selectRole(role) {
    ['publisher','advertiser'].forEach(r => {
        document.getElementById('card-'  + r).classList.toggle('selected', r === role);
        document.getElementById('panel-' + r).classList.toggle('active',   r === role);
    });
}
@if(old('_role') === 'advertiser')
    document.addEventListener('DOMContentLoaded', () => selectRole('advertiser'));
@endif

function openModal()  { document.getElementById('infoModal').classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal() { document.getElementById('infoModal').classList.remove('open'); document.body.style.overflow=''; }
function closeModalOutside(e) { if(e.target===document.getElementById('infoModal')) closeModal(); }
document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });

// Screenshot upload
const input=document.getElementById('screenshotInput'),area=document.getElementById('uploadArea'),
      grid=document.getElementById('previewGrid'),countWrap=document.getElementById('uploadCount'),
      countText=document.getElementById('uploadCountText');
let files=[];
if(input){
    input.addEventListener('change', ()=>handleFiles(input.files));
    area.addEventListener('dragover',  e=>{ e.preventDefault(); area.classList.add('drag-over'); });
    area.addEventListener('dragleave', ()=>area.classList.remove('drag-over'));
    area.addEventListener('drop', e=>{ e.preventDefault(); area.classList.remove('drag-over'); handleFiles(e.dataTransfer.files); });
}
function handleFiles(nf){ for(const f of nf){ if(files.length>=4)break; if(!f.type.match(/image\/(jpeg|png|webp)/))continue; files.push(f); } syncInput(); renderPreviews(); }
function syncInput(){ const dt=new DataTransfer(); files.forEach(f=>dt.items.add(f)); if(input)input.files=dt.files; }
function renderPreviews(){
    grid.innerHTML='';
    if(!files.length){ grid.style.display='none'; countWrap.style.display='none'; return; }
    grid.style.display='grid'; countWrap.style.display='inline-flex';
    countText.textContent=files.length+' / 4 screenshot'+(files.length!==1?'s':'');
    files.forEach((f,i)=>{
        const url=URL.createObjectURL(f),item=document.createElement('div');
        item.className='preview-item';
        item.innerHTML=`<img src="${url}"><div class="preview-remove" onclick="removeFile(${i})">✕</div>`;
        grid.appendChild(item);
    });
}
function removeFile(i){ files.splice(i,1); syncInput(); renderPreviews(); }
</script>
</body>
</html>
