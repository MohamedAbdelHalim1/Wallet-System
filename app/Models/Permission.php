<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name'];

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_permission', 'permission_id', 'admin_id');
    }
}
