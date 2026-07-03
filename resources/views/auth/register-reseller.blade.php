<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseller Registration — Installs Bank</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%237c3aed'/><text x='16' y='23' font-family='Arial,sans-serif' font-size='13' font-weight='800' fill='white' text-anchor='middle'>IB</text></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f9fafb; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
        .wrap { width:100%; max-width:560px; }
        .logo { text-align:center; margin-bottom:24px; }
        .logo img { height:40px; }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:32px; box-shadow:0 4px 20px rgba(0,0,0,.05); }
        h1 { font-size:22px; font-weight:800; color:#111827; margin-bottom:6px; }
        .sub { font-size:13px; color:#6b7280; margin-bottom:24px; line-height:1.6; }
        .chip { display:inline-block; background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe; border-radius:20px; padding:4px 14px; font-size:12px; font-weight:700; margin-bottom:14px; }
        label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
        .fg { margin-bottom:16px; }
        input[type=text], input[type=email], input[type=password], input[type=url] {
            width:100%; padding:11px 14px; border:1px solid #d1d5db; border-radius:9px; font-size:14px; font-family:inherit; outline:none; transition:border-color .15s;
        }
        input:focus { border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.1); }
        .row2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        @media(max-width:520px){ .row2 { grid-template-columns:1fr; } }
        .btn { width:100%; background:#7c3aed; color:#fff; border:none; border-radius:10px; padding:13px; font-size:15px; font-weight:700; cursor:pointer; font-family:inherit; transition:background .15s; }
        .btn:hover { background:#6d28d9; }
        .err { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; border-radius:9px; padding:11px 14px; font-size:13px; margin-bottom:16px; }
        .foot { text-align:center; font-size:13px; color:#6b7280; margin-top:18px; }
        .foot a { color:#7c3aed; font-weight:600; text-decoration:none; }
        .drop { border:2px dashed #d1d5db; border-radius:12px; padding:22px; text-align:center; cursor:pointer; background:#fafafa; transition:all .2s; }
        .hint { font-size:11px; color:#9ca3af; margin-top:4px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="logo">
        <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" onerror="this.outerHTML='<div style=&quot;font-size:20px;font-weight:900;&quot;>Installs Bank</div>'">
    </div>
    <div class="card">
        <div class="chip">RESELLER PROGRAM</div>
        <h1>Create your Reseller Account</h1>
        <div class="sub">
            Join as a reseller: add your websites, get dedicated ad codes and track your traffic live.
            Your application is reviewed by our team before activation.
        </div>

        @if($errors->any())
        <div class="err">
            @foreach($errors->all() as $e)<div>✗ {{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('reseller.register') }}" enctype="multipart/form-data">
            @csrf
            <div class="fg">
                <label>Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Your name">
            </div>
            <div class="fg">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com">
            </div>
            <div class="row2">
                <div class="fg">
                    <label>Password *</label>
                    <input type="password" name="password" required minlength="8" placeholder="Min. 8 characters">
                </div>
                <div class="fg">
                    <label>Confirm Password *</label>
                    <input type="password" name="password_confirmation" required minlength="8" placeholder="Repeat password">
                </div>
            </div>
            <div class="fg">
                <label>Main Website *</label>
                <input type="url" name="website" value="{{ old('website') }}" required placeholder="https://yoursite.com">
                <div class="hint">You can add more websites after approval.</div>
            </div>
            <div class="row2">
                <div class="fg">
                    <label>Phone (optional)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+92 ...">
                </div>
                <div class="fg">
                    <label>Telegram (optional)</label>
                    <input type="text" name="telegram" value="{{ old('telegram') }}" placeholder="@username">
                </div>
            </div>

            <div class="fg">
                <label>Traffic Statistics Screenshots * <span style="font-weight:400;color:#9ca3af;">(1–4 images, max 5MB each)</span></label>
                <div class="drop" onclick="document.getElementById('shots').click()"
                     ondragover="event.preventDefault();this.style.borderColor='#7c3aed';this.style.background='#f5f3ff';"
                     ondragleave="this.style.borderColor='#d1d5db';this.style.background='#fafafa';"
                     ondrop="handleDrop(event)">
                    <div style="font-size:14px;font-weight:600;color:#374151;">Click or drag screenshots here</div>
                    <div class="hint">Google Analytics, Yandex Metrica, GSC, Bing Webmaster</div>
                </div>
                <input type="file" id="shots" name="screenshots[]" accept="image/*" multiple style="display:none;" onchange="previewShots(this)">
                <div id="preview" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:12px;"></div>
            </div>

            <button type="submit" class="btn">Create Reseller Account</button>
        </form>

        <div class="foot">
            Already have an account? <a href="{{ route('login') }}">Log in</a>
        </div>
    </div>
</div>

<script>
let files = [];

function previewShots(input) {
    Array.from(input.files).slice(0, 4 - files.length).forEach(addThumb);
    sync();
}

function addThumb(file) {
    files.push(file);
    const reader  = new FileReader();
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;border:1.5px solid #e5e7eb;';
    reader.onload = e => {
        wrapper.innerHTML = `
            <img src="${e.target.result}" style="width:100px;height:70px;object-fit:cover;display:block;">
            <button type="button" style="position:absolute;top:3px;right:3px;background:rgba(0,0,0,.6);color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:12px;cursor:pointer;line-height:1;">×</button>`;
        wrapper.querySelector('button').onclick = () => {
            files.splice(Array.from(wrapper.parentNode.children).indexOf(wrapper), 1);
            wrapper.remove();
            sync();
        };
    };
    reader.readAsDataURL(file);
    document.getElementById('preview').appendChild(wrapper);
}

function sync() {
    const dt = new DataTransfer();
    files.forEach(f => dt.items.add(f));
    document.getElementById('shots').files = dt.files;
}

function handleDrop(event) {
    event.preventDefault();
    const dz = event.currentTarget;
    dz.style.borderColor = '#d1d5db';
    dz.style.background  = '#fafafa';
    Array.from(event.dataTransfer.files)
        .filter(f => f.type.startsWith('image/'))
        .slice(0, 4 - files.length)
        .forEach(addThumb);
    sync();
}
</script>
</body>
</html>
