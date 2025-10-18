<?php

use App\Projects\Landlord\Adapters\Admin\TenantAdmin;
use App\Projects\Landlord\Adapters\Admin\UserAdmin;
use App\Projects\Landlord\Http\Controller\Admin\TenantAdminController;
use App\Projects\Landlord\Http\Controller\Admin\UserAdminController;
use App\Projects\Landlord\Http\Controller\User\AuthController;

return [
    'landlord' => [
        'admins' => [UserAdmin::class, TenantAdmin::class],
        'controllers' => [
            AuthController::class,
        ],
    ],
];
