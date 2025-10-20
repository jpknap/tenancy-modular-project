<?php

namespace App\Common\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TransactionService
 * 
 * Servicio para orquestar operaciones complejas con múltiples repositorios
 * Patrón: Service Layer + Unit of Work
 * 
 * Responsabilidades:
 * - Coordinar múltiples repositorios
 * - Manejar transacciones atómicas
 * - Ejecutar lógica de negocio compleja
 */
class TransactionService
{
    /**
     * Ejecuta una operación dentro de una transacción
     * Si falla cualquier parte, hace rollback automático
     * 
     * @param callable $callback Función que contiene la lógica a ejecutar
     * @return mixed Resultado de la operación
     * @throws \Throwable
     */
    public function execute(callable $callback): mixed
    {
        try {
            return DB::transaction($callback);
        } catch (\Throwable $e) {
            Log::error('Transaction failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }

    /**
     * Ejecuta múltiples operaciones en secuencia dentro de una transacción
     * 
     * @param array $operations Array de callables a ejecutar
     * @return array Resultados de cada operación
     * 
     * Ejemplo:
     * $results = $service->executeMultiple([
     *     'tenant' => fn() => $tenantRepo->create($data),
     *     'user' => fn() => $userRepo->create($userData),
     * ]);
     */
    public function executeMultiple(array $operations): array
    {
        return $this->execute(function () use ($operations) {
            $results = [];
            foreach ($operations as $key => $operation) {
                $results[$key] = $operation();
            }
            return $results;
        });
    }

    /**
     * Ejecuta operación con reintentos en caso de deadlock
     * 
     * @param callable $callback Función a ejecutar
     * @param int $maxAttempts Número máximo de intentos
     * @return mixed
     */
    public function executeWithRetry(callable $callback, int $maxAttempts = 3): mixed
    {
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $attempts++;

            try {
                return DB::transaction($callback);
            } catch (\Throwable $e) {
                if ($this->isDeadlock($e) && $attempts < $maxAttempts) {
                    Log::warning("Deadlock detected, retrying... (attempt {$attempts}/{$maxAttempts})");
                    usleep(100000 * $attempts); // Backoff exponencial
                    continue;
                }
                throw $e;
            }
        }
    }

    /**
     * Ejecuta operaciones con rollback manual si es necesario
     * 
     * @param callable $callback
     * @param callable|null $onRollback Callback a ejecutar en caso de rollback
     * @return mixed
     */
    public function executeWithRollbackHandler(callable $callback, ?callable $onRollback = null): mixed
    {
        try {
            return DB::transaction($callback);
        } catch (\Throwable $e) {
            if ($onRollback) {
                $onRollback($e);
            }
            throw $e;
        }
    }

    /**
     * Verifica si el error es un deadlock de base de datos
     */
    private function isDeadlock(\Throwable $e): bool
    {
        $message = $e->getMessage();
        return str_contains($message, 'Deadlock') ||
               str_contains($message, '1213') ||
               str_contains($message, 'deadlock');
    }
}
