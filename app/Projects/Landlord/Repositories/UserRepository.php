<?php

namespace App\Projects\Landlord\Repositories;

use App\Common\Repository\BaseRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repositorio para el modelo User
 */
class UserRepository extends BaseRepository
{
    /**
     * Métodos específicos de User
     */
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
