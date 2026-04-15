<?php

namespace App\Projects\Landlord\Repositories;

use App\Common\Repository\BaseRepository;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantRepository extends BaseRepository
{
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->with('domains')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('domains')
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return $this->model->with('domains')
            ->get();
    }

    public function find(int $id): ?Model
    {
        return $this->model->with('domains')
            ->find($id);
    }

    protected function model(): string
    {
        return Tenant::class;
    }
}
