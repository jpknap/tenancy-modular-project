<?php

namespace App\Projects\Landlord\Http\Controller;

use App\Attributes\Middleware;
use App\Common\Http\Controller\ProfileController as BaseProfileController;
use App\Projects\Landlord\Enums\Routes;

#[Middleware(['auth.landlord'])]
class ProfileController extends BaseProfileController
{
    protected function guard(): string
    {
        return 'landlord';
    }

    protected function profileView(): string
    {
        return 'landlord.profile.edit';
    }

    protected function profileRoute(): string
    {
        return Routes::ProfileEdit->value;
    }
}
