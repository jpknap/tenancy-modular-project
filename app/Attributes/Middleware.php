<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Middleware
{
    /**
     * @param array $middleware Middleware names (e.g., ['auth.landlord'])
     */
    public function __construct(
        public readonly array $middleware,
    ) {
    }
}
