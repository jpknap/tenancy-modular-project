<?php

namespace App\Common\Http\Controller;

use App\Models\User;
use App\ProjectManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

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

        $user = User::find((int) $uid);

        abort_if($user === null || ! $user->is_system_user, 403);

        Auth::guard('web')->loginUsingId((int) $uid);

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
