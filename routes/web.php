<?php

use App\Http\Middleware\ProjectInitialized;
use App\Projects\Landlord\LandlordProject;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

Route::middleware([
    'web',
    //InitializeTenancyByDomain::class,
    ProjectInitialized::class,
])->group(function () {

    $routes = LandlordProject::getEndpoints();
    foreach ($routes as $endpoint) {
        $httpMethod = $endpoint->getPrimaryHttpMethod();

        $route = Route::$httpMethod($endpoint->path, [$endpoint->controller, $endpoint->method]);

        if ($endpoint->name) {
            $route->name($endpoint->name);
        }

        if (!empty($endpoint->middleware)) {
            $route->middleware($endpoint->middleware);
        }

        if (!empty($endpoint->where)) {
            $route->where($endpoint->where);
        }
    }
});
