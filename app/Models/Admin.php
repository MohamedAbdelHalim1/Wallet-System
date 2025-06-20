<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = ['name', 'email', 'password'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'admin_permission', 'admin_id', 'permission_id');
    }
    public function hasPermission($permName)
    {
        return $this->permissions->contains('name', $permName);
    }

    public function wallet()
    {
        return $this->morphOne(Wallet::class, 'walletable');
    }

    public function codes()
    {
        return $this->hasMany(ReferralCode::class);
    }
}
