<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequirePublisherApproved
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth()->user();

        if ($user && $user->role === 'publisher' && $user->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account is pending approval.'], 403);
            }
            return redirect()->route('publisher.dashboard')
                ->with('error', 'This section is only available after your account is approved.');
        }

        return $next($request);
    }
}
