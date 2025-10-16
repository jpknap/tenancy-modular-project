<?php

namespace App\Module\Admin\Services;

use App\Module\Admin\Contracts\PageAdminInterface;
use Illuminate\Support\Facades\Route;

class RouterService
{
    public static function initRouterAdmin(): void
    {
        /** @var array<class-string<PageAdminInterface>> $admins */
        $admins = config('admin');

        foreach ($admins as $admin) {
            $admin = new $admin();
            $resource = $admin->getRoutePrefix();
            $controller = $admin->getController();

            Route::prefix("admin/{$resource}")
                ->name("admin.{$resource}.")
                ->controller($controller)
                ->group(function () {
                    Route::get('list', 'list')->name('list');
                });
        }
    }
}
