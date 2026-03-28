<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Where
{
    /**
     * @param array $constraints Route parameter constraints (e.g., ['id' => '[0-9]+'])
     */
    public function __construct(
        public readonly array $constraints,
    ) {
    }
}
