<?php

use App\Http\Middleware\ProjectInitialized;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    ProjectInitialized::class,
])->group(function () {

});
