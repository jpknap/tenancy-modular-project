<?php

use App\Projects\Landlord\Adapters\Admin\TenantAdmin;
use App\Projects\Landlord\Adapters\Admin\UserAdmin;

return [
    'landlord' => [
        'admins' => [UserAdmin::class, TenantAdmin::class],
    ],
];
