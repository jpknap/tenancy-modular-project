<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSystemUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('web')->check() || Auth::guard('web')->user()->is_system_user !== true) {
            abort(403);
        }

        return $next($request);
    }
}
