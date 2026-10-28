<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::latest()->paginate(30);

        // Mark everything read once the admin opens the list.
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        return view('admin.notifications.index', compact('notifications'));
    }
}
