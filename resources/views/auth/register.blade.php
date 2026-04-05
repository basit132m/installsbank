<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Installs Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f0fdf7 0%, #e6faf2 50%, #f9fafb 100%); min-height: 100vh; display: flex; align-items: flex-start; justify-content: center; padding: 32px 16px; }
        .auth-container { width: 100%; max-width: 600px; }
        .logo-wrap { text-align: center; margin-bottom: 28px; }
        .logo-wrap img { height: 40px; margin-bottom: 8px; }
        .logo-text { font-size: 22px; font-weight: 800; color: #111827; }
        .card { background: white; border-radius: 16px; padding: 36px; box-shadow: 0 4px 24px rgba(0,0,0,0.07); border: 1px solid #e5e7eb; margin-bottom: 16px; }
        .card-title { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .card-sub { font-size: 14px; color: #6b7280; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-label .req { color: #ef4444; margin-left: 2px; }
        .form-label .opt { color: #9ca3af; font-weight: 400; font-size: 11px; margin-left: 4px; }
        .form-control { width: 100%; padding: 11px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: all 0.15s; color: #111827; background: white; }
        .form-control:focus { border-color: #01BF63; box-shadow: 0 0 0 3px rgba(1,191,99,0.1); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media(max-width:520px){ .grid-2 { grid-template-columns: 1fr; } }
        .btn-submit { width: 100%; padding: 13px; background: #01BF63; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-submit:hover { background: #00a354; }
        .form-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; }
        .form-footer a { color: #01BF63; font-weight: 600; text-decoration: none; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
        .alert-danger ul { padding-left: 18px; margin-top: 4px; }
        .back-link { text-align: center; margin-top: 12px; }
        .back-link a { color: #6b7280; font-size: 13px; text-decoration: none; }
        .step-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f3f4f6; }
        .step-num { width: 32px; height: 32px; background: #01BF63; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; flex-shrink: 0; }
        .step-title { font-size: 16px; font-weight: 700; color: #111827; }
        .step-sub { font-size: 12px; color: #6b7280; margin-top: 1px; }

        /* Requirements box */
        .req-box { background: #f0fdf9; border: 1.5px solid #a7f3d0; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .req-box-title { font-size: 14px; font-weight: 700; color: #065f46; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
        .req-list { display: flex; flex-direction: column; gap: 8px; }
        .req-item { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #374151; }
        .req-icon { width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
        .req-icon.green { background: #dcfce7; }
        .req-icon.red { background: #fee2e2; }

        /* Stats accepted/not accepted */
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 14px; }
        .stats-card { border-radius: 10px; padding: 12px 14px; }
        .stats-card.accepted { background: #f0fdf4; border: 1.5px solid #86efac; }
        .stats-card.rejected { background: #fef2f2; border: 1.5px solid #fca5a5; }
        .stats-card-title { font-size: 12px; font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .stats-card.accepted .stats-card-title { color: #15803d; }
        .stats-card.rejected .stats-card-title { color: #b91c1c; }
        .stats-item { font-size: 12px; color: #374151; display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }

        /* Upload area */
        .upload-area { border: 2px dashed #d1d5db; border-radius: 12px; padding: 28px 20px; text-align: center; cursor: pointer; transition: all 0.15s; background: #fafafa; position: relative; }
        .upload-area:hover, .upload-area.drag-over { border-color: #01BF63; background: #f0fdf7; }
        .upload-area input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .upload-icon { width: 48px; height: 48px; background: #e6faf2; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
        .upload-text { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
        .upload-sub { font-size: 12px; color: #9ca3af; }
        .preview-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 14px; }
        .preview-item { position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 16/9; background: #f3f4f6; }
        .preview-item img { width: 100%; height: 100%; object-fit: cover; }
        .preview-remove { position: absolute; top: 4px; right: 4px; width: 20px; height: 20px; background: rgba(0,0,0,0.6); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: white; font-size: 12px; font-weight: 700; z-index: 1; }
        .upload-count { display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; font-size: 13px; font-weight: 600; color: #374151; background: #f3f4f6; padding: 4px 12px; border-radius: 20px; }

        /* Referrer notice */
        .referrer-notice { background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1.5px solid #fcd34d; border-radius: 12px; padding: 16px 18px; display: flex; gap: 12px; align-items: flex-start; }
        .referrer-notice-icon { width: 36px; height: 36px; background: #fef3c7; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="logo-wrap">
        <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" onerror="this.style.display='none'">
        <div class="logo-text">Installs Bank</div>
    </div>

    @if($errors->any())
    <div class="alert-danger">
        <strong>Please fix the following:</strong>
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <!-- Step 1: Requirements -->
    <div class="card">
        <div class="step-header">
            <div class="step-num">1</div>
            <div>
                <div class="step-title">Before You Apply — Read This</div>
                <div class="step-sub">Make sure you meet these requirements</div>
            </div>
        </div>

        <div class="req-box">
            <div class="req-box-title">
                <svg width="16" height="16" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Acceptance Requirements
            </div>
            <div class="req-list">
                <div class="req-item">
                    <div class="req-icon green"><svg width="11" height="11" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                    <span>Your website must have <strong>at least 500 unique visitors per day</strong></span>
                </div>
                <div class="req-item">
                    <div class="req-icon green"><svg width="11" height="11" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                    <span>Traffic must be organic and real — no bots, click farms, or paid traffic</span>
                </div>
                <div class="req-item">
                    <div class="req-icon red"><svg width="11" height="11" fill="none" stroke="#dc2626" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></div>
                    <span>No adult, illegal, pirated, or harmful content</span>
                </div>
                <div class="req-item">
                    <div class="req-icon red"><svg width="11" height="11" fill="none" stroke="#dc2626" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></div>
                    <span>Your ad code will <strong>only work on the website you register</strong> — not on other domains</span>
                </div>
            </div>
        </div>

        <!-- Stats accepted vs not -->
        <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:10px;">📊 Which statistics screenshots we accept:</div>
        <div class="stats-grid">
            <div class="stats-card accepted">
                <div class="stats-card-title">
                    <svg width="14" height="14" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Accepted Sources
                </div>
                <div class="stats-item"><span style="color:#16a34a;font-weight:700;">✓</span> Google Analytics</div>
                <div class="stats-item"><span style="color:#16a34a;font-weight:700;">✓</span> Google Search Console</div>
                <div class="stats-item"><span style="color:#16a34a;font-weight:700;">✓</span> Yandex Metrica</div>
                <div class="stats-item"><span style="color:#16a34a;font-weight:700;">✓</span> Bing Webmaster Tools</div>
            </div>
            <div class="stats-card rejected">
                <div class="stats-card-title">
                    <svg width="14" height="14" fill="none" stroke="#dc2626" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Not Accepted
                </div>
                <div class="stats-item"><span style="color:#dc2626;font-weight:700;">✗</span> Cloudflare Analytics</div>
                <div class="stats-item"><span style="color:#dc2626;font-weight:700;">✗</span> Hosting panel stats</div>
                <div class="stats-item"><span style="color:#dc2626;font-weight:700;">✗</span> AWStats / Webalizer</div>
                <div class="stats-item"><span style="color:#dc2626;font-weight:700;">✗</span> Third-party counters</div>
            </div>
        </div>
        <div style="margin-top:14px;background:#fffbeb;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;">
            <strong>Required:</strong> Screenshots must show the <strong>last 28 days</strong> of traffic data including unique visitors/users per day. Make sure the date range is visible in your screenshot.
        </div>
    </div>

    <!-- Step 2: Your Info -->
    <div class="card">
        <div class="step-header">
            <div class="step-num">2</div>
            <div>
                <div class="step-title">Your Account Details</div>
                <div class="step-sub">Basic information for your publisher account</div>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
            @csrf

            <div class="form-group">
                <label class="form-label">Full Name <span class="req">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address <span class="req">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="you@example.com" required>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Password <span class="req">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password <span class="req">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">
                    Website URL <span class="req">*</span>
                    <span style="font-size:11px;color:#6b7280;font-weight:400;margin-left:4px;">— The website where you will place our ad code</span>
                </label>
                <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://yoursite.com" required>
                <div style="font-size:11px;color:#f59e0b;margin-top:5px;display:flex;align-items:center;gap:5px;">
                    <svg width="12" height="12" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Your ad code will only work on this domain. Clicks from other websites will not be counted.
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">WhatsApp <span class="opt">(optional)</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 234 567 8900">
                </div>
                <div class="form-group">
                    <label class="form-label">Telegram <span class="opt">(optional)</span></label>
                    <input type="text" name="telegram" class="form-control" value="{{ old('telegram') }}" placeholder="@username">
                </div>
            </div>

            <!-- Step 3: Upload Screenshots -->
            <div style="border-top:1px solid #f3f4f6;padding-top:24px;margin-top:8px;">
                <div class="step-header" style="border-bottom:none;margin-bottom:14px;padding-bottom:0;">
                    <div class="step-num">3</div>
                    <div>
                        <div class="step-title">Upload Statistics Screenshots</div>
                        <div class="step-sub">Last 28 days from Google Analytics, Search Console, Yandex or Bing only</div>
                    </div>
                </div>

                <div class="upload-area" id="uploadArea">
                    <input type="file" name="screenshots[]" id="screenshotInput" accept="image/jpeg,image/png,image/webp" multiple>
                    <div class="upload-icon">
                        <svg width="24" height="24" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div class="upload-text">Click to upload or drag & drop</div>
                    <div class="upload-sub">JPG, PNG or WebP · Max 5MB each · Up to 4 screenshots</div>
                </div>
                <div id="previewGrid" class="preview-grid" style="display:none;"></div>
                <div style="text-align:center;">
                    <span class="upload-count" id="uploadCount" style="display:none;">
                        <svg width="13" height="13" fill="none" stroke="#374151" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                        <span id="uploadCountText">0 / 4 screenshots</span>
                    </span>
                </div>
            </div>

            <!-- Referrer Notice -->
            <div class="referrer-notice" style="margin-top:24px;margin-bottom:24px;">
                <div class="referrer-notice-icon">
                    <svg width="18" height="18" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#92400e;margin-bottom:4px;">Important: Ad Code Domain Restriction</div>
                    <div style="font-size:12px;color:#78350f;line-height:1.7;">
                        The ad code you receive will be <strong>locked to your registered website</strong>. Clicks coming from any other domain will not be counted or paid. Make sure the website URL you enter above is where you plan to use our ad code.
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Submit Application
            </button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>

    <div class="back-link">
        <a href="{{ route('home') }}">← Back to Home</a>
    </div>
</div>

<script>
const input = document.getElementById('screenshotInput');
const area  = document.getElementById('uploadArea');
const grid  = document.getElementById('previewGrid');
const countWrap = document.getElementById('uploadCount');
const countText = document.getElementById('uploadCountText');
let files = [];

input.addEventListener('change', () => handleFiles(input.files));

area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('drag-over'); });
area.addEventListener('dragleave', () => area.classList.remove('drag-over'));
area.addEventListener('drop', e => {
    e.preventDefault();
    area.classList.remove('drag-over');
    handleFiles(e.dataTransfer.files);
});

function handleFiles(newFiles) {
    for (const f of newFiles) {
        if (files.length >= 4) break;
        if (!f.type.match(/image\/(jpeg|png|webp)/)) continue;
        files.push(f);
    }
    syncInput();
    renderPreviews();
}

function syncInput() {
    const dt = new DataTransfer();
    files.forEach(f => dt.items.add(f));
    input.files = dt.files;
}

function renderPreviews() {
    grid.innerHTML = '';
    if (files.length === 0) {
        grid.style.display = 'none';
        countWrap.style.display = 'none';
        return;
    }
    grid.style.display = 'grid';
    countWrap.style.display = 'inline-flex';
    countText.textContent = files.length + ' / 4 screenshot' + (files.length !== 1 ? 's' : '');

    files.forEach((f, i) => {
        const url = URL.createObjectURL(f);
        const item = document.createElement('div');
        item.className = 'preview-item';
        item.innerHTML = `<img src="${url}" alt="screenshot ${i+1}">
            <div class="preview-remove" onclick="removeFile(${i})">✕</div>`;
        grid.appendChild(item);
    });
}

function removeFile(i) {
    files.splice(i, 1);
    syncInput();
    renderPreviews();
}
</script>
</body>
</html>
