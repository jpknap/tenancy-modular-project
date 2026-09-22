<?php

// Rutas de Tenants (subdominios de tenant)
use App\Common\Http\Controller\LocaleSwitchController;
use App\Http\Middleware\LogTenancyState;
use App\Http\Middleware\ProjectInitialized;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use App\Projects\SportCompetition\SportCompetitionProject;
use App\Projects\TicketsValdi\TicketsValdiProject;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Acceso como system_user desde landlord
Route::middleware([
    'web',
    LogTenancyState::class,
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])
    ->get('/system-login', [\App\Common\Http\Controller\SystemLoginController::class, 'login'])
    ->name('tenant.system-login');

// Cambio de idioma de sesión (disponible en subdominios tenant)
Route::middleware(['web', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class])
    ->post('/locale/switch', [LocaleSwitchController::class, 'switch'])
    ->name('tenant.locale.switch');

$allTenantEndpoints = [
    ...SportCompetitionProject::getEndpoints(),
    ...ActivitiesBoardProject::getEndpoints(),
    ...TicketsValdiProject::getEndpoints(),
];

// Igual que en web.php: separamos los endpoints 'api/*' para que no pasen
// por el middleware 'web' (sesión/cookies/CSRF) y la API de auth sea
// stateless también en subdominios de tenant.
$isApiEndpoint = fn ($endpoint) => str_contains($endpoint->path, '/api/');
$webTenantEndpoints = array_values(array_filter($allTenantEndpoints, fn ($endpoint) => ! $isApiEndpoint($endpoint)));
$apiTenantEndpoints = array_values(array_filter($allTenantEndpoints, $isApiEndpoint));

$registerTenantEndpoints = function (array $endpoints): void {
    foreach ($endpoints as $endpoint) {
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
};

Route::middleware([
    'web',
    LogTenancyState::class,
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    ProjectInitialized::class,
    'cache.user.roles',
])->group(function () use ($registerTenantEndpoints, $webTenantEndpoints) {
    $registerTenantEndpoints($webTenantEndpoints);
});

// Grupo API stateless para tenants: InitializeTenancyByDomain va PRIMERO
// (resuelve la conexión a la DB del tenant antes de que auth:sanctum, ya
// aplicado por-endpoint, busque el token) y sin middleware 'web', para no
// arrancar sesión/cookies/CSRF.
Route::middleware([
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    ProjectInitialized::class,
])->group(function () use ($registerTenantEndpoints, $apiTenantEndpoints) {
    $registerTenantEndpoints($apiTenantEndpoints);
});
