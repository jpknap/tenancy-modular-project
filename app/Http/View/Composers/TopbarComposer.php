<?php

namespace App\Http\View\Composers;

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
