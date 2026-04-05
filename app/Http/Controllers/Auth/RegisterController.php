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
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users',
            'password'          => 'required|min:8|confirmed',
            'website'           => 'required|url',
            'phone'             => 'nullable|string|max:20',
            'telegram'          => 'nullable|string|max:100',
            'screenshots'       => 'required|array|min:1|max:4',
            'screenshots.*'     => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'website.required'        => 'Your website URL is required.',
            'website.url'             => 'Please enter a valid website URL (e.g. https://yoursite.com).',
            'screenshots.required'    => 'Please upload at least 1 screenshot of your website statistics.',
            'screenshots.min'         => 'Please upload at least 1 screenshot.',
            'screenshots.max'         => 'You can upload a maximum of 4 screenshots.',
            'screenshots.*.image'     => 'Each file must be an image.',
            'screenshots.*.mimes'     => 'Screenshots must be JPG, PNG, or WebP.',
            'screenshots.*.max'       => 'Each screenshot must be under 5MB.',
        ]);

        // Store screenshots
        $paths = [];
        foreach ($request->file('screenshots', []) as $file) {
            $path = $file->store('stat-screenshots', 'public');
            $paths[] = $path;
        }

        $user = User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'password'         => Hash::make($data['password']),
            'role'             => 'publisher',
            'status'           => 'pending',
            'website'          => $data['website'],
            'phone'            => $data['phone'] ?? null,
            'telegram'         => $data['telegram'] ?? null,
            'stat_screenshots' => $paths,
        ]);

        PublisherProfile::create(['user_id' => $user->id]);
        ClickDivider::create(['user_id' => $user->id, 'divider_value' => 1, 'is_enabled' => false]);

        Auth::login($user);

        return redirect()->route('publisher.dashboard')->with('registered', true);
    }
}
