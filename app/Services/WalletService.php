<?php

namespace App\Services;

use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Models\Transaction;
use App\Models\Notification;
use App\Notifications\TransactionRequestNotification;
use App\Models\Admin;

class WalletService
{
    protected $walletRepo;

    public function __construct(WalletRepositoryInterface $walletRepo)
    {
        $this->walletRepo = $walletRepo;
    }

    public function topUp($owner, $amount)
    {
        return $this->walletRepo->updateBalance($owner, $amount);
    }

    public function hold($owner, $amount)
    {
        return $this->walletRepo->holdAmount($owner, $amount);
    }

    public function releaseHold($owner, $amount)
    {
        return $this->walletRepo->releaseTempAmount($owner, $amount);
    }

    public function createTopUpRequest($user, $amount)
    {
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount' => $amount,
            'status' => 'pending',
        ]);

        Notification::create([
            'type' => 'topup_request',
            'transaction_id' => $transaction->id,
        ]);

        foreach (Admin::all() as $admin) {
            $admin->notify(new TransactionRequestNotification($transaction));
        }

        return $transaction;
    }

    public function approveTopUp(Transaction $transaction, $admin)
    {
        if ($transaction->type !== 'topup' || $transaction->status !== 'pending') {
            throw new \Exception('Invalid transaction for approval.');
        }

        $this->topUp($transaction->user, $transaction->amount);

        $transaction->status = 'approved';
        $transaction->admin_id = $admin->id;
        $transaction->save();

        return $transaction;
    }

    public function rejectTopUp(Transaction $transaction, $admin)
    {
        if ($transaction->type !== 'topup' || $transaction->status !== 'pending') {
            throw new \Exception('Invalid transaction for rejection.');
        }

        $transaction->status = 'rejected';
        $transaction->admin_id = $admin->id;
        $transaction->save();

        return $transaction;
    }

    public function createWithdrawRequest($admin, $amount)
    {
        $this->walletRepo->holdAmount($admin, $amount);

        $transaction = Transaction::create([
            'admin_id' => $admin->id,
            'type' => 'withdraw',
            'amount' => $amount,
            'status' => 'pending',
        ]);

        $reviewerAdmin = Admin::where('id', '!=', $admin->id)->first();

        if (!$reviewerAdmin) {
            throw new \Exception('No reviewer admin available.');
        }

        Notification::create([
            'type' => 'withdraw_request',
            'transaction_id' => $transaction->id,
            'admin_id' => $admin->id,              
            'target_admin_id' => $reviewerAdmin->id 
        ]);

        $reviewerAdmin->notify(new TransactionRequestNotification($transaction));

        return $transaction;
    }

    public function approveWithdraw(Transaction $transaction, $adminWhoApproved)
    {
        if ($transaction->type !== 'withdraw' || $transaction->status !== 'pending') {
            throw new \Exception('Invalid transaction.');
        }

        $this->walletRepo->releaseTempAmount($transaction->admin, $transaction->amount, true); // خصم نهائي

        $transaction->status = 'approved';
        $transaction->admin_id = $adminWhoApproved->id;
        $transaction->save();

        return $transaction;
    }

    public function rejectWithdraw(Transaction $transaction, $adminWhoRejected)
    {
        if ($transaction->type !== 'withdraw' || $transaction->status !== 'pending') {
            throw new \Exception('Invalid transaction.');
        }

        $this->walletRepo->releaseTempAmount($transaction->admin, $transaction->amount);

        $transaction->status = 'rejected';
        $transaction->admin_id = $adminWhoRejected->id;
        $transaction->save();

        return $transaction;
    }
}
