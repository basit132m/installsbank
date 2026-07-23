<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('portal')->check()) {
            return redirect()->route('portal.dashboard');
        }
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $ok = Auth::guard('portal')->attempt([
            'username' => $data['username'],
            'password' => $data['password'],
        ], $request->boolean('remember'));

        if (!$ok) {
            return back()->withErrors(['username' => 'Invalid username or password.'])->onlyInput('username');
        }

        $account = Auth::guard('portal')->user();
        if (!$account->is_active) {
            Auth::guard('portal')->logout();
            return back()->withErrors(['username' => 'This account is disabled.'])->onlyInput('username');
        }

        $account->forceFill(['last_login_at' => now()])->save();
        $request->session()->regenerate();

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('portal')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
    }
}
