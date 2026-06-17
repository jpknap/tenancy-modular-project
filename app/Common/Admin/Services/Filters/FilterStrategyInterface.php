<?php

namespace App\Common\Admin\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

interface FilterStrategyInterface
{
    public function applyFilter(Builder $query, string $column, mixed $value): Builder;

    public function getType(): string;
}
