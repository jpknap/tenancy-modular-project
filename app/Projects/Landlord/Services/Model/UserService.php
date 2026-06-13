<?php

namespace App\Projects\Landlord\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\Landlord\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

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
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $role = $data['role'] ?? null;
            unset($data['role'], $data['password_confirmation']);

            $user = $this->userRepository->create($data);

            if ($role !== null) {
                $user->syncRoles([$role]);
            }

            return $user;
        });
    }

    public function update(int $userId, array $data): Model
    {
        return $this->transactionService->execute(function () use ($userId, $data) {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $role = $data['role'] ?? null;
            unset($data['role'], $data['password_confirmation']);

            $this->userRepository->update($userId, $data);
            $user = $this->userRepository->find($userId);

            if ($role !== null) {
                $currentUser = auth()->user();
                if (! $currentUser || $currentUser->id !== $user->id) {
                    $user->syncRoles([$role]);
                }
            }

            return $user;
        });
    }

    public function delete(int $userId): bool
    {
        return $this->transactionService->execute(function () use ($userId) {
            $user = $this->userRepository->find($userId);

            if (! $user) {
                throw new \Exception('User not found');
            }

            $this->beforeDelete($user);

            return $this->userRepository->delete($userId);
        });
    }

    protected function beforeDelete(Model $user): void {}
}
