<?php

namespace App\Projects\Landlord;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Contracts\ProjectInterface;
use App\DTO\Endpoint;
use App\DTO\Menu\MenuBuilder;
use App\DTO\Menu\MenuItem;
use App\Module\Admin\Contracts\PageAdminInterface;
use App\Projects\Landlord\Services\MenuBuilderService;
use App\Services\EndpointProcessor;

class LandlordProject implements ProjectInterface
{
    private static string $title = 'Landlord';

    public static string $prefix = 'landlord';

    private MenuBuilder $menuBuilder;

    public function init(): void
    {
        $this->initMenu();
    }

    public static function getTitle(): string
    {
        return self::$title;
    }

    public function getMenuBuilder(): MenuBuilder
    {
        return $this->menuBuilder;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    private function initMenu(): void
    {
        $this->menuBuilder = MenuBuilderService::buildMenu();
    }

    /** @return Endpoint[] */
    public static function getEndpoints(): array
    {
        $adminControllers = [];
        $admins = config('projects.landlord.admins');

        /** @var class-string<AdminBaseAdapter> $adminClass */
        foreach ($admins as $adminClass) {
            $adminControllers[] = $adminClass::$controller;
        }
        $controllers = config('projects.landlord.controllers');

        $allControllers = [...$controllers, ...$adminControllers];
        $processor = new EndpointProcessor();
        return $processor->process($allControllers, self::$prefix);
    }
}
