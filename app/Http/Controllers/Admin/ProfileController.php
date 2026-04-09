<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return view('admin.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
        ]);

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withFragment('password');
        }

        $user->update(['password' => bcrypt($request->password)]);

        return back()->with('success', 'Password changed successfully.')->withFragment('password');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            $oldPath = public_path('avatars/' . $user->avatar);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file     = $request->file('avatar');
        $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('avatars'), $filename);

        $user->update(['avatar' => $filename]);

        return back()->with('success', 'Avatar updated.');
    }

    public function removeAvatar()
    {
        $user = auth()->user();

        if ($user->avatar) {
            $path = public_path('avatars/' . $user->avatar);
            if (file_exists($path)) {
                unlink($path);
            }
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Avatar removed.');
    }
}
