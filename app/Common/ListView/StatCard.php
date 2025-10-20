<?php

namespace App\Common\ListView;

/**
 * StatCard
 *
 * Representa una tarjeta de estadística en el listado
 */
class StatCard
{
    private string $title;
    private mixed $value;
    private string $icon;
    private string $color;
    private ?\Closure $valueResolver;

    public function __construct(string $title, mixed $value, array $options = [])
    {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $options['icon'] ?? 'bi-info-circle';
        $this->color = $options['color'] ?? 'primary';
        $this->valueResolver = $options['value_resolver'] ?? null;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getValue($items = null): mixed
    {
        // Si hay un resolver, usarlo
        if ($this->valueResolver && $items !== null) {
            return ($this->valueResolver)($items);
        }

        return $this->value;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getColorClass(): string
    {
        return match($this->color) {
            'primary' => 'bg-primary',
            'success' => 'bg-success',
            'warning' => 'bg-warning',
            'danger' => 'bg-danger',
            'info' => 'bg-info',
            'secondary' => 'bg-secondary',
            default => 'bg-primary'
        };
    }

    public function getTextColorClass(): string
    {
        return match($this->color) {
            'primary' => 'text-primary',
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'info' => 'text-info',
            'secondary' => 'text-secondary',
            default => 'text-primary'
        };
    }
}
