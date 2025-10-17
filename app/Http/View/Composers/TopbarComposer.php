<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TopbarComposer
{
    /**
     * Vincula datos al partial del topbar
     */
    public function compose(View $view): void
    {
        // Aquí se pueden inyectar datos dinámicos del topbar
        // Por ejemplo: información del usuario, notificaciones, etc.

        $topbarData = [
            'title' => 'Barra superior',
            'user' => Auth::user(),
            'notifications' => $this->getNotifications(),
        ];

        $view->with('topbarData', $topbarData);
    }

    /**
     * Obtiene las notificaciones (ejemplo)
     */
    private function getNotifications(): array
    {
        return [
            // Aquí se pueden cargar notificaciones reales
        ];
    }
}
