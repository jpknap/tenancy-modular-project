<?php

namespace App\Projects\ActivitiesBoard\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];
}
