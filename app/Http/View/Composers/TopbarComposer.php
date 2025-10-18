<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TopbarComposer
{
    public function compose(View $view): void
    {
        // Aquí se pueden inyectar datos dinámicos del topbar
        // Por ejemplo: información del usuario, notificaciones, etc.

        // Obtener usuario autenticado o crear uno de prueba
        $user = Auth::user();

        // Si no hay usuario autenticado, crear uno de prueba para desarrollo
        if (! $user) {
            $user = (object) [
                'id' => 1,
                'name' => 'Usuario Demo',
                'email' => 'demo@example.com',
            ];
        }

        $topbarData = [
            'title' => '',
            'user' => $user,
            'notifications' => $this->getNotifications(),
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
