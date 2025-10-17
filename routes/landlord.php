<?php

use App\Http\Controllers\LandlordAuthController;
use App\Http\Middleware\ProjectInitialized;
use App\Module\Admin\Services\RouterService;

Route::prefix('landlord')->middleware(['web', ProjectInitialized::class])->group(function () {
    Route::get('login', [LandlordAuthController::class, 'showLogin'])->name('landlord.login');
    Route::post('login', [LandlordAuthController::class, 'login']);
    Route::post('logout', [LandlordAuthController::class, 'logout'])->name('landlord.logout');

    Route::middleware('auth:web')->group(function () {
        Route::get('dashboard', function () {
            return view('landlord.list');
        })->name('landlord.dashboard');

        RouterService::initRouterAdmin();
    });
});
