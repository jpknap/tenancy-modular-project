<?php

namespace App\Projects\Landlord\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\ProjectManager;
use App\Projects\Landlord\LandlordProject;
use App\Projects\Landlord\Repositories\TenantRepository;
use App\Projects\Landlord\Repositories\UserRepository;
use App\Services\ProjectInitService;
use Illuminate\Support\Str;

class TenantService
{
    public function __construct(
        private TransactionService $transactionService,
        private TenantRepository $tenantRepository,
        private UserRepository $userRepository
    ) {
    }

    public function create(array $data)
    {
        // NO usar transacción porque PostgreSQL no permite CREATE SCHEMA dentro de transacciones
        // El evento TenantCreated disparará la creación del schema automáticamente
        $identifier = Str::slug($data['name']);
        $subdomain = $data['subdomain'];

        $tenantData = [
            'name'            => $data['name'],
            'identifier'      => $identifier,
            'current_project' => $data['current_project'] ?? null,
            'timezone'        => $data['timezone'] ?? 'UTC',
            'locale'          => $data['locale'] ?? 'es',
            'data'            => [
                'email'       => $data['email'] ?? null,
                'status'      => $data['status'] ?? 'pending',
                'description' => $data['description'] ?? null,
            ],
        ];


        $tenant = $this->tenantRepository->create($tenantData);
        $this->createDomain($tenant, $subdomain);
        
        $this->setupDefaultSettings($tenant);

        return $tenant;
    }

    public function update(int $id, array $data)
    {
        return $this->transactionService->execute(function () use ($id, $data) {
            $tenant = $this->tenantRepository->find($id);

            if (!$tenant) {
                throw new \Exception('Tenant not found');
            }

            $currentData = $tenant->data ?? [];

            $tenantData = [
                'name'            => $data['name'],
                'current_project' => $data['current_project'] ?? $tenant->current_project,
                'timezone'        => $data['timezone'] ?? $tenant->timezone,
                'locale'          => $data['locale'] ?? $tenant->locale,
                'data'            => array_merge($currentData, [
                    'email'       => $data['email'] ?? null,
                    'status'      => $data['status'] ?? 'pending',
                    'description' => $data['description'] ?? null,
                ]),
            ];

            return $this->tenantRepository->update($id, $tenantData);
        });
    }

    public function delete(int $id): bool
    {
        // NO usar transacción porque PostgreSQL no permite DROP SCHEMA dentro de transacciones
        // El evento TenantDeleted disparará la eliminación del schema automáticamente
        return $this->tenantRepository->delete($id);
    }

    private function createDomain($tenant, string $subdomain): void
    {
        $domain = $subdomain . '.' . config('app.domain', 'localhost');

        $tenant->domains()->create([
            'domain' => $domain,
            'subdomain' => $subdomain,
        ]);
    }

    public function createTenantWithAdmin(array $tenantData, array $adminData): array
    {
        return $this->transactionService->execute(function () use ($tenantData, $adminData) {
            $tenant = $this->tenantRepository->create($tenantData);
            $adminData['tenant_id'] = $tenant->id;
            $adminData['role'] = 'admin';
            $admin = $this->userRepository->create($adminData);
            $this->setupDefaultSettings($tenant);

            return [
                'tenant' => $tenant,
                'admin' => $admin,
            ];
        });
    }

    /**
     * Actualiza tenant y sus usuarios relacionados
     */
    public function updateTenantWithUsers(int $tenantId, array $tenantData, array $usersData): array
    {
        return $this->transactionService->executeMultiple([
            'tenant' => fn () => $this->tenantRepository->update($tenantId, $tenantData),
            'users' => fn () => $this->updateRelatedUsers($tenantId, $usersData),
        ]);
    }

    /**
     * Elimina tenant y todos sus datos relacionados
     */
    public function deleteTenantWithRelations(int $tenantId): bool
    {
        return $this->transactionService->execute(function () use ($tenantId) {
            $tenant = $this->tenantRepository->find($tenantId);

            if (! $tenant) {
                throw new \Exception('Tenant not found');
            }

            // 1. Eliminar usuarios del tenant
            foreach ($tenant->users as $user) {
                $this->userRepository->delete($user->id);
            }

            // 2. Eliminar configuraciones
            $this->deleteSettings($tenant);

            // 3. Eliminar el tenant
            return $this->tenantRepository->delete($tenantId);
        });
    }

    /**
     * Migra usuarios de un tenant a otro
     */
    public function migrateUsers(int $fromTenantId, int $toTenantId, array $userIds): array
    {
        return $this->transactionService->execute(function () use ($fromTenantId, $toTenantId, $userIds) {
            $migratedUsers = [];

            foreach ($userIds as $userId) {
                $user = $this->userRepository->find($userId);

                if ($user && $user->tenant_id === $fromTenantId) {
                    $this->userRepository->update($userId, [
                        'tenant_id' => $toTenantId,
                    ]);
                    $migratedUsers[] = $user;
                }
            }

            return $migratedUsers;
        });
    }

    /**
     * Actualiza múltiples entidades relacionadas
     */
    public function bulkUpdate(array $updates): array
    {
        return $this->transactionService->execute(function () use ($updates) {
            $results = [];

            // Actualizar tenants
            if (isset($updates['tenants'])) {
                foreach ($updates['tenants'] as $id => $data) {
                    $results['tenants'][$id] = $this->tenantRepository->update($id, $data);
                }
            }

            // Actualizar usuarios
            if (isset($updates['users'])) {
                foreach ($updates['users'] as $id => $data) {
                    $results['users'][$id] = $this->userRepository->update($id, $data);
                }
            }

            return $results;
        });
    }

    /**
     * Configuraciones por defecto para nuevo tenant
     */
    private function setupDefaultSettings($tenant): void
    {
        //TODO: Lógica para crear configuraciones por defecto
        //Ejemplo: crear roles, permisos, configuraciones, etc.
    }

    /**
     * Actualiza usuarios relacionados al tenant
     */
    private function updateRelatedUsers(int $tenantId, array $usersData): array
    {
        $updated = [];
        foreach ($usersData as $userId => $userData) {
            $updated[$userId] = $this->userRepository->update($userId, $userData);
        }
        return $updated;
    }

    /**
     * Elimina configuraciones del tenant
     */
    private function deleteSettings($tenant): void
    {
        // Lógica para eliminar configuraciones
    }
}
