<?php

namespace App\Projects\ActivitiesBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description', 'color', 'icon', 'position', 'archived_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nombrada `activityItems()` (no `activities()`) por la misma razón que
     * `User::activityItems()`: LogsActivity ya define `activities()`.
     */
    public function activityItems(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'category_activity');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'color', 'icon', 'position', 'archived_at'])
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'archived_at' => 'datetime',
        ];
    }
}
