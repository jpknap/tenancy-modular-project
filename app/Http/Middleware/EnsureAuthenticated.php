<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next, string $guard = 'web'): mixed
    {
        if (! Auth::guard($guard)->check()) {
            return redirect($this->loginUrl($request, $guard));
        }

        return $next($request);
    }

    private function loginUrl(Request $request, string $guard): string
    {
        // Deriva la URL de login a partir del primer segmento de la ruta actual.
        // e.g. /landlord/admin/tenant/list → /landlord/auth/login
        $prefix = $request->segment(1);

        return $prefix ? "/{$prefix}/auth/login" : '/auth/login';
    }
}
