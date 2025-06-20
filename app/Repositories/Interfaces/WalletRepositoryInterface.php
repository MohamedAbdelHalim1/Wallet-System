<?php

namespace App\Repositories\Interfaces;

interface WalletRepositoryInterface
{
    public function findByOwner($owner);
    public function updateBalance($userId, $amount);
    public function holdAmount($userId, $amount);
    public function releaseTempAmount($userId, $amount);
}
