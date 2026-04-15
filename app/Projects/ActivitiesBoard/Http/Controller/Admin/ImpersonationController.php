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
#[Middleware(['auth.tenant', 'auth.system_user'])]
class ImpersonationController extends Controller
{
    public function __construct(
        private readonly AlertManager $alertManager
    ) {
    }

    #[Route('{id}/impersonate', methods: ['POST'], name: 'impersonate')]
    public function impersonate(int $id, Request $request): RedirectResponse
    {
        $actor = Auth::guard('web')->user();

        abort_unless($actor->canImpersonate(), 403);

        $target = User::findOrFail($id);

        abort_unless($target->canBeImpersonated(), 403);

        if ($target->id === $actor->id) {
            $this->alertManager->error('No puedes suplantarte a ti mismo.');
            return redirect()->route(Routes::UserList->value);
        }

        session([
            'system_impersonator_id' => $actor->id,
        ]);
        Auth::guard('web')->loginUsingId($target->id);
        $request->session()
            ->regenerate();

        $this->alertManager->warning(
            "Ahora estás actuando como {$target->name} dentro del tenant. Usa el banner superior para salir.",
            'Suplantación interna activa'
        );

        return redirect()->route(Routes::UserList->value);
    }
}
