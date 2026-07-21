<?php

// Grupo de rutas API stateless para dominios centrales (Landlord y
// SportCompetition) y ActivitiesBoard cuando corre en dominio central.
//
// Se registra desde bootstrap/app.php vía el callback `then` de
// withRouting(), FUERA del Route::middleware('web')->group(...) con el que
// el framework envuelve automáticamente routes/web.php. Ese envolvimiento
// es incondicional (ver ApplicationBuilder::buildRoutingCallback), por lo
// que ningún grupo anidado dentro de web.php puede evitar sesión, cookies
// o CSRF; por eso este archivo vive aparte.
//
// auth:sanctum se aplica por-endpoint vía #[Middleware] en cada controller
// (login es público, logout/me lo exigen), no acá.

use App\Http\Middleware\EnsureIsCentralDomain;
use App\Http\Middleware\ProjectInitialized;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use App\Projects\Landlord\LandlordProject;
use App\Projects\SportCompetition\SportCompetitionProject;
use Illuminate\Support\Facades\Route;

$allEndpoints = [
    ...LandlordProject::getEndpoints(),
    ...SportCompetitionProject::getEndpoints(),
    ...ActivitiesBoardProject::getEndpoints(),
];

$apiEndpoints = array_values(array_filter(
    $allEndpoints,
    fn ($endpoint) => str_contains($endpoint->path, '/api/')
));

Route::middleware([
    EnsureIsCentralDomain::class,
    ProjectInitialized::class,
])->group(function () use ($apiEndpoints) {
    foreach ($apiEndpoints as $endpoint) {
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
