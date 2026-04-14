<?php

namespace App\Projects\Landlord;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Contracts\ProjectInterface;
use App\DTO\Endpoint;
use App\DTO\Menu\MenuBuilder;
use App\Projects\Landlord\Providers\LandlordServiceProvider;
use App\Projects\Landlord\Services\MenuBuilderService;
use App\Services\EndpointProcessor;

class LandlordProject implements ProjectInterface
{
    public static string $prefix = 'landlord';

    private static string $title = 'Landlord';

private MenuBuilder $menuBuilder;

    public function init(): void
    {
        $this->registerServiceProvider();
        $this->initMenu();
        $this->registerTranslations();
    }

    /**
     * Registrar el ServiceProvider del proyecto
     */
    private function registerServiceProvider(): void
    {
        $app = app();
        
        if (!$app->providerIsLoaded(LandlordServiceProvider::class)) {
            $app->register(LandlordServiceProvider::class);
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
        $admins = config('projects.landlord.admins');

        /** @var class-string<AdminBaseAdapter> $adminClass */
        foreach ($admins as $adminClass) {
            $adminControllers[] = $adminClass::getController();
        }
        $controllers = config('projects.landlord.controllers');

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
        return "";
    }

    public function getLangPath(): string
    {
        return lang_path('projects/landlord');
    }

    private function registerTranslations(): void
    {
        app('translator')->addNamespace(static::$prefix, $this->getLangPath());
    }
}
