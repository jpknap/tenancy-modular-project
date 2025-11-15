<?php

namespace App\Projects\SportCompetition\Repositories;

use App\Common\Repository\BaseRepository;
use App\Projects\SportCompetition\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy('email', $email);
    }

    public function getActiveUsers(): Collection
    {
        return $this->model->where('active', true)
            ->get();
    }

    protected function model(): string
    {
        return User::class;
    }
}
