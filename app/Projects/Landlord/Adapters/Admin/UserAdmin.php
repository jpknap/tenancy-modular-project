<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Models\User;
use App\Projects\Landlord\Http\Controller\Admin\UserAdminController;
use App\Projects\Landlord\Repositories\UserRepository;
use App\Projects\Landlord\Requests\UserFormRequest;
use App\Projects\Landlord\Services\Model\UserService;

class UserAdmin extends AdminBaseAdapter
{
    protected static string $controller = UserAdminController::class;

    protected static string $model = User::class;

    protected string $routePrefix = 'user';

    public function getFormRequest(): string
    {
        return UserFormRequest::class;
    }

    public function getService(): string
    {
        return UserService::class;
    }

    public function getTitle(): string
    {
        return 'Usuarios';
    }

    public function getListableAttributes(): array
    {
        return ['id', 'name', 'email', 'created_at'];
    }
}
