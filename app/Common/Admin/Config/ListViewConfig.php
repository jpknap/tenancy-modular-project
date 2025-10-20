<?php

namespace App\Common\Admin\Config;

use App\Common\ListView\ListAction;
use App\Common\ListView\ListColumn;
use App\Common\ListView\StatCard;

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
                $this->addColumn($key, $config);
            } elseif (is_array($config)) {
                $label = $config['label'] ?? $key;
                unset($config['label']);
                $this->addColumn($key, $label, $config);
            }
        }
        return $this;
    }
    public function addAction(string $label, string $route, array $options = []): self
    {
        $this->actions[] = new ListAction($label, $route, $options);
        return $this;
    }
    public function addStatCard(string $title, mixed $value, array $options = []): self
    {
        $this->statCards[] = new StatCard($title, $value, $options);
        return $this;
    }

    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }
    public function emptyMessage(string $message): self
    {
        $this->emptyMessage = $message;
        return $this;
    }

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
