<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Services\WalletService;

class TransactionController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index()
    {
        $adminId = auth('admin')->id();

        $pendingTopups = Transaction::where('status', 'pending')
            ->where(function ($query) use ($adminId) {
                $query->where('type', 'topup')
                    ->orWhere(function ($q) use ($adminId) {
                        $q->where('type', 'withdraw')
                            ->whereHas('notification', function ($q2) use ($adminId) {
                                $q2->where('target_admin_id', $adminId);
                            });
                    });
            })
            ->latest()
            ->get();

        $unreadCount = Notification::where('target_admin_id', $adminId)
            ->where('read', false)
            ->count();

        return view('admin.transactions.index', compact('pendingTopups', 'unreadCount'));
    }

    public function approve($id)
    {
        $admin = auth('admin')->user()->load('permissions');

        //take a look in permissions seeder i made amin1 has all permissions while admin2 has only withdraw permissions
        
        if (!$admin->hasPermission('can_accept_topup')) {
            abort(403, 'Unauthorized');
        }
        $transaction = Transaction::findOrFail($id);

        try {
            $this->walletService->approveTopUp($transaction, $admin);
            session()->flash('message', 'Top-up approved successfully.');
            return redirect()->route('admin.transactions');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function reject($id)
    {
        $admin = auth('admin')->user()->load('permissions');

        if (!$admin->hasPermission('can_reject_topup')) {
            abort(403, 'Unauthorized');
        }

        $transaction = Transaction::findOrFail($id);

        try {
            $this->walletService->rejectTopUp($transaction, $admin);
            session()->flash('message', 'Top-up rejected successfully.');
            return redirect()->route('admin.transactions');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function requestWithdraw(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        $admin = auth('admin')->user();

        try {
            $transaction = $this->walletService->createWithdrawRequest($admin, $request->amount);
            session()->flash('message', 'Withdraw request created successfully.');
            return redirect()->route('admin.transactions');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function approveWithdraw($id)
    {
        $admin = auth('admin')->user()->load('permissions');
        if (!$admin->hasPermission('can_accept_withdrawals')) {
            abort(403, 'Unauthorized');
        }
        $transaction = Transaction::findOrFail($id);

        try {
            $this->walletService->approveWithdraw($transaction, $admin);
            session()->flash('message', 'Withdraw approved successfully.');
            return redirect()->route('admin.transactions');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('admin.transactions');
        }
    }

    public function rejectWithdraw($id)
    {
        $admin = auth('admin')->user()->load('permissions');
        if (!$admin->hasPermission('can_reject_withdrawals')) {
            abort(403, 'Unauthorized');
        }
        $transaction = Transaction::findOrFail($id);

        try {
            $this->walletService->rejectWithdraw($transaction, $admin);
            session()->flash('message', 'Withdraw rejected successfully.');
            return redirect()->route('admin.transactions');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('admin.transactions');
        }
    }
}
