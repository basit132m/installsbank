<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckManagerPermission
{
    /**
     * Allow the request only if the user has the given manager permission.
     * Admins always pass (User::hasPermission returns true for admins).
     */
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $user = $request->user();

        abort_unless($user && $user->hasPermission($permission), 403, 'You do not have permission to access this section.');

        return $next($request);
    }
}
