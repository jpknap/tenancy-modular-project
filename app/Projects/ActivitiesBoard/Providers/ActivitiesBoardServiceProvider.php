<?php

namespace App\Projects\ActivitiesBoard\Providers;

use App\Common\Repository\RepositoryManager;
use App\Common\Repository\Service\TransactionService;
use App\Common\Services\FileUpload\Contracts\FileUploaderInterface;
use App\Projects\ActivitiesBoard\Models\Activity;
use App\Projects\ActivitiesBoard\Models\ActivityLog;
use App\Projects\ActivitiesBoard\Models\Category;
use App\Projects\ActivitiesBoard\Models\User;
use App\Projects\ActivitiesBoard\Repositories\ActivityLogRepository;
use App\Projects\ActivitiesBoard\Repositories\ActivityRepository;
use App\Projects\ActivitiesBoard\Repositories\CategoryRepository;
use App\Projects\ActivitiesBoard\Repositories\UserRepository;
use App\Projects\ActivitiesBoard\Services\FileUpload\AttachmentUploadService;
use App\Projects\ActivitiesBoard\Services\Model\ActivityLogService;
use App\Projects\ActivitiesBoard\Services\Model\ActivityService;
use App\Projects\ActivitiesBoard\Services\Model\CategoryService;
use App\Projects\ActivitiesBoard\Services\Model\UserService;
use Illuminate\Support\ServiceProvider;

class ActivitiesBoardServiceProvider extends ServiceProvider
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
        $manager->register(Activity::class, ActivityRepository::class);
        $manager->register(ActivityLog::class, ActivityLogRepository::class);
        $manager->register(Category::class, CategoryRepository::class);
        $manager->register(User::class, UserRepository::class);
    }

    /**
     * Registrar servicios del proyecto
     */
    private function registerServices(): void
    {
        $this->app->bind(ActivityService::class, function ($app) {
            return new ActivityService(
                $app->make(ActivityRepository::class),
                $app->make(TransactionService::class)
            );
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(UserRepository::class), $app->make(TransactionService::class));
        });

        $this->app->bind(ActivityLogService::class, function ($app) {
            return new ActivityLogService($app->make(TransactionService::class));
        });

        $this->app->bind(CategoryService::class, function ($app) {
            return new CategoryService(
                $app->make(CategoryRepository::class),
                $app->make(TransactionService::class)
            );
        });

        // DIP: los consumidores dependen de la interfaz, no de la clase concreta
        $this->app->bind(FileUploaderInterface::class, AttachmentUploadService::class);
    }
}
