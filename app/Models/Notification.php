<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'admin_id',
        'target_admin_id',
        'transaction_id',
        'read',
    ];


    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
