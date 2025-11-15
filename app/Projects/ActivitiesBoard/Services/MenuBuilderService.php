<?php

namespace App\Projects\ActivitiesBoard\Services;

use App\DTO\Menu\MenuBuilder;
use App\DTO\Menu\MenuItem;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;

class MenuBuilderService
{
    protected static array $items = [
        'activities' => [
            'label' => 'Actividades',
            'alias_route' => 'activities-board.admin.activities.list',
            'icon' => 'bi-list-check',
            'permissions' => [],
            'children' => [],
        ],
        'users' => [
            'label' => 'Usuarios',
            'alias_route' => 'activities-board.admin.users.list',
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
        return new MenuBuilder(title: ActivitiesBoardProject::getTitle(), items: $items);
    }
}
