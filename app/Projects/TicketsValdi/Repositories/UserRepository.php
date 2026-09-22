<?php

namespace App\Projects\TicketsValdi\Repositories;

use App\Common\Repository\BaseRepository;
use App\Projects\TicketsValdi\Models\User;

class UserRepository extends BaseRepository
{
    protected function model(): string
    {
        return User::class;
    }
}
