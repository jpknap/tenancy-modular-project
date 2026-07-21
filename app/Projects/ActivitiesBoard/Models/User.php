<?php

namespace App\Projects\ActivitiesBoard\Models;

use App\Models\User as BaseUser;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends BaseUser
{
    /**
     * Nombrada distinto de `activities()` porque ese nombre ya lo define
     * Spatie\Activitylog\Traits\LogsActivity (heredado de BaseUser) como
     * MorphMany hacia su propio modelo de auditoría.
     */
    public function activityItems(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function categoryItems(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
