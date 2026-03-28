<?php

namespace App\Common\Admin\Contracts;

interface ListableInterface
{
    /**
     * @return array<string>
     */
    public function getListableAttributes(): array;
}
