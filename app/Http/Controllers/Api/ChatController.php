<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /** Return messages from the publisher's most recent chat ticket */
    public function messages()
    {
        if (auth()->user()->status !== 'active') {
            return response()->json(['ticket_id' => null, 'status' => null, 'messages' => []]);
        }

        $ticket = SupportTicket::where('user_id', auth()->id())
            ->where('is_chat', true)
            ->latest()
            ->first();

        if (!$ticket) {
            return response()->json(['ticket_id' => null, 'status' => null, 'messages' => []]);
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

        return response()->json([
            'ticket_id' => $ticket->id,
            'status'    => $ticket->status,
            'messages'  => $messages,
        ]);
    }

    /** Send a message — creates a new chat ticket if none or last is closed */
    public function send(Request $request)
    {
        if (auth()->user()->status !== 'active') {
            return response()->json(['error' => 'Your account is not active.'], 403);
        }

        $data = $request->validate(['message' => 'required|string|max:2000']);

        $isNew  = false;
        $ticket = SupportTicket::where('user_id', auth()->id())
            ->where('is_chat', true)
            ->latest()
            ->first();

        if (!$ticket || $ticket->status === 'closed') {
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
            'ticket_id'  => $ticket->id,
            'sender'     => auth()->user()->name,
            'time'       => 'just now',
            'created_at' => $msg->created_at->toISOString(),
        ]);
    }
}
