<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $profile = $user->advertiserProfile;
        return view('advertiser.profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'website'      => 'nullable|url',
            'telegram'     => 'required|string|max:100',
            'whatsapp'     => 'required|string|max:30',
            'phone'        => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'     => $data['name'],
            'phone'    => $data['phone'] ?? null,
            'telegram' => $data['telegram'],
            'website'  => $data['website'] ?? null,
        ]);

        $user->advertiserProfile->update([
            'company_name' => $data['company_name'] ?? null,
            'website'      => $data['website'] ?? null,
            'telegram'     => $data['telegram'],
            'whatsapp'     => $data['whatsapp'],
        ]);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();
        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);
        return back()->with('success', 'Password updated.');
    }
}
