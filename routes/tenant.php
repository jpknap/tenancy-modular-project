<?php

// Rutas de Tenants (subdominios de tenant)
use App\Common\Http\Controller\LocaleSwitchController;
use App\Http\Middleware\ProjectInitialized;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Acceso como system_user desde landlord
Route::middleware(['web', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class])
    ->get('/system-login', [\App\Common\Http\Controller\SystemLoginController::class, 'login'])
    ->name('tenant.system-login');

// Cambio de idioma de sesión (disponible en subdominios tenant)
Route::middleware(['web', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class])
    ->post('/locale/switch', [LocaleSwitchController::class, 'switch'])
    ->name('tenant.locale.switch');

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    ProjectInitialized::class,
])->group(function () {
    $routes = [...ActivitiesBoardProject::getEndpoints()];

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
