<?php

use App\Projects\ActivitiesBoard\Http\Controller\Auth\AuthController as AuthControllerAct;
use App\Projects\ActivitiesBoard\Adapters\Admin\ActivityAdmin;
use App\Projects\ActivitiesBoard\Adapters\Admin\UserAdmin as ActivitiesBoardUserAdmin;
use App\Projects\Landlord\Adapters\Admin\TenantAdmin;
use App\Projects\Landlord\Adapters\Admin\UserAdmin;
use App\Projects\Landlord\Http\Controller\Auth\AuthController;
use App\Projects\Landlord\Http\Controller\ProfileController as LandlordProfileController;
use App\Projects\ActivitiesBoard\Http\Controller\ProfileController as ActivitiesBoardProfileController;
use App\Projects\SportCompetition\Adapters\Admin\UserAdmin as SportCompetitionUserAdmin;
use App\Projects\SportCompetition\Http\Controller\Auth\AuthController as SportCompetitionAuthController;
use App\Projects\SportCompetition\Http\Controller\Admin\ImpersonationController as SportCompetitionImpersonationController;
use App\Projects\Landlord\Http\Controller\Admin\ImpersonationController as LandlordImpersonationController;
use App\Projects\Landlord\Http\Controller\Admin\TenantAccessController as LandlordTenantAccessController;

return [
    'landlord' => [
        'admins' => [UserAdmin::class, TenantAdmin::class],
        'controllers' => [AuthController::class, LandlordProfileController::class, LandlordImpersonationController::class, LandlordTenantAccessController::class],
    ],
    'sport-competition' => [
        'admins' => [SportCompetitionUserAdmin::class],
        'controllers' => [SportCompetitionAuthController::class, SportCompetitionImpersonationController::class],
    ],
    'activities-board' => [
        'admins' => [ActivityAdmin::class, ActivitiesBoardUserAdmin::class],
        'controllers' => [AuthControllerAct::class, ActivitiesBoardProfileController::class],
    ],
];
