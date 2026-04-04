<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->with('latestMessage')
            ->latest()
            ->paginate(20);

        return response()->json([
            'tickets'       => $tickets->items(),
            'total'         => $tickets->total(),
            'next_page_url' => $tickets->nextPageUrl(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        $ticket = SupportTicket::create([
            'user_id'      => auth()->id(),
            'subject'      => $data['subject'],
            'status'       => 'open',
            'priority'     => 'normal',
            'last_reply_at'=> now(),
        ]);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $data['message'],
            'is_staff'  => false,
        ]);

        return response()->json(['message' => 'Ticket created.', 'ticket' => $ticket], 201);
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json([
            'ticket'   => $ticket,
            'messages' => $ticket->messages,
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if ($ticket->status === 'closed') {
            return response()->json(['message' => 'This ticket is closed.'], 422);
        }

        $data = $request->validate(['message' => 'required|string|max:2000']);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $data['message'],
            'is_staff'  => false,
        ]);

        $ticket->update(['last_reply_at' => now(), 'status' => 'open']);

        return response()->json(['message' => 'Reply sent.']);
    }
}
