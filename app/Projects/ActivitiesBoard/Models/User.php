<?php

namespace App\Projects\ActivitiesBoard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    protected $fillable = ['name', 'email', 'password', 'timezone'];

    protected $hidden = ['password', 'remember_token'];

    public function canImpersonate(): bool
    {
        if (isset($this->is_system_user) && $this->is_system_user) {
            return true;
        }

        return $this->hasRole('superadmin') || $this->hasPermissionTo('users:impersonate');
    }

    public function canBeImpersonated(): bool
    {
        return ! (isset($this->is_system_user) && $this->is_system_user)
            && ! $this->hasRole('superadmin');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'timezone', 'locale', 'is_system_user'])
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_system_user' => 'boolean',
        ];
    }
}
