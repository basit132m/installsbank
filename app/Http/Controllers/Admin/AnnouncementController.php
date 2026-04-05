<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:info,warning,danger,success',
            'message' => 'required|string|max:1000',
        ]);

        Announcement::create([
            'created_by' => auth()->id(),
            'type'       => $request->type,
            'message'    => $request->message,
            'is_active'  => true,
        ]);

        return back()->with('success', 'Announcement published.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        return back()->with('success', $announcement->is_active ? 'Announcement shown.' : 'Announcement hidden.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
