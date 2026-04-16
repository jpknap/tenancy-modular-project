<?php

namespace App\Common\Audit;

use Illuminate\Database\Eloquent\Model;

class UserResolver
{
    public static function resolve(): ?Model
    {
        // En contexto tenant, el guard activo es 'web'
        if (tenancy()->initialized) {
            return auth('web')->user();
        }

        // En contexto landlord, usar el guard 'landlord'
        return auth('landlord')->user();
    }
}
