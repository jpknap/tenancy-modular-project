<?php

namespace App\Common\ListView;

/**
 * Representa un filtro en el listado
 */
class ListFilter
{
    private string $name;

    private string $label;

    private string $type;

    private array $options;

    private ?string $placeholder;

    private mixed $defaultValue;

    public function __construct(string $name, string $label, string $type, array $options = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type; // text, select, date, daterange
        $this->options = $options['choices'] ?? [];
        $this->placeholder = $options['placeholder'] ?? null;
        $this->defaultValue = $options['default'] ?? null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }
}
