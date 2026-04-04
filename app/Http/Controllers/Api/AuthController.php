<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 401);
        }

        if (!$user->isPublisher()) {
            return response()->json(['message' => 'This app is for publishers only.'], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->userPayload($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out.']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink($request->only('email'));
        return response()->json(['message' => 'If that email exists, a reset link has been sent.']);
    }

    public function updateDeviceToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);
        $request->user()->update(['fcm_token' => $request->fcm_token]);
        return response()->json(['message' => 'Device token updated.']);
    }

    private function userPayload(User $user): array
    {
        $profile = $user->publisherProfile;
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'phone'      => $user->phone,
            'website'    => $user->website,
            'status'     => $user->status,
            'avatar_url' => $user->avatar ? url('/avatars/' . $user->avatar) : null,
            'created_at' => $user->created_at->toDateString(),
            'payment_enabled'  => (bool) ($profile?->payment_enabled ?? false),
            'show_earnings'    => $profile ? ($profile->payment_enabled && !$profile->isFixedRate()) : false,
        ];
    }
}
