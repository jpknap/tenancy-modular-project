<?php

namespace App\Projects\TicketsValdi;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Contracts\ProjectInterface;
use App\DTO\Endpoint;
use App\DTO\Menu\MenuBuilder;
use App\Projects\TicketsValdi\Providers\TicketsValdiServiceProvider;
use App\Projects\TicketsValdi\Services\MenuBuilderService;
use App\Services\EndpointProcessor;

class TicketsValdiProject implements ProjectInterface
{
    public static string $prefix = 'tickets-valdi';

    private static string $title = 'Tickets Valdi';

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
        $admins = config('projects.tickets-valdi.admins');

        /** @var class-string<AdminBaseAdapter> $adminClass */
        foreach ($admins as $adminClass) {
            $adminControllers[] = $adminClass::getController();
        }
        $controllers = config('projects.tickets-valdi.controllers');

        $allControllers = [...$controllers, ...$adminControllers];
        $processor = new EndpointProcessor();
        return $processor->process($allControllers, self::$prefix);
    }

    public function getPathMigration(): string
    {
        return 'TicketsValdi';
    }

    public function getLangPath(): string
    {
        return lang_path('projects/tickets-valdi');
    }

    /**
     * Registrar el ServiceProvider del proyecto
     */
    private function registerServiceProvider(): void
    {
        $app = app();

        if (! $app->providerIsLoaded(TicketsValdiServiceProvider::class)) {
            $app->register(TicketsValdiServiceProvider::class);
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
