<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Models\User;
use App\Module\Admin\Adapter\AdminBaseAdapter;
use App\Projects\Landlord\Http\Controller\Admin\UserAdminController;

final class UserAdmin extends AdminBaseAdapter
{
    protected string $model = User::class;

    protected string $routePrefix = 'user';

    protected string $controller = UserAdminController::class;

    public function getListableAttributes(): array
    {
        return ['id'];
    }
}
