<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ReferralController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes that use the "api" middleware group
| Typically for frontend/mobile apps using tokens
|--------------------------------------------------------------------------
*/

// Public routes (No auth required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated user routes (via Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });

    // Wallet routes
    Route::prefix('wallet')->group(function () {
        Route::post('/topup/request', [WalletController::class, 'requestTopUp']);   // request with admin approval
    });

    //logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
