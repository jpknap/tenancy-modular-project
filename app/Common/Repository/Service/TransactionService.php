<?php

namespace App\Common\Repository\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Empaquedador de transacciones
 */
class TransactionService
{
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
     * @param callable|null $onRollback Callback a ejecutar en caso de rollback
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
