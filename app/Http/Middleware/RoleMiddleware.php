<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied.'], 403);
            }
            abort(403, 'Access denied.');
        }

        if ($user->status === 'suspended') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account has been suspended. Please contact support.'], 403);
            }
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
