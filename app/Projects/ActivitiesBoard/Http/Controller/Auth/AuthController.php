<?php

namespace App\Projects\ActivitiesBoard\Http\Controller\Auth;

use App\Common\Http\Controller\Auth\BaseAuthController;
use App\Projects\ActivitiesBoard\Enums\Routes;

class AuthController extends BaseAuthController
{
    protected function guard(): string
    {
        return 'web';
    }

    protected function loginView(): string
    {
        return 'activities-board.auth.login';
    }

    protected function defaultRedirectRoute(): string
    {
        return Routes::UserList->value;
    }

    protected function loginRoute(): string
    {
        return Routes::Login->value;
    }
}
