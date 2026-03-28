<?php

namespace App\Projects\ActivitiesBoard\Http\Controller;

use App\Attributes\Middleware;
use App\Common\Http\Controller\ProfileController as BaseProfileController;
use App\Projects\ActivitiesBoard\Enums\Routes;

#[Middleware(['auth.tenant'])]
class ProfileController extends BaseProfileController
{
    protected function guard(): string
    {
        return 'web';
    }

    protected function profileView(): string
    {
        return 'activities-board.profile.edit';
    }

    protected function profileRoute(): string
    {
        return Routes::ProfileEdit->value;
    }
}
