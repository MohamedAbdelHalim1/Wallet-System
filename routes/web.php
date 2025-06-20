<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\Mail;


// Route::get('/test-mail', function () {
//     Mail::raw('Test Mail', function ($message) {
//         $message->to('admin@example.com')->subject('Test Mail');
//     });

//     return 'Email sent successfully';
// });
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| هنا بنسجل راوتات الويب، سواء عامة أو للإدارة، مع الميدلوير وViews.
|
*/

// راوت الصفحة الرئيسية - يعمل تشيك لو الأدمن عامل تسجيل دخول
Route::get('/', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.transactions');
    }
    return redirect()->route('admin.login');
});

// راوت تسجيل الدخول - عرض الفورم
Route::get('/admin/login', function () {
    return view('admin.auth.login');
})->name('admin.login');

// راوت تسجيل الدخول - إرسال البيانات
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');

// راوتات لوحة تحكم الأدمن - محمية بـ auth:admin
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions');
    Route::post('/transactions/{id}/approve', [TransactionController::class, 'approve']);
    Route::post('/transactions/{id}/reject', [TransactionController::class, 'reject']);
    Route::post('/transactions/withdraw/request', [TransactionController::class, 'requestWithdraw']);
    Route::post('/transactions/withdraw/{id}/approve', [TransactionController::class, 'approveWithdraw']);
    Route::post('/transactions/withdraw/{id}/reject', [TransactionController::class, 'rejectWithdraw']);
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications');
    Route::post('/referrals/generate', [ReferralController::class, 'generate'])->name('admin.referrals.generate');
    Route::get('/referrals/list', [ReferralController::class, 'list'])->name('admin.referrals.list');
    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
});
