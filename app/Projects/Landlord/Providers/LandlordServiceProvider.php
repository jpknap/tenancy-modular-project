<?php

namespace App\Projects\Landlord\Providers;

use App\Common\Repository\RepositoryManager;
use App\Common\Repository\Service\TransactionService;
use App\Models\Tenant;
use App\Models\User;
use App\Projects\Landlord\Repositories\TenantRepository;
use App\Projects\Landlord\Repositories\UserRepository;
use App\Projects\Landlord\Services\Model\TenantService;
use App\Projects\Landlord\Services\Model\UserService;
use Illuminate\Support\ServiceProvider;

class LandlordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar repositorios
        $this->registerRepositories();
        
        // Registrar servicios
        $this->registerServices();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Registrar repositorios del proyecto
     */
    private function registerRepositories(): void
    {
        $manager = $this->app->make(RepositoryManager::class);
        $manager->register(User::class, UserRepository::class);
        $manager->register(Tenant::class, TenantRepository::class);
    }

    /**
     * Registrar servicios del proyecto
     */
    private function registerServices(): void
    {
        $this->app->bind(TenantService::class, function ($app) {
            return new TenantService(
                $app->make(TransactionService::class),
                $app->make(TenantRepository::class),
                $app->make(UserRepository::class)
            );
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService(
                $app->make(TransactionService::class),
                $app->make(UserRepository::class)
            );
        });
    }
}
