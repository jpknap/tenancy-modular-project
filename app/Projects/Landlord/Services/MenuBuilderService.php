<?php

namespace App\Projects\Landlord\Services;

use App\DTO\Menu\MenuBuilder;
use App\DTO\Menu\MenuItem;
use App\ProjectManager;
use App\Projects\Landlord\LandlordProject;

class MenuBuilderService
{
    protected static array $items = [
        'users' => [
            'label' => 'Users',
            'alias_route' => 'landlord.users.list',
            'icon' => 'icon',
            'permissions' => [],
            'children' => [],
        ],
        'tenants' => [
            'label' => 'Tenants',
            'alias_route' => 'landlord.tenants.list',
            'icon' => 'icon',
            'permissions' => [],
            'children' => [],
        ]
    ];
    public static function buildMenu(): MenuBuilder
    {
        $items = [];
        foreach (self::$items as $item) {
            $items[] = new MenuItem(
                label: $item['label'],
                url: route($item['alias_route']),
                icon: $item['icon'],
                permissions: $item['permissions'],
                children: $item['children']
            );
        }
        return new MenuBuilder(title: LandlordProject::getTitle(), items: $items);
    }
}
