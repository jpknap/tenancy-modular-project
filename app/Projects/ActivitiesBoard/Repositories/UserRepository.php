<?php

namespace App\Projects\ActivitiesBoard\Repositories;

use App\Common\Repository\BaseRepository;
use App\Projects\ActivitiesBoard\Models\User;

class UserRepository extends BaseRepository
{
    protected function model(): string
    {
        return User::class;
    }
}
