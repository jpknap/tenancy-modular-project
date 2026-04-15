<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.landlord'           => \App\Http\Middleware\EnsureAuthenticated::class . ':landlord',
            'auth.web'                => \App\Http\Middleware\EnsureAuthenticated::class . ':web',
            'auth.tenant'             => \App\Http\Middleware\EnsureAuthenticated::class . ':web',
            'auth.admin'              => \App\Http\Middleware\EnsureAdminAuthenticated::class,
            'role'                    => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'              => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission'      => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'auth.system_user'        => \App\Http\Middleware\EnsureIsSystemUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
