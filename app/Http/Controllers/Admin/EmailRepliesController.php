<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailReply;
use Illuminate\Http\Request;

class EmailRepliesController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all | unread

        $query = EmailReply::query()->latest('received_at');

        if ($filter === 'unread') {
            $query->where('is_read', false);
        }

        $replies     = $query->paginate(30)->withQueryString();
        $unreadCount = EmailReply::where('is_read', false)->count();

        return view('admin.email-replies.index', compact('replies', 'unreadCount', 'filter'));
    }

    public function show(EmailReply $emailReply)
    {
        if (!$emailReply->is_read) {
            $emailReply->update(['is_read' => true]);
        }

        $unreadCount = EmailReply::where('is_read', false)->count();

        return view('admin.email-replies.show', compact('emailReply', 'unreadCount'));
    }

    public function markAllRead()
    {
        EmailReply::where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All replies marked as read.');
    }

    public function destroy(EmailReply $emailReply)
    {
        $emailReply->delete();
        return redirect()->route('admin.email-replies.index')->with('success', 'Email deleted.');
    }

    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            EmailReply::whereIn('id', $ids)->delete();
        }
        return back()->with('success', count($ids) . ' email(s) deleted.');
    }
}
