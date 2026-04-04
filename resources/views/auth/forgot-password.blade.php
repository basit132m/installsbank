<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Installs Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f0fdf7 0%, #e6faf2 50%, #f9fafb 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-container { width: 100%; max-width: 440px; padding: 24px; }
        .logo-wrap { text-align: center; margin-bottom: 32px; }
        .logo-wrap img { height: 40px; margin-bottom: 8px; }
        .logo-text { font-size: 22px; font-weight: 800; color: #111827; }
        .logo-sub { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .card { background: white; border-radius: 16px; padding: 36px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; }
        .card-title { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .card-sub { font-size: 14px; color: #6b7280; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 11px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: all 0.15s; color: #111827; }
        .form-control:focus { border-color: #01BF63; box-shadow: 0 0 0 3px rgba(1,191,99,0.1); }
        .btn-submit { width: 100%; padding: 12px; background: #01BF63; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.15s; }
        .btn-submit:hover { background: #00a354; }
        .form-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; }
        .form-footer a { color: #01BF63; font-weight: 600; text-decoration: none; }
        .alert { padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #6b7280; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
        .back-link a:hover { color: #111827; }
        .icon-wrap { width: 56px; height: 56px; background: #e6faf2; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo-wrap">
            <img src="https://installsbank.com/images/installs-bank.webp" alt="Installs Bank" onerror="this.style.display='none'">
            <div class="logo-text">Installs Bank</div>
            <div class="logo-sub">Premium Publisher Network</div>
        </div>
        <div class="card">
            <div class="icon-wrap">
                <svg width="26" height="26" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="card-title" style="text-align:center;">Forgot Password?</h2>
            <p class="card-sub" style="text-align:center;">Enter your email and we'll send you a reset link.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                </div>
                <button type="submit" class="btn-submit">Send Reset Link</button>
            </form>

            <div class="form-footer">
                Remembered it? <a href="{{ route('login') }}">Back to Sign In</a>
            </div>
        </div>
        <div class="back-link">
            <a href="{{ route('home') }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
