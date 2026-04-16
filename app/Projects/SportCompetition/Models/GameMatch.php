<?php

namespace App\Projects\SportCompetition\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class GameMatch extends Model
{
    use LogsActivity;

    protected $table = 'matches';

    protected $fillable = ['competition_id', 'home_team_id', 'away_team_id', 'match_date', 'venue', 'home_score', 'away_score', 'status'];

    protected $casts = [
        'match_date' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['competition_id', 'home_team_id', 'away_team_id', 'match_date', 'venue', 'home_score', 'away_score', 'status'])
            ->logOnlyDirty();
    }
}
