<?php

namespace App\Common\Admin\models\ListView;

/**
 * Representa una acción (botón/enlace) en el listado
 */
class ListAction
{
    private string $label;

    private string $route;

    private string $type;

    private string $icon;

    private string $class;

    private bool $requiresConfirmation;

    private ?string $confirmMessage;

    private array $routeParams;

    public function __construct(string $label, string $route, array $options = [])
    {
        $this->label = $label;
        $this->route = $route;
        $this->type = $options['type'] ?? 'link'; // link, button, form
        $this->icon = $options['icon'] ?? '';
        $this->class = $options['class'] ?? 'btn btn-sm btn-primary';
        $this->requiresConfirmation = $options['confirm'] ?? false;
        $this->confirmMessage = $options['confirm_message'] ?? '¿Está seguro?';
        $this->routeParams = $options['route_params'] ?? [];
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function requiresConfirmation(): bool
    {
        return $this->requiresConfirmation;
    }

    public function getConfirmMessage(): ?string
    {
        return $this->confirmMessage;
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function getUrl($item): string
    {
        $params = [];
        foreach ($this->routeParams as $key => $value) {
            // Si el valor es una función, ejecutarla con el item
            if (is_callable($value)) {
                $params[$key] = $value($item);
            } else {
                // Si es un string, buscar en el item
                $params[$key] = data_get($item, $value);
            }
        }

        return route($this->route, $params);
    }
}
