<?php

namespace App\Providers;

use App\Common\Admin\Services\Filters\BooleanFilterStrategy;
use App\Common\Admin\Services\Filters\DateFilterStrategy;
use App\Common\Admin\Services\Filters\NumberFilterStrategy;
use App\Common\Admin\Services\Filters\TextFilterStrategy;
use App\Common\Repository\RepositoryManager;
use App\Common\Repository\Service\TransactionService;
use App\Common\Services\AlertManager;
use App\Http\View\Composers\SidebarComposer;
use App\Http\View\Composers\TopbarComposer;
use App\Listeners\InvalidateUserRolesCache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;

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

        $this->app->bind(TextFilterStrategy::class);
        $this->app->bind(NumberFilterStrategy::class);
        $this->app->bind(DateFilterStrategy::class);
        $this->app->bind(BooleanFilterStrategy::class);

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
        Blade::directive('displayDate', fn ($expression) => "<?= display_date({$expression}) ?>");
        Event::listen([RoleAttachedEvent::class, RoleDetachedEvent::class], InvalidateUserRolesCache::class);
    }
}
