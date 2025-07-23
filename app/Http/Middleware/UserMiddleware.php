<?php
// app/Http/Middleware/UserMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // not logged in → login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // only role == 0 is allowed
        if (Auth::user()->role !== 0) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
