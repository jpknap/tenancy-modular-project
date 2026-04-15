<?php

use App\Common\Http\Controller\LocaleSwitchController;
use App\Http\Middleware\EnsureIsCentralDomain;
use App\Http\Middleware\ProjectInitialized;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use App\Projects\Landlord\LandlordProject;
use App\Projects\SportCompetition\SportCompetitionProject;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Cambio de idioma de sesión (disponible en dominio central)
Route::middleware(['web', EnsureIsCentralDomain::class])
    ->post('/locale/switch', [LocaleSwitchController::class, 'switch'])
    ->name('locale.switch');

// Rutas del Landlord (dominios centrales: localhost, admin.localhost, etc.)
Route::middleware([
    'web',
    EnsureIsCentralDomain::class,
    ProjectInitialized::class,
])->group(function () {
    $routes = [
        ...LandlordProject::getEndpoints(),
        ...SportCompetitionProject::getEndpoints(),
    ];

    foreach ($routes as $endpoint) {
        $httpMethod = $endpoint->getPrimaryHttpMethod();
        $route = Route::$httpMethod($endpoint->path, [$endpoint->controller, $endpoint->method]);

        if ($endpoint->name) {
            $route->name($endpoint->name);
        }

        if (! empty($endpoint->middleware)) {
            $route->middleware($endpoint->middleware);
        }

        if (! empty($endpoint->where)) {
            $route->where($endpoint->where);
        }
    }
});

