<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'status', 'admin_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function notification()
    {
        return $this->hasOne(\App\Models\Notification::class);
    }
}
