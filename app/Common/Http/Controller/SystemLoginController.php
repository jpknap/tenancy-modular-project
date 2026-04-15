<?php

namespace App\Common\Http\Controller;

use App\Models\User;
use App\ProjectManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemLoginController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        Log::debug('[Log-System-Auth] SystemLoginController::login() iniciado', [
            'path' => $request->path(),
            'host' => $request->getHost(),
            'uid_param' => $request->query('uid'),
        ]);

        $uid = $request->query('uid');
        $exp = $request->query('exp');
        $sig = $request->query('sig');

        Log::debug('[Log-System-Auth] Parámetros recibidos', [
            'uid' => $uid,
            'exp' => $exp,
            'sig' => $sig ? 'presente' : 'ausente',
        ]);

        abort_if((int) $exp <= now()->timestamp, 403);

        $payload = "{$uid}:{$exp}";
        $expected = hash_hmac('sha256', $payload, config('app.key'));

        abort_unless(hash_equals($expected, (string) $sig), 403);

        Log::debug('[Log-System-Auth] Validación de firma exitosa');

        // Token one-time use via store 'database' — el driver file no soporta tags
        // que CacheTenancyBootstrapper requiere. La tabla cache existe en cada tenant
        // gracias a las migraciones Common.
        $tokenKey = 'system_login_used:' . hash('sha256', $sig);
        $cache = Cache::store('database');
        abort_if($cache->has($tokenKey), 403);

        $user = User::find((int) $uid);

        Log::debug('[Log-System-Auth] Usuario encontrado', [
            'user_id' => $user?->id,
            'is_system_user' => $user?->is_system_user,
            'email' => $user?->email,
        ]);

        abort_if($user === null || ! $user->is_system_user, 403);

        // Marcar token como usado (TTL = tiempo restante hasta expiración)
        $ttl = max(1, (int) $exp - now()->timestamp);
        $cache->put($tokenKey, true, $ttl);

        Log::debug('[Log-System-Auth] Autenticando usuario en guard web', [
            'user_id' => $uid,
        ]);

        Auth::guard('web')->loginUsingId((int) $uid);

        Log::debug('[Log-System-Auth] Verificando autenticación post-login', [
            'is_authenticated' => Auth::guard('web')->check(),
            'auth_user_id' => Auth::guard('web')->id(),
            'session_id' => session()->getId(),
        ]);

        $project = ProjectManager::getCurrentProject();

        Log::debug('[Log-System-Auth] Proyecto actual', [
            'project_class' => $project ? get_class($project) : 'null',
            'project_prefix' => $project ? $project::getPrefix() : 'null',
        ]);

        $redirectUrl = '/';

        if ($project !== null) {
            $prefix = $project::getPrefix();
            $routeName = "{$prefix}.admin.users.list";

            Log::debug('[Log-System-Auth] Buscando ruta de destino', [
                'route_name' => $routeName,
                'route_exists' => app('router')->has($routeName),
            ]);

            if (app('router')->has($routeName)) {
                $redirectUrl = route($routeName);
            }
        }

        Log::debug('[Log-System-Auth] Redirigiendo a', [
            'redirect_url' => $redirectUrl,
        ]);

        return redirect($redirectUrl);
    }
}
