<?php

namespace App\Services;

use App\Models\ReferralCode;
use App\Models\User;
use Illuminate\Support\Str;

class ReferralService
{
    public function generateCode($owner)
    {
        return ReferralCode::create([
            'code' => strtoupper(Str::random(8)),
            'generated_by' => $owner->id,
        ]);
    }

}
