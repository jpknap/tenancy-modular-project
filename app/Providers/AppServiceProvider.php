<?php

namespace App\Providers;

use App\Common\Repository\RepositoryManager;
use App\Common\Repository\Service\TransactionService;
use App\Common\Services\AlertManager;
use App\Http\View\Composers\SidebarComposer;
use App\Http\View\Composers\TopbarComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar solo servicios globales (compartidos por todos los proyectos)
        $this->app->singleton(RepositoryManager::class, function ($app) {
            return new RepositoryManager();
        });

        $this->app->singleton(TransactionService::class);

        $this->app->singleton(AlertManager::class);

        // Los repositorios y servicios específicos de cada proyecto
        // se registran en sus propios ServiceProviders cuando el proyecto se inicializa
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole('superadmin')) {
                return true;
            }

            return null;
        });

        View::composer('partials.sidebar-menu', SidebarComposer::class);
        View::composer('partials.top-bar', TopbarComposer::class);
    }
}
