<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return response()->json($this->userPayload($user));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|max:30',
            'website' => 'nullable|url|max:200',
        ]);
        $user->update($data);
        return response()->json(['message' => 'Profile updated.', 'user' => $this->userPayload($user->fresh())]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update(['password' => bcrypt($request->password)]);
        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);

        $user = auth()->user();

        if ($user->avatar) {
            $old = public_path('avatars/' . $user->avatar);
            if (file_exists($old)) unlink($old);
        }

        $file     = $request->file('avatar');
        $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('avatars'), $filename);
        $user->update(['avatar' => $filename]);

        return response()->json([
            'message'    => 'Avatar updated.',
            'avatar_url' => url('/avatars/' . $filename),
        ]);
    }

    private function userPayload($user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'phone'      => $user->phone,
            'website'    => $user->website,
            'status'     => $user->status,
            'avatar_url' => $user->avatar ? url('/avatars/' . $user->avatar) : null,
            'created_at' => $user->created_at->toDateString(),
        ];
    }
}
