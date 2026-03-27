<?php

namespace App\Projects\ActivitiesBoard\Http\Controller\Auth;

use App\Common\Http\Controller\Auth\BaseAuthController;

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

    protected function defaultRedirect(): string
    {
        return '/activities-board/admin/users/list';
    }
}
