<?php

namespace App\Projects\ActivitiesBoard\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\ActivitiesBoard\Repositories\ActivityRepository;

class ActivityService
{
    public function __construct(
        private ActivityRepository $activityRepository,
        private TransactionService $transactionService
    ) {
    }

    public function create(array $data)
    {
        return $this->transactionService->execute(function () use ($data) {
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
