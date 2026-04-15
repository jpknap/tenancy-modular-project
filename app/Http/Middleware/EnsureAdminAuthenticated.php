<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): mixed
    {
        // En contexto de tenancia: verificar con guard 'web'
        // Sin tenancia (Landlord): verificar con guard 'landlord'
        $guard = tenancy()->initialized ? 'web' : 'landlord';

        Log::debug('[Log-System-Auth] EnsureAdminAuthenticated::handle()', [
            'path' => $request->path(),
            'host' => $request->getHost(),
            'tenancy_initialized' => tenancy()->initialized,
            'selected_guard' => $guard,
            'is_authenticated' => Auth::guard($guard)->check(),
            'auth_user_id' => Auth::guard($guard)->id(),
        ]);

        if (! Auth::guard($guard)->check()) {
            $loginUrl = $this->loginUrl($request);
            Log::warning('[Log-System-Auth] Usuario no autenticado, redirigiendo a login', [
                'path' => $request->path(),
                'guard' => $guard,
                'login_url' => $loginUrl,
            ]);
            return redirect($loginUrl);
        }

        return $next($request);
    }

    private function loginUrl(Request $request): string
    {
        // Deriva la URL de login a partir del primer segmento de la ruta actual.
        // e.g. /landlord/admin/tenant/list → /landlord/auth/login
        $prefix = $request->segment(1);

        return $prefix ? "/{$prefix}/auth/login" : '/auth/login';
    }
}
