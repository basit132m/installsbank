<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewPublisherMailable;
use App\Mail\VerifyEmailMailable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    public function verify(string $token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'This verification link is invalid or has already been used.']);
        }

        // Mark as verified
        $user->update([
            'email_verified_at'  => now(),
            'verification_token' => null,
        ]);

        // Notify all admins
        User::where('role', 'admin')->each(function ($admin) use ($user) {
            try {
                Mail::to($admin->email)->send(new AdminNewPublisherMailable($user));
            } catch (\Exception) {
                // Don't block the publisher flow if admin email fails
            }
        });

        $user->update(['last_login_at' => now()]);
        Auth::login($user);

        session()->flash('show_welcome', true);

        return redirect()->route('publisher.dashboard')
            ->with('success', 'Your email has been verified! Your account is now pending admin review.');
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)
            ->whereNull('email_verified_at')
            ->first();

        if (!$user) {
            // Don't reveal whether email exists — just show success
            return back()->with('resent', true);
        }

        // Throttle: don't resend if token was set within last 2 minutes
        if ($user->updated_at && $user->updated_at->diffInMinutes(now()) < 2) {
            return back()->withErrors(['email' => 'Please wait a moment before requesting another email.']);
        }

        $user->update(['verification_token' => Str::random(64)]);

        try {
            Mail::to($user->email)->send(new VerifyEmailMailable($user));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send email. Please try again shortly.']);
        }

        return back()->with('resent', true);
    }
}
