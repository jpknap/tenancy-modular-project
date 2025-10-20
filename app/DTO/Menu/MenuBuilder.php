<?php

namespace App\DTO\Menu;

class MenuBuilder
{
    public function __construct(
        public string $title,
        public array $items
    ) {
    }

}
