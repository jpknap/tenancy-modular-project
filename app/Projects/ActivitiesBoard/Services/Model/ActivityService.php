<?php

namespace App\Projects\ActivitiesBoard\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\ActivitiesBoard\Models\Activity;
use App\Projects\ActivitiesBoard\Repositories\ActivityRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class ActivityService
{
    public function __construct(
        private ActivityRepository $activityRepository,
        private TransactionService $transactionService
    ) {
    }

    /**
     * Actividades del usuario autenticado (API, sin filtros ni paginación).
     */
    public function allForUser(int $userId): Collection
    {
        return $this->activityRepository->getQueryBuilder()
            ->where('user_id', $userId)
            ->orderBy('position')
            ->get();
    }

    public function findForUser(int $id, int $userId): Activity
    {
        /** @var Activity $activity */
        $activity = $this->activityRepository->getQueryBuilder()
            ->where('user_id', $userId)
            ->with('categories')
            ->findOrFail($id);

        return $activity;
    }

    public function createForUser(array $data, int $userId): Activity
    {
        return $this->transactionService->execute(function () use ($data, $userId) {
            $categoryIds = Arr::pull($data, 'category_ids');

            // user_id no es mass-assignable (no está en $fillable) a propósito,
            // para que nunca pueda llegar desde el payload del cliente.
            /** @var Activity $activity */
            $activity = $this->activityRepository->getModel()
                ->newInstance($data);
            $activity->user_id = $userId;
            $activity->save();

            if ($categoryIds !== null) {
                $activity->categories()
                    ->sync($categoryIds);
            }

            return $activity->load('categories');
        });
    }

    public function updateForUser(int $id, array $data, int $userId): Activity
    {
        return $this->transactionService->execute(function () use ($id, $data, $userId) {
            $categoryIds = Arr::pull($data, 'category_ids');

            $activity = $this->findForUser($id, $userId);
            $activity->update($data);

            if ($categoryIds !== null) {
                $activity->categories()
                    ->sync($categoryIds);
            }

            return $activity->load('categories');
        });
    }

    public function deleteForUser(int $id, int $userId): void
    {
        $this->transactionService->execute(function () use ($id, $userId) {
            $this->findForUser($id, $userId)
                ->delete();
        });
    }

    public function create(array $data)
    {
        return $this->transactionService->execute(function () use ($data) {
            // Fechas datetime deben llegar ya convertidas a UTC desde el FormRequest.
            // La BD siempre almacena UTC. Ver ActivityFormRequest para la conversión.
            return $this->activityRepository->create($data);
        });
    }

    public function update(int $id, array $data)
    {
        return $this->transactionService->execute(function () use ($id, $data) {
            return $this->activityRepository->update($id, $data);
        });
    }

    public function delete(int $id): bool
    {
        return $this->transactionService->execute(function () use ($id) {
            return $this->activityRepository->delete($id);
        });
    }

    public function find(int $id)
    {
        return $this->activityRepository->find($id);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->activityRepository->paginate($perPage);
    }
}
