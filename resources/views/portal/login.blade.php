<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%230f172a'/><path d='M8 20l4-5 3 3 5-7' stroke='%2338bdf8' stroke-width='2.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#0f172a; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; color:#e2e8f0; }
        .card { width:100%; max-width:400px; background:#1e293b; border:1px solid #334155; border-radius:18px; padding:34px 30px; box-shadow:0 20px 60px rgba(0,0,0,.4); }
        .logo { width:52px; height:52px; border-radius:14px; background:#0f172a; display:flex; align-items:center; justify-content:center; margin:0 auto 18px; }
        h1 { font-size:20px; font-weight:800; text-align:center; }
        .sub { font-size:13px; color:#94a3b8; text-align:center; margin-top:4px; margin-bottom:24px; }
        label { display:block; font-size:12px; font-weight:600; color:#cbd5e1; margin-bottom:6px; }
        .fg { margin-bottom:16px; }
        input[type=text], input[type=password] { width:100%; padding:12px 14px; border:1px solid #334155; border-radius:10px; font-size:14px; font-family:inherit; background:#0f172a; color:#e2e8f0; outline:none; transition:border-color .15s; }
        input:focus { border-color:#38bdf8; box-shadow:0 0 0 3px rgba(56,189,248,.15); }
        .btn { width:100%; background:#38bdf8; color:#0f172a; border:none; border-radius:10px; padding:13px; font-size:15px; font-weight:800; cursor:pointer; font-family:inherit; transition:background .15s; }
        .btn:hover { background:#0ea5e9; }
        .err { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.35); color:#fca5a5; border-radius:10px; padding:11px 14px; font-size:13px; margin-bottom:16px; }
        .remember { display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:18px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <svg width="26" height="26" fill="none" stroke="#38bdf8" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h1>Analytics Dashboard</h1>
        <div class="sub">Sign in to view your traffic statistics</div>

        @if($errors->any())
        <div class="err">@foreach($errors->all() as $e){{ $e }}@endforeach</div>
        @endif

        <form method="POST" action="{{ route('portal.login') }}">
            @csrf
            <div class="fg">
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username') }}" autofocus required autocomplete="username">
            </div>
            <div class="fg">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>
            <label class="remember"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button type="submit" class="btn">Sign In</button>
        </form>
    </div>
</body>
</html>
