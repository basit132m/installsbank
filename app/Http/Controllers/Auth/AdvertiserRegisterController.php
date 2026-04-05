<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdvertiserProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdvertiserRegisterController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.register-advertiser');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users',
            'password'     => 'required|min:8|confirmed',
            'company_name' => 'nullable|string|max:255',
            'website'      => 'nullable|url',
            'telegram'     => 'required|string|max:100',
            'whatsapp'     => 'required|string|max:30',
            'phone'        => 'nullable|string|max:20',
        ], [
            'telegram.required' => 'Telegram username is required for account communication.',
            'whatsapp.required' => 'WhatsApp number is required for account communication.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'advertiser',
            'status'   => 'active', // advertisers are active immediately; campaigns need approval
            'phone'    => $data['phone'] ?? null,
            'telegram' => $data['telegram'],
            'website'  => $data['website'] ?? null,
        ]);

        AdvertiserProfile::create([
            'user_id'      => $user->id,
            'company_name' => $data['company_name'] ?? null,
            'website'      => $data['website'] ?? null,
            'telegram'     => $data['telegram'],
            'whatsapp'     => $data['whatsapp'],
        ]);

        // Fire Registered event → triggers email verification email
        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('advertiser.dashboard')->with('registered', true);
    }

    private function redirectByRole($user)
    {
        return match ($user->role) {
            'advertiser' => redirect()->route('advertiser.dashboard'),
            default      => redirect('/'),
        };
    }
}
