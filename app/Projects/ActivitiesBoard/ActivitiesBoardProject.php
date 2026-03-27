<?php

namespace App\Projects\ActivitiesBoard;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Contracts\ProjectInterface;
use App\DTO\Endpoint;
use App\DTO\Menu\MenuBuilder;
use App\Projects\ActivitiesBoard\Providers\ActivitiesBoardServiceProvider;
use App\Projects\ActivitiesBoard\Services\MenuBuilderService;
use App\Services\EndpointProcessor;

class ActivitiesBoardProject implements ProjectInterface
{
    public static string $prefix = 'activities-board';

    private static string $title = 'Activities Board';

private MenuBuilder $menuBuilder;

    public function init(): void
    {
        $this->registerServiceProvider();
        $this->initMenu();
    }

    /**
     * Registrar el ServiceProvider del proyecto
     */
    private function registerServiceProvider(): void
    {
        $app = app();
        
        if (!$app->providerIsLoaded(ActivitiesBoardServiceProvider::class)) {
            $app->register(ActivitiesBoardServiceProvider::class);
        }
    }

    public static function getTitle(): string
    {
        return self::$title;
    }

    public function getMenuBuilder(): MenuBuilder
    {
        return $this->menuBuilder;
    }

    public static function getPrefix(): string
    {
        return static::$prefix;
    }

    /**
     * @return Endpoint[]
     */
    public static function getEndpoints(): array
    {
        $adminControllers = [];
        $admins = config('projects.activities-board.admins');

        /** @var class-string<AdminBaseAdapter> $adminClass */
        foreach ($admins as $adminClass) {
            $adminControllers[] = $adminClass::getController();
        }
        $controllers = config('projects.activities-board.controllers');

        $allControllers = [...$controllers, ...$adminControllers];
        $processor = new EndpointProcessor();
        return $processor->process($allControllers, self::$prefix);
    }

    private function initMenu(): void
    {
        $this->menuBuilder = MenuBuilderService::buildMenu();
    }

    public function getPathMigration(): string
    {
        return "ActivitiesBoard";
    }
}
