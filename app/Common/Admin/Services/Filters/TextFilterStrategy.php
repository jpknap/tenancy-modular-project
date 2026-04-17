<?php

namespace App\Common\Admin\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

class TextFilterStrategy implements FilterStrategyInterface
{
    public function applyFilter(Builder $query, string $column, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        $normalizedValue = $this->normalizeText($value);

        return $query->whereRaw(
            "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE($column, 'á', 'a'), 'Á', 'a'), 'é', 'e'), 'É', 'e'), 'í', 'i'), 'Í', 'i'), 'ó', 'o'), 'Ó', 'o'), 'ú', 'u'), 'Ú', 'u')) LIKE ?",
            ['%' . strtolower($normalizedValue) . '%']
        );
    }

    public function render(string $columnName, mixed $currentValue = null): string
    {
        $value = htmlspecialchars($currentValue ?? '', ENT_QUOTES, 'UTF-8');
        $id = 'filter_' . str_replace(['.', '-'], '_', $columnName);

        return <<<HTML
<input
    type="text"
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

    private function normalizeText(string $text): string
    {
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
        ];

        return strtr($text, $replacements);
    }
}
