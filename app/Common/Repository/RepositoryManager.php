<?php

namespace App\Common\Repository;

use App\Common\Repository\Contracts\RepositoryInterface;

class RepositoryManager
{
    private array $repositories = [];
    private array $instances = [];

    /**
     * Registra un repositorio
     */
    public function register(string $name, string $class): void
    {
        $this->repositories[$name] = $class;
    }

    /**
     * Obtiene una instancia del repositorio
     */
    public function get(string $name): RepositoryInterface
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (!isset($this->repositories[$name])) {
            throw new \InvalidArgumentException("Repository '{$name}' is not registered");
        }

        $this->instances[$name] = app($this->repositories[$name]);

        return $this->instances[$name];
    }

    /**
     * Verifica si existe un repositorio
     */
    public function has(string $name): bool
    {
        return isset($this->repositories[$name]);
    }

    /**
     * Acceso mágico: $manager->user
     */
    public function __get(string $name): RepositoryInterface
    {
        return $this->get($name);
    }
}
