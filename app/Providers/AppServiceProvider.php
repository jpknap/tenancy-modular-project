<?php

namespace App\Providers;

use App\Common\Repository\RepositoryManager;
use App\Common\Repository\Service\TransactionService;
use App\Common\Services\AlertManager;
use App\Http\View\Composers\SidebarComposer;
use App\Http\View\Composers\TopbarComposer;
use App\Models\Tenant;
use App\Models\User;
use App\Projects\ActivitiesBoard\Models\Activity;
use App\Projects\ActivitiesBoard\Models\User as ActivitiesBoardUser;
use App\Projects\ActivitiesBoard\Repositories\ActivityRepository;
use App\Projects\ActivitiesBoard\Repositories\UserRepository as ActivitiesBoardUserRepository;
use App\Projects\ActivitiesBoard\Services\Model\ActivityService;
use App\Projects\ActivitiesBoard\Services\Model\UserService as ActivitiesBoardUserService;
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
        $this->app->singleton(RepositoryManager::class, function ($app) {
            $manager = new RepositoryManager();
            // Landlord repositories
            $manager->register(User::class, UserRepository::class);
            $manager->register(Tenant::class, TenantRepository::class);
            
            // ActivitiesBoard repositories
            $manager->register(Activity::class, ActivityRepository::class);
            $manager->register(ActivitiesBoardUser::class, ActivitiesBoardUserRepository::class);
            
            return $manager;
        });

        $this->app->singleton(TransactionService::class);

        $this->app->singleton(AlertManager::class);

        $this->app->bind(TenantService::class, function ($app) {
            return new TenantService(
                $app->make(TransactionService::class),
                $app->make(TenantRepository::class),
                $app->make(UserRepository::class)
            );
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(TransactionService::class), $app->make(UserRepository::class));
        });

        // ActivitiesBoard Services
        $this->app->bind(ActivityService::class, function ($app) {
            return new ActivityService(
                $app->make(ActivityRepository::class),
                $app->make(TransactionService::class)
            );
        });

        $this->app->bind(ActivitiesBoardUserService::class, function ($app) {
            return new ActivitiesBoardUserService(
                $app->make(ActivitiesBoardUserRepository::class),
                $app->make(TransactionService::class)
            );
        });
    }

    public function boot(): void
    {
        View::composer('partials.sidebar-menu', SidebarComposer::class);
        View::composer('partials.top-bar', TopbarComposer::class);
    }
}
