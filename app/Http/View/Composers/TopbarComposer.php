<?php

namespace App\Http\View\Composers;

use App\ProjectManager;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use App\Projects\ActivitiesBoard\Enums\Routes as ActivitiesBoardRoutes;
use App\Projects\Landlord\Enums\Routes as LandlordRoutes;
use App\Projects\Landlord\LandlordProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TopbarComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();

        if (! $user) {
            $user = (object) [
                'id' => 1,
                'name' => 'Usuario Demo',
                'email' => 'demo@example.com',
            ];
        }

        $project = ProjectManager::getCurrentProject();

        $profileRoute = match(true) {
            $project instanceof LandlordProject      => LandlordRoutes::ProfileEdit->value,
            $project instanceof ActivitiesBoardProject => ActivitiesBoardRoutes::ProfileEdit->value,
            default                                  => null,
        };

        $topbarData = [
            'title'         => '',
            'user'          => $user,
            'notifications' => $this->getNotifications(),
            'logoutUrl'     => $project ? '/' . $project->getPrefix() . '/auth/logout' : null,
            'profileUrl'    => $profileRoute ? route($profileRoute) : null,
        ];

        $view->with('topbarData', $topbarData);
    }

    private function getNotifications(): array
    {
        // Notificaciones de prueba
        return [
            'Nueva actualización disponible',
            'Tienes 3 mensajes sin leer',
            'Recordatorio: Reunión a las 15:00',
        ];
    }
}
