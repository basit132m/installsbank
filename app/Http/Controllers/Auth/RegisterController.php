<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ClickDivider;
use App\Models\PublisherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('publisher.dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'website' => 'nullable|url',
            'phone' => 'nullable|string|max:20',
            'telegram' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'publisher',
            'status' => 'pending',
            'website' => $data['website'] ?? null,
            'phone' => $data['phone'] ?? null,
            'telegram' => $data['telegram'] ?? null,
        ]);

        PublisherProfile::create(['user_id' => $user->id]);
        ClickDivider::create(['user_id' => $user->id, 'divider_value' => 1, 'is_enabled' => false]);

        // Auto-login so publisher lands on dashboard with pending message
        Auth::login($user);

        return redirect()->route('publisher.dashboard')
            ->with('registered', true);
    }
}
