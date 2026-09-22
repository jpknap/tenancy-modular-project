<?php

namespace App\Projects\TicketsValdi\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\TicketsValdi\Repositories\InstitutionRepository;
use Illuminate\Database\Eloquent\Model;

class InstitutionService
{
    public function __construct(
        private TransactionService $transactionService,
        private InstitutionRepository $institutionRepository
    ) {
    }

    public function create(array $data): Model
    {
        return $this->transactionService->execute(function () use ($data) {
            return $this->institutionRepository->create($data);
        });
    }

    public function update(int $id, array $data): Model
    {
        return $this->transactionService->execute(function () use ($id, $data) {
            $this->institutionRepository->update($id, $data);

            return $this->institutionRepository->find($id);
        });
    }
}
