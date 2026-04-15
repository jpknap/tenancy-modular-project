<?php

namespace App\Projects\ActivitiesBoard\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\ActivitiesBoard\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private TransactionService $transactionService
    ) {
    }

    public function create(array $data)
    {
        return $this->transactionService->execute(function () use ($data) {
            // Hash password
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Remove password_confirmation
            unset($data['password_confirmation']);

            return $this->userRepository->create($data);
        });
    }

    public function update(int $id, array $data)
    {
        return $this->transactionService->execute(function () use ($id, $data) {
            // Si no se proporciona contraseña, eliminarla del array
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            // Remove password_confirmation
            unset($data['password_confirmation']);

            if (isset($data['role'])) {
                $role = $data['role'];
                unset($data['role']);
                $user = $this->userRepository->update($id, $data);
                // No permitir que superadmin se cambie el rol a sí mismo
                $currentUser = auth()
                    ->user();
                if (! $currentUser || $currentUser->id !== $user->id) {
                    $user->syncRoles([$role]);
                }
                return $user;
            }

            return $this->userRepository->update($id, $data);
        });
    }

    public function delete(int $id): bool
    {
        return $this->transactionService->execute(function () use ($id) {
            return $this->userRepository->delete($id);
        });
    }

    public function find(int $id)
    {
        return $this->userRepository->find($id);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->userRepository->paginate($perPage);
    }
}
