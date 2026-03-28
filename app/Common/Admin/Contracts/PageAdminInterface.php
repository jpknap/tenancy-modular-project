<?php

namespace App\Common\Admin\Contracts;

interface PageAdminInterface extends ListableInterface
{
    public function getTitle(): string;

    public function getRoutePrefix(): string;

    public function getController(): string;
}
