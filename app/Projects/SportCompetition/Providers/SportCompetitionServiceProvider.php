<?php

namespace App\Projects\SportCompetition\Providers;

use App\Common\Repository\RepositoryManager;
use App\Common\Repository\Service\TransactionService;
use App\Models\User;
use App\Projects\SportCompetition\Repositories\UserRepository;
use App\Projects\SportCompetition\Services\Model\UserService;
use Illuminate\Support\ServiceProvider;

class SportCompetitionServiceProvider extends ServiceProvider
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
    }

    /**
     * Registrar repositorios del proyecto
     */
    private function registerRepositories(): void
    {
        $manager = $this->app->make(RepositoryManager::class);
        $manager->register(User::class, UserRepository::class);
    }

    /**
     * Registrar servicios del proyecto
     */
    private function registerServices(): void
    {
        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(TransactionService::class), $app->make(UserRepository::class));
        });
    }
}
