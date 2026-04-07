<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /** Return messages from the publisher's most recent open chat ticket */
    public function messages()
    {
        // Only approved publishers
        if (auth()->user()->status !== 'active') {
            return response()->json(['ticket_id' => null, 'messages' => []]);
        }

        $ticket = SupportTicket::where('user_id', auth()->id())
            ->where('is_chat', true)
            ->whereIn('status', ['open', 'answered', 'replied'])
            ->latest()
            ->first();

        if (!$ticket) {
            return response()->json(['ticket_id' => null, 'messages' => []]);
        }

        $messages = $ticket->messages()->orderBy('created_at')->get()
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

    /** Send a message — creates a chat ticket if none exists */
    public function send(Request $request)
    {
        // Only approved publishers
        if (auth()->user()->status !== 'active') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate(['message' => 'required|string|max:2000']);

        $isNew = false;
        $ticket = SupportTicket::where('user_id', auth()->id())
            ->where('is_chat', true)
            ->whereIn('status', ['open', 'answered', 'replied'])
            ->latest()
            ->first();

        if (!$ticket) {
            $ticket = SupportTicket::create([
                'user_id'  => auth()->id(),
                'subject'  => 'Live Chat — ' . now()->format('M d, Y'),
                'priority' => 'medium',
                'status'   => 'open',
                'is_chat'  => true,
            ]);
            $isNew = true;
        } else {
            $ticket->update(['status' => 'open', 'last_reply_at' => now()]);
        }

        $msg = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $data['message'],
            'is_staff'  => false,
        ]);

        // Auto-reply on first message of a new chat
        if ($isNew) {
            SupportMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => null,
                'message'   => 'Hello, Hope you will be fine. The team will get back to you soon. To prevent any further delay consider adding any details for the team.',
                'is_staff'  => true,
            ]);
        }

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
