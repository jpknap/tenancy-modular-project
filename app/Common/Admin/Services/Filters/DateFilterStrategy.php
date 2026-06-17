<?php

namespace App\Common\Admin\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

class DateFilterStrategy implements FilterStrategyInterface
{
    public function applyFilter(Builder $query, string $column, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        try {
            $date = \DateTime::createFromFormat('d/m/Y', $value);
            if ($date === false) {
                return $query;
            }

            return $query->whereDate($column, '=', $date->format('Y-m-d'));
        } catch (\Exception) {
            return $query;
        }
    }

    public function getType(): string
    {
        return 'date';
    }
}
