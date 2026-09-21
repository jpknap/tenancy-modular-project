<?php

namespace App\Projects\ActivitiesBoard\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\ActivitiesBoard\Models\Activity;
use App\Projects\ActivitiesBoard\Models\ActivityLog;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    private const PER_PAGE = 15;

    public function __construct(
        private TransactionService $transactionService
    ) {
    }

    public function paginateForActivity(Activity $activity): LengthAwarePaginator
    {
        return $activity->logs()
            ->latest('occurred_at')
            ->paginate(self::PER_PAGE);
    }

    public function findForActivity(Activity $activity, int $id): ActivityLog
    {
        /** @var ActivityLog $log */
        $log = $activity->logs()
            ->findOrFail($id);

        return $log;
    }

    /**
     * Idempotente por `client_uuid`: reintentos del PWA (offline-first) al
     * reenviar el mismo registro no crean duplicados.
     */
    public function createIdempotent(Activity $activity, array $data): ActivityLog
    {
        return $this->transactionService->execute(function () use ($activity, $data) {
            $existing = $activity->logs()
                ->where('client_uuid', $data['client_uuid'])->first();

            return $existing ?? $activity->logs()
                ->create($data);
        });
    }

    public function updateForActivity(Activity $activity, int $id, array $data): ActivityLog
    {
        return $this->transactionService->execute(function () use ($activity, $id, $data) {
            $log = $this->findForActivity($activity, $id);
            $log->update($data);

            return $log;
        });
    }

    public function deleteForActivity(Activity $activity, int $id): void
    {
        $this->transactionService->execute(function () use ($activity, $id) {
            $this->findForActivity($activity, $id)
                ->delete();
        });
    }
}
