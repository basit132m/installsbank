<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Installs Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f0fdf7 0%, #e6faf2 50%, #f9fafb 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .auth-container { width: 100%; max-width: 480px; }
        .logo-wrap { text-align: center; margin-bottom: 28px; }
        .logo-wrap img { height: 40px; margin-bottom: 8px; }
        .logo-text { font-size: 22px; font-weight: 800; color: #111827; }
        .card { background: white; border-radius: 16px; padding: 36px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; }
        .card-title { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .card-sub { font-size: 14px; color: #6b7280; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 11px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: all 0.15s; color: #111827; }
        .form-control:focus { border-color: #01BF63; box-shadow: 0 0 0 3px rgba(1,191,99,0.1); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .btn-submit { width: 100%; padding: 12px; background: #01BF63; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.15s; }
        .btn-submit:hover { background: #00a354; }
        .form-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; }
        .form-footer a { color: #01BF63; font-weight: 600; text-decoration: none; }
        .alert { padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .info-box { background: #e6faf2; border: 1px solid #a7f3d0; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #065f46; margin-bottom: 20px; }
        .back-link { text-align: center; margin-top: 16px; }
        .back-link a { color: #6b7280; font-size: 13px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo-wrap">
            <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" onerror="this.style.display='none'">
            <div class="logo-text">Installs Bank</div>
        </div>
        <div class="card">
            <h2 class="card-title">Create Publisher Account</h2>
            <p class="card-sub">Join our network and start earning</p>

            <div class="info-box">
                <strong>⚠️ Requirements before applying:</strong>
                <ul style="margin-top:8px;padding-left:18px;line-height:1.8;">
                    <li>Your website must have <strong>at least 500 unique visitors per day</strong></li>
                    <li>Traffic must be real — no bots, paid traffic exchanges, or click farms</li>
                    <li>Websites with adult, illegal, or pirated content are not accepted</li>
                </ul>
                <div style="margin-top:10px;padding-top:10px;border-top:1px solid #a7f3d0;font-size:12px;">
                    If your site does not meet these requirements, please do not apply — your account will be rejected.
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">@foreach($errors->all() as $e){{ $e }}<br>@endforeach</div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="you@example.com" required>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min 8 chars" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Website (Optional)</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://yoursite.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone (Optional)</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 234 567 8900">
                </div>
                <button type="submit" class="btn-submit">Create Account</button>
            </form>
            <div class="form-footer">
                Already have an account? <a href="{{ route('login') }}">Sign in</a>
            </div>
        </div>
        <div class="back-link">
            <a href="{{ route('home') }}">← Back to Home</a>
        </div>
    </div>
</body>
</html>
