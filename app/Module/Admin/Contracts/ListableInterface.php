<?php

namespace App\Module\Admin\Contracts;

interface ListableInterface
{
    /**
     * @return array<string>
     */
    public function getListableAttributes(): array;
}
