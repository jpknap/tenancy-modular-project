<?php

namespace App\Adapters\Admin;

use App\Http\Controllers\Admin\UserAdminController;
use App\Models\User;
use App\Module\Admin\Adapter\AdminBaseAdapter;

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
