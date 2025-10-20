<?php

namespace App\DTO;

readonly class Endpoint
{
    /**
     * @param string $path URI completa (ej: 'landlord/admin/users/list')
     * @param string $controller FQCN del controlador
     * @param string $method Método del controlador a invocar
     * @param array $httpMethods Métodos HTTP permitidos (ej: ['GET', 'POST'])
     * @param string|null $name Nombre de la ruta (ej: 'landlord.admin.users.list')
     * @param array $middleware Middleware aplicable a esta ruta
     * @param array $where Restricciones de parámetros (ej: ['id' => '[0-9]+'])
     */
    public function __construct(
        public string $path,
        public string $controller,
        public string $method,
        public array $httpMethods,
        public ?string $name = null,
        public array $middleware = [],
        public array $where = [],
    ) {
    }

    /**
     * Obtiene el método HTTP principal (el primero del array)
     */
    public function getPrimaryHttpMethod(): string
    {
        return strtolower($this->httpMethods[0] ?? 'get');
    }

    /**
     * Verifica si soporta un método HTTP específico
     */
    public function supportsHttpMethod(string $method): bool
    {
        return in_array(strtoupper($method), $this->httpMethods, true);
    }

    /**
     * Obtiene la acción en formato Laravel (ej: 'App\Controllers\UserController@index')
     */
    public function getAction(): string
    {
        return "{$this->controller}@{$this->method}";
    }
}
