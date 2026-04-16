<?php

namespace App\Projects\SportCompetition\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Team extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'code', 'description', 'logo_url'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'description', 'logo_url'])
            ->logOnlyDirty();
    }
}
