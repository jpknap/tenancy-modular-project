<?php

use App\Http\Controllers\LandlordAuthController;

Route::prefix('landlord')->group(function () {
    Route::get('login', [LandlordAuthController::class, 'showLogin'])->name('landlord.login');
    Route::post('login', [LandlordAuthController::class, 'login']);
    Route::post('logout', [LandlordAuthController::class, 'logout'])->name('landlord.logout');

    Route::middleware('auth:web')->group(function () {
        Route::get('dashboard', function () {
            return view('landlord.dashboard');
        })->name('landlord.dashboard');
    });
});
