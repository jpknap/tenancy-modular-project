<?php

namespace App\Projects\ActivitiesBoard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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
}
