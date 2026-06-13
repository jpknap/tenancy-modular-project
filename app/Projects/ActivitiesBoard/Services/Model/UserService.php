<?php

namespace App\Projects\ActivitiesBoard\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\ActivitiesBoard\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private TransactionService $transactionService
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

    public function update(int $id, array $data): Model
    {
        return $this->transactionService->execute(function () use ($id, $data) {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            $role = $data['role'] ?? null;
            unset($data['role'], $data['password_confirmation']);

            $this->userRepository->update($id, $data);
            $user = $this->userRepository->find($id);

            if ($role !== null) {
                $currentUser = auth()->user();
                if (! $currentUser || $currentUser->id !== $user->id) {
                    $user->syncRoles([$role]);
                    $this->forgetUserRolesCache($user);
                }
            }

            return $user;
        });
    }

    public function delete(int $id): bool
    {
        return $this->transactionService->execute(function () use ($id) {
            return $this->userRepository->delete($id);
        });
    }

    public function find(int $id): ?Model
    {
        return $this->userRepository->find($id);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->userRepository->paginate($perPage);
    }

    private function forgetUserRolesCache(Model $user): void
    {
        $tenantKey = tenancy()->tenant->getTenantKey();
        cache()->forget("user.{$user->id}.roles.tenant.{$tenantKey}");
    }
}
