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

        $topbarData = [
            'title' => '',
            'user' => Auth::user(),
            'notifications' => $this->getNotifications(),
        ];

        $view->with('topbarData', $topbarData);
    }
    private function getNotifications(): array
    {
        return [
        ];
    }
}
