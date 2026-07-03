<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireResellerApproved
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if ($user && $user->role === 'reseller' && $user->status !== 'active') {
            return redirect()->route('reseller.dashboard')
                ->with('error', 'Your account is pending approval. This section unlocks once an admin approves your account.');
        }

        return $next($request);
    }
}
