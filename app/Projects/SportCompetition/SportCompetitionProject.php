<?php

namespace App\Projects\SportCompetition;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Contracts\ProjectInterface;
use App\DTO\Endpoint;
use App\DTO\Menu\MenuBuilder;
use App\Projects\SportCompetition\Providers\SportCompetitionServiceProvider;
use App\Projects\SportCompetition\Services\MenuBuilderService;
use App\Services\EndpointProcessor;

class SportCompetitionProject implements ProjectInterface
{
    public static string $prefix = 'sport-competition';

    private static string $title = 'Sport Competition';

    private MenuBuilder $menuBuilder;

    public function init(): void
    {
        $this->registerServiceProvider();
        $this->initMenu();
        $this->registerTranslations();
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
        $admins = config('projects.sport-competition.admins');

        /** @var class-string<AdminBaseAdapter> $adminClass */
        foreach ($admins as $adminClass) {
            $adminControllers[] = $adminClass::getController();
        }
        $controllers = config('projects.sport-competition.controllers');

        $allControllers = [...$controllers, ...$adminControllers];
        $processor = new EndpointProcessor();
        return $processor->process($allControllers, self::$prefix);
    }

    public function getPathMigration(): string
    {
        return 'SportCompetition';
    }

    public function getLangPath(): string
    {
        return lang_path('projects/sport-competition');
    }

    /**
     * Registrar el ServiceProvider del proyecto
     */
    private function registerServiceProvider(): void
    {
        $app = app();

        if (! $app->providerIsLoaded(SportCompetitionServiceProvider::class)) {
            $app->register(SportCompetitionServiceProvider::class);
        }
    }

    private function initMenu(): void
    {
        $this->menuBuilder = MenuBuilderService::buildMenu();
    }

    private function registerTranslations(): void
    {
        app('translator')->addNamespace(static::$prefix, $this->getLangPath());
    }
}
