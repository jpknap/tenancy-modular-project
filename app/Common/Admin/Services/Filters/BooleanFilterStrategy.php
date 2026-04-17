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

    public function render(string $columnName, mixed $currentValue = null): string
    {
        $value = htmlspecialchars($currentValue ?? '', ENT_QUOTES, 'UTF-8');
        $id = 'filter_' . str_replace(['.', '-'], '_', $columnName);

        return <<<HTML
<select
    class="form-select form-select-sm column-filter-text"
    id="$id"
    name="filters[$columnName]"
    data-column="$columnName"
>
    <option value="">— Todos —</option>
    <option value="1" @selected($value === '1')>Sí</option>
    <option value="0" @selected($value === '0')>No</option>
</select>
HTML;
    }
}
