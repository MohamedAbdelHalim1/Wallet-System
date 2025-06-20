<?php

namespace App\Repositories\Eloquent;

use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;

class WalletRepository implements WalletRepositoryInterface
{
    public function findByOwner($owner)
    {
        return $owner->wallet ?? $owner->wallet()->create([
            'balance' => 1000,   // Just for testing i place 1000 for every initial created wallet to avoid negaitve values in temp_balace
            'temp_balance' => 0,
        ]);
    }

    public function updateBalance($owner, $amount)
    {
        $wallet = $this->findByOwner($owner);
        $wallet->balance += $amount;
        $wallet->save();
        return $wallet;
    }

    public function holdAmount($owner, $amount)
    {
        $wallet = $this->findByOwner($owner);
        if ($wallet->balance >= $amount) {
            $wallet->balance -= $amount;
            $wallet->temp_balance += $amount;
            $wallet->save();
        }
        return $wallet;
    }

    public function releaseTempAmount($owner, $amount, $finalize = false)
    {
        $wallet = $this->findByOwner($owner);

        if ($finalize) {
            $wallet->temp_balance -= $amount;
            // خلاص اتحذفت من السيستم
        } else {
            $wallet->temp_balance -= $amount;
            $wallet->balance += $amount;
        }

        $wallet->save();
        return $wallet;
    }
}
