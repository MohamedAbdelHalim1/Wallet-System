<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'temp_balance'];

    public function walletable()   //to make it fit for users and admins table
    {
        return $this->morphTo();
    }
}
