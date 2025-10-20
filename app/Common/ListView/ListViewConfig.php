<?php

namespace App\Common\ListView;

/**
 * ListViewConfig
 * 
 * Configuración simplificada para vistas de listado
 * Patrón: Builder + Configuration Object
 */
class ListViewConfig
{
    /** @var ListColumn[] */
    private array $columns = [];

    /** @var ListAction[] */
    private array $actions = [];

    /** @var StatCard[] */
    private array $statCards = [];

    private int $perPage = 15;
    private string $emptyMessage = 'No hay registros para mostrar';

    /**
     * Agrega una columna al listado
     */
    public function addColumn(string $key, string $label, array $options = []): self
    {
        $this->columns[] = new ListColumn($key, $label, $options);
        return $this;
    }

    /**
     * Agrega múltiples columnas
     */
    public function columns(array $columns): self
    {
        foreach ($columns as $key => $config) {
            if (is_string($config)) {
                // Simple: ['name' => 'Nombre']
                $this->addColumn($key, $config);
            } elseif (is_array($config)) {
                // Con opciones: ['name' => ['label' => 'Nombre', 'sortable' => true]]
                $label = $config['label'] ?? $key;
                unset($config['label']);
                $this->addColumn($key, $label, $config);
            }
        }
        return $this;
    }

    /**
     * Agrega una acción (botón/enlace)
     */
    public function addAction(string $label, string $route, array $options = []): self
    {
        $this->actions[] = new ListAction($label, $route, $options);
        return $this;
    }

    /**
     * Agrega una tarjeta de estadística
     */
    public function addStatCard(string $title, mixed $value, array $options = []): self
    {
        $this->statCards[] = new StatCard($title, $value, $options);
        return $this;
    }

    /**
     * Configura la paginación
     */
    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Mensaje cuando no hay datos
     */
    public function emptyMessage(string $message): self
    {
        $this->emptyMessage = $message;
        return $this;
    }

    // Getters

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getStatCards(): array
    {
        return $this->statCards;
    }

    public function hasStatCards(): bool
    {
        return count($this->statCards) > 0;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getEmptyMessage(): string
    {
        return $this->emptyMessage;
    }
}
