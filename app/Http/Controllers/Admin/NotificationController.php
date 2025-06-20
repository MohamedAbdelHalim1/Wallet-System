<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $adminId = auth('admin')->id();

        $notifications = Notification::where('target_admin_id', $adminId)
            ->orderByDesc('created_at')
            ->get();

        // علمهم إنهم اتقرو
        Notification::where('target_admin_id', $adminId)
            ->where('read', false)
            ->update(['read' => true]);

        $unreadCount = Notification::where('target_admin_id', $adminId)
            ->where('read', false)
            ->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }
}
