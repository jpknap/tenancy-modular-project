<?php

namespace App\Common\Admin\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

class NumberFilterStrategy implements FilterStrategyInterface
{
    public function applyFilter(Builder $query, string $column, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        return $query->where($column, '=', (int) $value);
    }

    public function render(string $columnName, mixed $currentValue = null): string
    {
        $value = htmlspecialchars($currentValue ?? '', ENT_QUOTES, 'UTF-8');
        $id = 'filter_' . str_replace(['.', '-'], '_', $columnName);

        return <<<HTML
<input
    type="number"
    class="form-control form-control-sm column-filter-text"
    id="$id"
    name="filters[$columnName]"
    value="$value"
    placeholder="Filtrar..."
    data-column="$columnName"
    autocomplete="off"
>
HTML;
    }
}
