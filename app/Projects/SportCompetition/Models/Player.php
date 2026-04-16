<?php

namespace App\Projects\SportCompetition\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Player extends Model
{
    use LogsActivity;

    protected $fillable = ['team_id', 'first_name', 'last_name', 'birth_date', 'position', 'jersey_number'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['team_id', 'first_name', 'last_name', 'birth_date', 'position', 'jersey_number'])
            ->logOnlyDirty();
    }
}
