<?php

namespace App\Projects\Landlord\Repositories;

use App\Common\Repository\BaseRepository;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repositorio para el modelo Tenant
 */
class TenantRepository extends BaseRepository
{
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function model(): string
    {
        return Tenant::class;
    }
}
