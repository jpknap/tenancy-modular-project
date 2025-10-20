<?php

namespace App\Contracts;

use App\DTO\Menu\MenuBuilder;

interface ProjectInterface
{
    public static function getTitle(): string;
    public static function getPrefix(): string;

    public function init(): void;

    public function getMenuBuilder(): MenuBuilder;
}
