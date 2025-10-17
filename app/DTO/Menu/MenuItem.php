<?php

namespace App\DTO\Menu;

readonly class MenuItem
{
    public function __construct(
        public string $label,
        public string $url,
        public string $icon,
        public array $permissions = [],
        public array $children = []
    ) {
    }

}
