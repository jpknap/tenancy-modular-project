<?php

namespace App\Providers;

use App\Common\Repository\RepositoryManager;
use App\Http\View\Composers\SidebarComposer;
use App\Http\View\Composers\TopbarComposer;
use App\Projects\Landlord\Repositories\TenantRepository;
use App\Projects\Landlord\Repositories\UserRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar RepositoryManager como Singleton
        $this->app->singleton(RepositoryManager::class, function ($app) {
            $manager = new RepositoryManager();
            $manager->register('user', UserRepository::class);
            $manager->register('tenant', TenantRepository::class);
            return $manager;
        });
    }

    public function boot(): void
    {
        View::composer('partials.sidebar-menu', SidebarComposer::class);
        View::composer('partials.top-bar', TopbarComposer::class);
    }
}
