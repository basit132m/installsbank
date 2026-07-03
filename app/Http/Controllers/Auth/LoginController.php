<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status === 'suspended') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been suspended.']);
            }

            // Require email verification for publishers and resellers
            if (in_array($user->role, ['publisher', 'reseller']) && is_null($user->email_verified_at)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('email.check')
                    ->with('email', $user->email)
                    ->withErrors(['email' => 'Please verify your email address before logging in.']);
            }

            $user->update(['last_login_at' => now()]);
            if ($user->role === 'publisher') {
                $request->session()->flash('show_welcome', true);

                // First login after approval — show congratulations overlay once
                $approvalNotif = \App\Models\PublisherNotification::where('user_id', $user->id)
                    ->where('type', 'account_approved')
                    ->whereNull('read_at')
                    ->first();
                if ($approvalNotif) {
                    $approvalNotif->update(['read_at' => now()]);
                    $request->session()->flash('show_approval_welcome', true);
                }
            }
            return $this->redirectByRole($user);
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole($user)
    {
        return match ($user->role) {
            'admin', 'manager' => redirect()->route('admin.dashboard'),
            'publisher'        => redirect()->route('publisher.dashboard'),
            'advertiser'       => redirect()->route('advertiser.dashboard'),
            'reseller'         => redirect()->route('reseller.dashboard'),
            default            => redirect('/'),
        };
    }
}
