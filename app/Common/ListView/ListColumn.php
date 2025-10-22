<?php

namespace App\Common\ListView;

/**
 * Representa una columna en el listado
 */
class ListColumn
{
    private string $key;

    private string $label;

    private bool $sortable;

    private bool $searchable;

    private ?string $format;

    private ?\Closure $formatter;

    private ?string $class;

    private ?string $headerClass;

    private bool $visible;

    public function __construct(string $key, string $label, array $options = [])
    {
        $this->key = $key;
        $this->label = $label;
        $this->sortable = $options['sortable'] ?? false;
        $this->searchable = $options['searchable'] ?? false;
        $this->format = $options['format'] ?? null;
        $this->formatter = $options['formatter'] ?? null;
        $this->class = $options['class'] ?? null;
        $this->headerClass = $options['header_class'] ?? null;
        $this->visible = $options['visible'] ?? true;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function hasFormatter(): bool
    {
        return $this->formatter !== null;
    }

    public function format($value)
    {
        if ($this->formatter) {
            return ($this->formatter)($value);
        }

        return match ($this->format) {
            'date' => $value ? date('d/m/Y', strtotime($value)) : '-',
            'datetime' => $value ? date('d/m/Y H:i', strtotime($value)) : '-',
            'currency' => '$' . number_format($value, 2),
            'boolean' => $value ? 'Sí' : 'No',
            'badge' => $this->formatBadge($value),
            default => $value ?? '-'
        };
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getHeaderClass(): ?string
    {
        return $this->headerClass;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    private function formatBadge($value): string
    {
        $badges = [
            'active' => '<span class="badge bg-success">Activo</span>',
            'inactive' => '<span class="badge bg-secondary">Inactivo</span>',
            'pending' => '<span class="badge bg-warning">Pendiente</span>',
            'completed' => '<span class="badge bg-primary">Completado</span>',
        ];

        return $badges[$value] ?? "<span class=\"badge bg-secondary\">{$value}</span>";
    }
}
