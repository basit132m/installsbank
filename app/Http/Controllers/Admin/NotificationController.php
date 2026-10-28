<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function index()
    {
        // Table may not be migrated yet — degrade gracefully instead of 500.
        if (!Schema::hasTable('admin_notifications')) {
            $notifications = new LengthAwarePaginator(collect(), 0, 30);
            $needsMigration = true;
            return view('admin.notifications.index', compact('notifications', 'needsMigration'));
        }

        $notifications = AdminNotification::latest()->paginate(30);

        // Mark everything read once the admin opens the list.
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        return view('admin.notifications.index', compact('notifications'));
    }
}
