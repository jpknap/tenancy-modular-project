<?php

namespace App\Projects\ActivitiesBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = ['client_uuid', 'occurred_at', 'note'];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }
}
