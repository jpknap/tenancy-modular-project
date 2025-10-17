<?php

namespace App\Contracts;

use App\DTO\Menu\MenuBuilder;

interface ProjectInterface
{
    public function getPrefix(): string;

    public function init(): void;

    public function getMenuBuilder(): MenuBuilder;
}
