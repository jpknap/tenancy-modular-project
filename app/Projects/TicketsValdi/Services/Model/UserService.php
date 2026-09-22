<?php

namespace App\Projects\TicketsValdi\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\TicketsValdi\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserService
{
    public function __construct(
        private TransactionService $transactionService,
        private UserRepository $userRepository
    ) {
    }

    public function create(array $data): Model
    {
        return $this->transactionService->execute(function () use ($data) {
            return $this->userRepository->create($this->prepare($data));
        });
    }

    public function update(int $userId, array $data): Model
    {
        return $this->transactionService->execute(function () use ($userId, $data) {
            $this->userRepository->update($userId, $this->prepare($data));

            $user = $this->userRepository->find($userId);

            if (! $user) {
                throw new RuntimeException(__('tickets-valdi::messages.user.not_found'));
            }

            return $user;
        });
    }

    public function delete(int $userId): bool
    {
        return $this->transactionService->execute(function () use ($userId) {
            if (! $this->userRepository->find($userId)) {
                throw new RuntimeException(__('tickets-valdi::messages.user.not_found'));
            }

            return $this->userRepository->delete($userId);
        });
    }

    /**
     * Hashea la contraseña y descarta campos que no son columnas del modelo.
     */
    private function prepare(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        unset($data['password_confirmation']);

        return $data;
    }
}
