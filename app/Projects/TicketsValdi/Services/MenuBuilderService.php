<?php

namespace App\Projects\TicketsValdi\Services;

use App\DTO\Menu\MenuBuilder;
use App\DTO\Menu\MenuItem;
use App\Projects\TicketsValdi\Enums\Routes;
use App\Projects\TicketsValdi\TicketsValdiProject;

class MenuBuilderService
{
    protected static array $items = [
        'users' => [
            'label' => 'Usuarios',
            'alias_route' => Routes::UserList->value,
            'icon' => 'bi-people',
            'permissions' => [],
            'children' => [],
        ],
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
        return new MenuBuilder(title: TicketsValdiProject::getTitle(), items: $items);
    }
}
