<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralCode;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    // ✅ POST /admin/referrals/generate
    public function generate(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        try {
            $code = $this->referralService->generateCode($admin);
            return response()->json(['message' => 'Referral code generated.', 'code' => $code]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // ✅ GET /admin/referrals/list
    public function list()
    {
        $admin = Auth::guard('admin')->user();

        $codes = ReferralCode::where('generated_by', $admin->id)->with('user')->latest()->get();

        return response()->json([
            'codes' => $codes->map(function ($code) {
                return [
                    'code' => $code->code,
                    'used_by' => $code->used_by ? ($code->user->name ?? 'N/A') : null,
                ];
            }),
        ]);
    }
}
