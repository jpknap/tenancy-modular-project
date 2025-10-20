<?php

namespace App\Projects\Landlord\Services\Model;

use App\Common\Service\TransactionService;
use App\Projects\Landlord\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private TransactionService $transactionService,
        private UserRepository $userRepository
    ) {}

    /**
     * Crea un usuario (usado desde AdminController)
     */
    public function create(array $data)
    {
        return $this->transactionService->execute(function () use ($data) {
            // Hash de password si existe
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Crear usuario
            $user = $this->userRepository->create($data);

            // Asignar rol por defecto si existe
            if (isset($data['role'])) {
                $this->assignRole($user, $data['role']);
            }

            return $user;
        });
    }

    /**
     * Actualiza un usuario
     */
    public function update(int $userId, array $data)
    {
        return $this->transactionService->execute(function () use ($userId, $data) {
            // Hash de password si existe
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Actualizar usuario
            $updated = $this->userRepository->update($userId, $data);

            // Actualizar rol si cambió
            if (isset($data['role'])) {
                $user = $this->userRepository->find($userId);
                $this->assignRole($user, $data['role']);
            }

            return $updated;
        });
    }

    /**
     * Elimina un usuario
     */
    public function delete(int $userId): bool
    {
        return $this->transactionService->execute(function () use ($userId) {
            $user = $this->userRepository->find($userId);

            if (!$user) {
                throw new \Exception("User not found");
            }

            // Lógica adicional antes de eliminar
            $this->beforeDelete($user);

            return $this->userRepository->delete($userId);
        });
    }

    /**
     * Asigna rol al usuario
     */
    private function assignRole($user, string $role): void
    {
        // Implementar lógica de roles
        // Ejemplo: $user->syncRoles([$role]);
    }

    /**
     * Lógica antes de eliminar
     */
    private function beforeDelete($user): void
    {
        // Ejemplo: eliminar archivos, notificaciones, etc.
    }
}
