<?php

namespace App\Projects\SportCompetition\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Competition extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description', 'sport_type', 'start_date', 'end_date', 'status'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'sport_type', 'start_date', 'end_date', 'status'])
            ->logOnlyDirty();
    }
}
