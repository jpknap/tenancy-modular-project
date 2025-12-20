<?php

// Rutas de Tenants (subdominios de tenant)
use App\Http\Middleware\ProjectInitialized;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    ProjectInitialized::class,
])->group(function () {
    $routes = [
        ...ActivitiesBoardProject::getEndpoints(),
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
