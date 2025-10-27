<?php

namespace App\Providers;

use App\Common\Repository\RepositoryManager;
use App\Common\Repository\Service\TransactionService;
use App\Http\View\Composers\SidebarComposer;
use App\Http\View\Composers\TopbarComposer;
use App\Models\Tenant;
use App\Models\User;
use App\Projects\Landlord\Repositories\TenantRepository;
use App\Projects\Landlord\Repositories\UserRepository;
use App\Projects\Landlord\Services\Model\TenantService;
use App\Projects\Landlord\Services\Model\UserService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository Manager
        $this->app->singleton(RepositoryManager::class, function ($app) {
            $manager = new RepositoryManager();
            $manager->register(User::class, UserRepository::class);
            $manager->register(Tenant::class, TenantRepository::class);
            return $manager;
        });

        // Transaction Service (Singleton)
        $this->app->singleton(TransactionService::class);

        // Tenant Service
        $this->app->bind(TenantService::class, function ($app) {
            return new TenantService(
                $app->make(TransactionService::class),
                $app->make(TenantRepository::class),
                $app->make(UserRepository::class)
            );
        });

        // User Service
        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(TransactionService::class), $app->make(UserRepository::class));
        });
    }

    public function boot(): void
    {
        View::composer('partials.sidebar-menu', SidebarComposer::class);
        View::composer('partials.top-bar', TopbarComposer::class);
    }
}
