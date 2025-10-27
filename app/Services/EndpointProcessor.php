<?php

namespace App\Services;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\DTO\Endpoint;
use ReflectionClass;
use ReflectionMethod;

class EndpointProcessor
{
    /**
     * Procesa controladores y genera endpoints desde sus atributos
     *
     * @param array $controllers Array de FQCNs de controladores
     * @param string $projectPrefix Prefijo base del proyecto
     * @return Endpoint[]
     */
    public function process(array $controllers, string $projectPrefix = ''): array
    {
        $endpoints = [];
        foreach ($controllers as $controllerClass) {
            if (! class_exists($controllerClass)) {
                continue;
            }
            $reflection = new ReflectionClass($controllerClass);

            $classPrefix = $this->getClassPrefix($reflection);
            if (empty($classPrefix) && empty($projectPrefix)) {
                continue;
            }
            $controllerEndpoints = $this->processControllerMethods(
                reflection: $reflection,
                controllerClass: $controllerClass,
                projectPrefix: $projectPrefix,
                classPrefix: $classPrefix,
            );
            $endpoints = array_merge($endpoints, $controllerEndpoints);
        }

        return $endpoints;
    }

    private function processControllerMethods(
        ReflectionClass $reflection,
        string $controllerClass,
        string $projectPrefix,
        string $classPrefix,
        array $classMiddleware = []
    ): array {
        $endpoints = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->name === '__construct') {
                continue;
            }

            /** @var Route $routeAttrs */
            $routeAttrs = $method->getAttributes(Route::class);
            if (empty($routeAttrs)) {
                continue;
            }

            foreach ($routeAttrs as $routeAttr) {
                $route = $routeAttr->newInstance();
                foreach ($route->methods as $httpMethod) {
                    $endpoints[] = $this->buildEndpoint(
                        projectPrefix: $projectPrefix,
                        classPrefix: $classPrefix,
                        route: $route,
                        controller: $controllerClass,
                        method: $method->name,
                        httpMethod: $httpMethod,
                        classMiddleware: $classMiddleware,
                        methodMiddleware: $this->getMethodMiddleware($method),
                        where: $this->getWhereConstraints($method)
                    );
                }
            }
        }

        return $endpoints;
    }

    /**
     * Obtiene prefijo de clase con herencia (padre → hijo)
     */
    private function getClassPrefix(ReflectionClass $reflection): string
    {
        $prefixes = [];
        $current = $reflection;

        while ($current) {
            $attributes = $current->getAttributes(RoutePrefix::class);
            if (! empty($attributes)) {
                $prefix = $attributes[0]->newInstance()->prefix;
                array_unshift($prefixes, $prefix);
            }
            $current = $current->getParentClass();
        }

        return implode('/', array_filter($prefixes));
    }

    /**
     * Obtiene middleware de clase con herencia
     */

    /**
     * Obtiene middleware de método
     */
    private function getMethodMiddleware(ReflectionMethod $method): array
    {
        $middleware = [];
        $attributes = $method->getAttributes(Middleware::class);

        foreach ($attributes as $attr) {
            $middleware = array_merge($middleware, $attr->newInstance()->middleware);
        }

        return $middleware;
    }

    /**
     * Obtiene restricciones Where de un método
     */
    private function getWhereConstraints(ReflectionMethod $method): array
    {
        $attributes = $method->getAttributes(Where::class);

        if (empty($attributes)) {
            return [];
        }

        return $attributes[0]->newInstance()->constraints;
    }

    /**
     * Construye un objeto Endpoint con toda la metadata combinada
     */
    private function buildEndpoint(
        string $projectPrefix,
        string $classPrefix,
        Route $route,
        string $controller,
        string $method,
        string $httpMethod,
        array $classMiddleware,
        array $methodMiddleware,
        array $where
    ): Endpoint {
        $name = str_replace('/', '.', "{$projectPrefix}.{$classPrefix}.{$route->name}");

        $pathParts = array_filter([$projectPrefix, $classPrefix, trim($route->path, '/')]);

        $fullPath = implode('/', $pathParts);

        $allMiddleware = array_unique(array_merge($classMiddleware, $methodMiddleware));

        return new Endpoint(
            path: $fullPath,
            controller: $controller,
            method: $method,
            httpMethods: [$httpMethod], // Ahora es un solo método HTTP
            name: $name,
            middleware: $allMiddleware,
            where: $where
        );
    }
}
