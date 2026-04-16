<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;
    use LogsActivity;

    public $incrementing = true;

    protected $connection = 'pgsql';

    protected $fillable = ['name', 'identifier', 'current_project', 'timezone', 'locale', 'data'];

    protected $casts = [
        'data' => 'array',
    ];

    public static function getCustomColumns(): array
    {
        return ['id', 'name', 'identifier', 'current_project', 'timezone', 'locale'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'identifier', 'current_project', 'timezone', 'locale', 'data'])
            ->logOnlyDirty();
    }
}
