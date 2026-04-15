<?php

namespace App\Common\Http\Controller;

use App\Models\User;
use App\ProjectManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SystemLoginController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $uid = $request->query('uid');
        $exp = $request->query('exp');
        $sig = $request->query('sig');

        abort_if((int) $exp <= now()->timestamp, 403);

        $payload = "{$uid}:{$exp}";
        $expected = hash_hmac('sha256', $payload, config('app.key'));

        abort_unless(hash_equals($expected, (string) $sig), 403);

        // Token one-time use: rechazar si ya fue consumido
        $tokenKey = 'system_login_used:' . hash('sha256', $sig);
        abort_if(Cache::has($tokenKey), 403);

        $user = User::find((int) $uid);

        abort_if($user === null || ! $user->is_system_user, 403);

        // Marcar token como usado (TTL = tiempo restante hasta expiración)
        $ttl = max(1, (int) $exp - now()->timestamp);
        Cache::put($tokenKey, true, $ttl);

        Auth::guard('web')->loginUsingId((int) $uid);

        // Regenerar sesión para prevenir session fixation en el tenant
        $request->session()
            ->regenerate();

        $project = ProjectManager::getCurrentProject();
        $redirectUrl = '/';

        if ($project !== null) {
            $prefix = $project::getPrefix();
            $routeName = "{$prefix}.admin.users.list";

            if (app('router')->has($routeName)) {
                $redirectUrl = route($routeName);
            }
        }

        return redirect($redirectUrl);
    }
}
