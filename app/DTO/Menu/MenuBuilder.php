<?php

namespace App\DTO\Menu;

readonly class MenuBuilder
{
    public function __construct(
        public string $title,
        public array $items
    ) {
    }

}
