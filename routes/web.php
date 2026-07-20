<?php

use App\Common\Http\Controller\LocaleSwitchController;
use App\Http\Middleware\EnsureIsCentralDomain;
use App\Http\Middleware\ProjectInitialized;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use App\Projects\Landlord\LandlordProject;
use App\Projects\SportCompetition\SportCompetitionProject;
use Illuminate\Support\Facades\Route;

// Cambio de idioma de sesión (disponible en dominio central)
Route::middleware(['web', EnsureIsCentralDomain::class])
    ->post('/locale/switch', [LocaleSwitchController::class, 'switch'])
    ->name('locale.switch');

$allEndpoints = [
    ...LandlordProject::getEndpoints(),
    ...SportCompetitionProject::getEndpoints(),
    ...ActivitiesBoardProject::getEndpoints()
];

// Los endpoints 'api/*' (ver feature rest-api) se registran aparte, sin el
// middleware 'web' (arranca sesión/cookies/CSRF), para que la API de auth
// sea stateless. El resto de las rutas sigue igual que antes.
$isApiEndpoint = fn ($endpoint) => str_contains($endpoint->path, '/api/');
$webEndpoints = array_values(array_filter($allEndpoints, fn ($endpoint) => ! $isApiEndpoint($endpoint)));
$apiEndpoints = array_values(array_filter($allEndpoints, $isApiEndpoint));

$registerEndpoints = function (array $endpoints): void {
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

// Rutas del Landlord y SportCompetition (dominios centrales)
Route::middleware([
    'web',
    EnsureIsCentralDomain::class,
    ProjectInitialized::class,
])->group(function () use ($registerEndpoints, $webEndpoints) {
    $registerEndpoints($webEndpoints);
});

// Grupo API stateless (dominio central): sin middleware 'web', por lo tanto
// sin sesión/cookies/CSRF. auth:sanctum se aplica por-endpoint (login es
// público, logout/me lo exigen vía #[Middleware] en el controller).
Route::middleware([
    EnsureIsCentralDomain::class,
    ProjectInitialized::class,
])->group(function () use ($registerEndpoints, $apiEndpoints) {
    $registerEndpoints($apiEndpoints);
});

