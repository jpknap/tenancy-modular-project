<?php

namespace App\Projects\TicketsValdi\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Organización que publica eventos y vende entradas. Ver docs/DOMAIN.md.
 *
 * Las relaciones hacia events/coupons/orders se agregan cuando existan esos
 * modelos; no se declaran de antemano para no referenciar clases inexistentes.
 */
class Institution extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description', 'logo_url', 'enabled'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'logo_url', 'enabled'])
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }
}
