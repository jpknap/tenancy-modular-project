<?php

namespace App\Common\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface CriteriaInterface
 * 
 * Contrato para implementar el patrón Specification
 * Permite aplicar filtros y condiciones de forma reutilizable
 */
interface CriteriaInterface
{
    /**
     * Aplica el criterio al query builder
     */
    public function apply(Builder $query): Builder;
}
