<?php

namespace App\Common\Admin\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

class BooleanFilterStrategy implements FilterStrategyInterface
{
    public function applyFilter(Builder $query, string $column, mixed $value): Builder
    {
        if ($value === '' || $value === null) {
            return $query;
        }

        $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($boolValue === null) {
            return $query;
        }

        return $query->where($column, '=', $boolValue ? 1 : 0);
    }

    public function getType(): string
    {
        return 'boolean';
    }
}
