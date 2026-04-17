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
            $date = \DateTime::createFromFormat('Y-m-d', $value);
            if ($date === false) {
                return $query;
            }

            return $query->whereDate($column, '=', $value);
        } catch (\Exception) {
            return $query;
        }
    }

    public function render(string $columnName, mixed $currentValue = null): string
    {
        $value = htmlspecialchars($currentValue ?? '', ENT_QUOTES, 'UTF-8');
        $id = 'filter_' . str_replace(['.', '-'], '_', $columnName);

        return <<<HTML
<input
    type="date"
    class="form-control form-control-sm column-filter-text"
    id="$id"
    name="filters[$columnName]"
    value="$value"
    data-column="$columnName"
    autocomplete="off"
>
HTML;
    }
}
