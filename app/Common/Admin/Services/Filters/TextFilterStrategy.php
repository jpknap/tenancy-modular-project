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

    public function getType(): string
    {
        return 'text';
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
