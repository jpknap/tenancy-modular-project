<?php

namespace App\Projects\SportCompetition\Http\Controller\Admin;

use App\Attributes\Middleware;
use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Common\Services\AlertManager;
use App\Projects\SportCompetition\Enums\Routes;
use App\Projects\SportCompetition\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

#[RoutePrefix('admin/users')]
#[Middleware(['auth.landlord'])]
class ImpersonationController extends Controller
{
    public function __construct(private readonly AlertManager $alertManager) {}

    #[Route('{id}/impersonate', methods: ['POST'], name: 'impersonate')]
    public function impersonate(int $id): RedirectResponse
    {
        $actor = auth('landlord')->user();
        abort_unless($actor?->can('users:impersonate'), 403);

        $target = User::findOrFail($id);

        if ($target->id === $actor->id) {
            $this->alertManager->error('No puedes suplantarte a ti mismo.');
            return redirect()->route(Routes::UserList->value);
        }

        session(['impersonator_id' => $actor->id]);
        Auth::guard('landlord')->loginUsingId($target->id);

        $this->alertManager->warning(
            "Ahora estás suplantando a {$target->name}. Usa el banner superior para detener la suplantación.",
            'Modo suplantación activo'
        );

        return redirect()->route(Routes::UserList->value);
    }

    #[Route('stop-impersonation', methods: ['POST'], name: 'stop-impersonation')]
    public function stopImpersonation(): RedirectResponse
    {
        $impersonatorId = session('impersonator_id');

        if (! $impersonatorId) {
            return redirect()->route(Routes::UserList->value);
        }

        Auth::guard('landlord')->loginUsingId($impersonatorId);
        session()->forget('impersonator_id');

        $this->alertManager->success('Has vuelto a tu sesión original.', 'Suplantación detenida');

        return redirect()->route(Routes::UserList->value);
    }
}
