<?php

namespace App\Projects\Landlord;

use App\Contracts\ProjectInterface;
use App\DTO\Menu\MenuBuilder;
use App\DTO\Menu\MenuItem;
use App\Module\Admin\Contracts\PageAdminInterface;

class LandlordProject implements ProjectInterface
{
    private string $title = 'Landlord';

    private string $prefix = 'landlord';

    private MenuBuilder $menuBuilder;

    public function init(): void
    {
        $this->initMenu();
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
        $admins = config('projects.landlord.admins');
        $items = [];
        foreach ($admins as $adminClass) {
            /** @var PageAdminInterface $admin */
            $admin = new $adminClass();
            $resource = $admin->getRoutePrefix();

            $url = $resource ? url("{$this->prefix}/admin/{$resource}/list") : '#';
            $items[] = new MenuItem(label: $resource, url: $url, icon: 'icon', permissions: [], children: []);
        }
        $this->menuBuilder = new MenuBuilder(title: $this->title, items: $items);
    }
}
