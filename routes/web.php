<?php

use App\Common\Http\Controller\LocaleSwitchController;
use App\Http\Middleware\EnsureIsCentralDomain;
use App\Http\Middleware\ProjectInitialized;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use App\Projects\Landlord\LandlordProject;
use App\Projects\SportCompetition\SportCompetitionProject;
use App\Projects\TicketsValdi\TicketsValdiProject;
use Illuminate\Support\Facades\Route;

// Cambio de idioma de sesión (disponible en dominio central)
Route::middleware(['web', EnsureIsCentralDomain::class])
    ->post('/locale/switch', [LocaleSwitchController::class, 'switch'])
    ->name('locale.switch');

$allEndpoints = [
    ...LandlordProject::getEndpoints(),
    ...SportCompetitionProject::getEndpoints(),
    ...ActivitiesBoardProject::getEndpoints(),
    ...TicketsValdiProject::getEndpoints(),
];

// Los endpoints 'api/*' (ver feature rest-api) NO se registran acá: este
// archivo lo envuelve el framework en el middleware 'web' de forma
// automática e ineludible (ver ApplicationBuilder::withRouting -> siempre
// hace Route::middleware('web')->group($web)), así que ningún grupo interno
// anidado puede evitar sesión/cookies/CSRF. Se registran, sin ese problema,
// en routes/api-auth.php vía el callback `then` de withRouting() en
// bootstrap/app.php.
$isApiEndpoint = fn ($endpoint) => str_contains($endpoint->path, '/api/');
$webEndpoints = array_values(array_filter($allEndpoints, fn ($endpoint) => ! $isApiEndpoint($endpoint)));

// Rutas del Landlord y SportCompetition (dominios centrales)
Route::middleware([
    'web',
    EnsureIsCentralDomain::class,
    ProjectInitialized::class,
])->group(function () use ($webEndpoints) {
    foreach ($webEndpoints as $endpoint) {
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
