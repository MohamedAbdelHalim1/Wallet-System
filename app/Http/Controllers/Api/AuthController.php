<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ReferralCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'referral_code' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ⬇️ Handle referral code logic
        if ($request->filled('referral_code')) {
            $code = ReferralCode::where('code', $request->referral_code)->first();

            if (!$code) {
                return response()->json(['error' => 'Invalid referral code.'], 400);
            }

            if ($code->used_by) {
                return response()->json(['error' => 'Referral code has already been used.'], 400);
            }

            // سجل الاستخدام
            $code->used_by = $user->id;
            $code->save();

            // Add 10 EGP to both wallets
            $userWallet = $user->wallet()->create([
                'balance' => 10,
                'temp_balance' => 0,
            ]);

            $admin = Admin::find($code->generated_by);
            if ($admin) {
                $adminWallet = $admin->wallet()->firstOrCreate([], ['balance' => 0, 'temp_balance' => 0]);
                $adminWallet->increment('balance', 10);
            }
        } else {
            // بدون referral → محفظة ببداية 0
            $user->wallet()->create([
                'balance' => 0,
                'temp_balance' => 0,
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'token' => $token,
            'user' => $user
        ]);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
