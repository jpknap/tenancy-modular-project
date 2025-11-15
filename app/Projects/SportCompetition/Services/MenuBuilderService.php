<?php

namespace App\Projects\SportCompetition\Services;

use App\DTO\Menu\MenuBuilder;
use App\DTO\Menu\MenuItem;
use App\Projects\SportCompetition\SportCompetitionProject;

class MenuBuilderService
{
    protected static array $items = [
        'users' => [
            'label' => 'Users',
            'alias_route' => 'sport-competition.admin.users.list',
            'icon' => 'icon',
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
        return new MenuBuilder(title: SportCompetitionProject::getTitle(), items: $items);
    }
}
