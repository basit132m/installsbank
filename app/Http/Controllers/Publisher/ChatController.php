<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /** Return messages from the publisher's most recent open ticket */
    public function messages()
    {
        $ticket = SupportTicket::where('user_id', auth()->id())
            ->whereIn('status', ['open', 'answered'])
            ->latest()
            ->first();

        if (!$ticket) {
            return response()->json(['ticket_id' => null, 'messages' => []]);
        }

        $messages = $ticket->messages()->with('sender')->orderBy('created_at')->get()
            ->map(fn($m) => [
                'id'         => $m->id,
                'message'    => $m->message,
                'is_staff'   => (bool) $m->is_staff,
                'sender'     => $m->sender?->name ?? 'Support',
                'time'       => $m->created_at->diffForHumans(),
                'created_at' => $m->created_at->toISOString(),
            ]);

        return response()->json(['ticket_id' => $ticket->id, 'messages' => $messages]);
    }

    /** Send a message — creates a ticket if none exists */
    public function send(Request $request)
    {
        $data = $request->validate(['message' => 'required|string|max:2000']);

        $ticket = SupportTicket::where('user_id', auth()->id())
            ->whereIn('status', ['open', 'answered'])
            ->latest()
            ->first();

        if (!$ticket) {
            $ticket = SupportTicket::create([
                'user_id'  => auth()->id(),
                'subject'  => 'Live Chat — ' . now()->format('M d, Y'),
                'priority' => 'medium',
                'status'   => 'open',
            ]);
        } else {
            $ticket->update(['status' => 'open', 'last_reply_at' => now()]);
        }

        $msg = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $data['message'],
            'is_staff'  => false,
        ]);

        return response()->json([
            'id'         => $msg->id,
            'message'    => $msg->message,
            'is_staff'   => false,
            'sender'     => auth()->user()->name,
            'time'       => 'just now',
            'created_at' => $msg->created_at->toISOString(),
        ]);
    }
}
