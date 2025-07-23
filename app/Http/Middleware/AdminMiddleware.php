<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // not logged in → login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // only role == 1 is allowed
        if (Auth::user()->role !== 1) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
