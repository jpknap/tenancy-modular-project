<?php

namespace App\Projects\SportCompetition\Http\Controller\Auth;

use App\Common\Http\Controller\Auth\BaseAuthController;
use App\Projects\SportCompetition\Enums\Routes;

class AuthController extends BaseAuthController
{
    protected function guard(): string
    {
        return 'web';
    }

    protected function loginView(): string
    {
        return 'sport-competition.auth.login';
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
