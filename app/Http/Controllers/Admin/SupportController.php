<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'latestMessage']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $tickets = $query->latest()->paginate(20);
        return view('admin.support.index', compact('tickets'));
    }

    public function show(SupportTicket $supportTicket)
    {
        $supportTicket->load(['user', 'messages.sender']);
        SupportMessage::where('ticket_id', $supportTicket->id)->where('user_id', '!=', auth()->id())->update(['is_read' => true]);
        return view('admin.support.show', compact('supportTicket'));
    }

    public function reply(Request $request, SupportTicket $supportTicket)
    {
        $data = $request->validate(['message' => 'required|string|max:2000']);
        SupportMessage::create(['ticket_id' => $supportTicket->id, 'user_id' => auth()->id(), 'message' => $data['message']]);
        $supportTicket->update(['status' => 'replied', 'last_reply_at' => now()]);
        return back()->with('success', 'Reply sent.');
    }

    public function close(SupportTicket $supportTicket)
    {
        $supportTicket->update(['status' => 'closed']);
        return back()->with('success', 'Ticket closed.');
    }
}
