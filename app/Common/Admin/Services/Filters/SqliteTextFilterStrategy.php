<?php

namespace App\Common\Admin\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

class SqliteTextFilterStrategy implements FilterStrategyInterface
{
    public function applyFilter(Builder $query, string $column, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        return $query->whereRaw(
            "LOWER({$column}) LIKE ?",
            ['%' . mb_strtolower($value) . '%']
        );
    }

    public function getType(): string
    {
        return 'text';
    }
}
