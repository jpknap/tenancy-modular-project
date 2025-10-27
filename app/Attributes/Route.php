<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    /**
     * @param string $path URI path (e.g., '/api/posts/{id}')
     * @param array $methods HTTP methods (e.g., ['GET', 'POST'])
     * @param string|null $name Route name (e.g., 'posts.show')
     */
    public function __construct(
        public readonly string $path,
        public readonly array $methods,
        public readonly ?string $name = null,
    ) {
    }
}
