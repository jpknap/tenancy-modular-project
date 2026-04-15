<?php

namespace App\Projects\ActivitiesBoard\Http\Controller\Admin;

use App\Attributes\Middleware;
use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Common\Services\AlertManager;
use App\Projects\ActivitiesBoard\Enums\Routes;
use App\Projects\ActivitiesBoard\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

#[RoutePrefix('admin/users')]
#[Middleware(['auth.tenant'])]
class StopImpersonationController extends Controller
{
    public function __construct(
        private readonly AlertManager $alertManager
    ) {
    }

    #[Route('stop-impersonation', methods: ['POST'], name: 'stop-impersonation')]
    public function stopImpersonation(Request $request): RedirectResponse
    {
        $impersonatorId = session('system_impersonator_id');

        if (! $impersonatorId) {
            return redirect()->route(Routes::UserList->value);
        }

        $impersonator = User::find((int) $impersonatorId);

        abort_if($impersonator === null || ! $impersonator->is_system_user, 403);

        session()
            ->forget('system_impersonator_id');
        Auth::guard('web')->loginUsingId($impersonator->id);
        $request->session()
            ->regenerate();

        $this->alertManager->success('Has vuelto a tu sesión original.', 'Suplantación detenida');

        return redirect()->route(Routes::UserList->value);
    }
}
