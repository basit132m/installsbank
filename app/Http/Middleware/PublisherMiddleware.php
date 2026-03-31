<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PublisherMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!auth()->check() || auth()->user()->role !== 'publisher') {
            return redirect()->route('login');
        }
        if (auth()->user()->status === 'suspended') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended.');
        }
        return $next($request);
    }
}
