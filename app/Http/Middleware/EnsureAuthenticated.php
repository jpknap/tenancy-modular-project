<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next, string $guard = 'web'): mixed
    {
        Log::debug('[Log-System-Auth] EnsureAuthenticated::handle()', [
            'path' => $request->path(),
            'host' => $request->getHost(),
            'guard' => $guard,
            'is_authenticated' => Auth::guard($guard)->check(),
            'auth_user_id' => Auth::guard($guard)->id(),
            'session_id' => session()->getId(),
            'auth_all_guards' => [
                'web_check' => Auth::guard('web')->check(),
                'web_id' => Auth::guard('web')->id(),
            ],
        ]);

        if (! Auth::guard($guard)->check()) {
            $loginUrl = $this->loginUrl($request, $guard);
            Log::warning('[Log-System-Auth] Usuario no autenticado, redirigiendo a login', [
                'path' => $request->path(),
                'guard' => $guard,
                'login_url' => $loginUrl,
            ]);
            return redirect($loginUrl);
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
