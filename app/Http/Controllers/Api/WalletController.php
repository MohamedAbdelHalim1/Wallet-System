<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

 

    public function requestTopUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth('api')->user();
        $amount = $request->input('amount');

        $transaction = $this->walletService->createTopUpRequest($user, $amount);

        return response()->json([
            'message' => 'Top-up request sent successfully.',
            'transaction' => $transaction
        ], 201);
    }
}
