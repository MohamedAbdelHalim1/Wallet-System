<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    protected $fillable = ['code', 'generated_by', 'used_by'];

    public function generator()
    {
        return $this->belongsTo(Admin::class, 'generated_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
