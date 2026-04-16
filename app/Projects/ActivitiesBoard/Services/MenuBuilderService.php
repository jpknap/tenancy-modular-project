<?php

namespace App\Projects\ActivitiesBoard\Services;

use App\DTO\Menu\MenuBuilder;
use App\DTO\Menu\MenuItem;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use App\Projects\ActivitiesBoard\Enums\Routes;

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
        'activities' => [
            'label' => 'Actividades',
            'alias_route' => Routes::ActivityList->value,
            'icon' => 'bi-list-check',
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
        return new MenuBuilder(title: ActivitiesBoardProject::getTitle(), items: $items);
    }
}
