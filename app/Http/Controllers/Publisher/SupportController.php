<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())->latest()->paginate(10);
        return view('publisher.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('publisher.support.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $data['subject'],
            'priority' => $data['priority'],
            'status' => 'open',
        ]);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $data['message'],
        ]);

        return redirect()->route('publisher.support.show', $ticket)->with('success', 'Ticket submitted.');
    }

    public function show(SupportTicket $supportTicket)
    {
        if ($supportTicket->user_id !== auth()->id()) abort(403);
        $supportTicket->load(['messages.sender']);
        return view('publisher.support.show', compact('supportTicket'));
    }

    public function reply(Request $request, SupportTicket $supportTicket)
    {
        if ($supportTicket->user_id !== auth()->id()) abort(403);
        $data = $request->validate(['message' => 'required|string|max:2000']);
        SupportMessage::create(['ticket_id' => $supportTicket->id, 'user_id' => auth()->id(), 'message' => $data['message']]);
        $supportTicket->update(['status' => 'open', 'last_reply_at' => now()]);
        return back()->with('success', 'Reply sent.');
    }
}
