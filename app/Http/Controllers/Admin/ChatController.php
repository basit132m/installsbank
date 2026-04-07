<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /** Latest unread chat message ID — for global sound polling */
    public function latestUnread()
    {
        $latest = SupportMessage::whereHas('ticket', fn($q) => $q->where('is_chat', true))
            ->where('is_staff', false)
            ->latest()
            ->value('id') ?? 0;

        return response()->json(['latest_id' => $latest]);
    }

    /** List all live chat conversations */
    public function index()
    {
        $chats = SupportTicket::with(['user', 'latestMessage'])
            ->where('is_chat', true)
            ->latest()
            ->paginate(25);

        $unreadCount = SupportTicket::where('is_chat', true)
            ->where('status', 'open')
            ->count();

        return view('admin.chat.index', compact('chats', 'unreadCount'));
    }

    /** Show a single live chat conversation */
    public function show(SupportTicket $supportTicket)
    {
        abort_unless($supportTicket->is_chat, 404);
        $supportTicket->load(['user', 'messages.sender']);

        // Mark publisher messages as read
        SupportMessage::where('ticket_id', $supportTicket->id)
            ->where('is_staff', false)
            ->update(['is_read' => true]);

        return view('admin.chat.show', compact('supportTicket'));
    }

    /** JSON endpoint for polling messages */
    public function messages(SupportTicket $supportTicket)
    {
        abort_unless($supportTicket->is_chat, 404);

        $messages = $supportTicket->messages()->with('sender')->orderBy('created_at')->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'message'     => $m->message,
                'is_staff'    => (bool) $m->is_staff,
                'sender_name' => $m->sender?->name,   // null for system messages
                'time'        => $m->created_at->diffForHumans(),
            ]);

        return response()->json(['messages' => $messages]);
    }

    /** Send a reply from admin */
    public function reply(Request $request, SupportTicket $supportTicket)
    {
        abort_unless($supportTicket->is_chat, 404);
        $data = $request->validate(['message' => 'required|string|max:2000']);

        $msg = SupportMessage::create([
            'ticket_id' => $supportTicket->id,
            'user_id'   => auth()->id(),
            'message'   => $data['message'],
            'is_staff'  => true,
        ]);

        $supportTicket->update(['status' => 'replied', 'last_reply_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json([
                'id'       => $msg->id,
                'message'  => $msg->message,
                'is_staff' => true,
                'sender'   => auth()->user()->name,
                'time'     => 'just now',
            ]);
        }

        return back()->with('success', 'Reply sent.');
    }

    /** Close a chat */
    public function close(SupportTicket $supportTicket)
    {
        abort_unless($supportTicket->is_chat, 404);

        // Insert system closure message so publisher sees it in real-time
        SupportMessage::create([
            'ticket_id' => $supportTicket->id,
            'user_id'   => null,
            'message'   => '— This chat session has been closed by support. You may start a new chat anytime. —',
            'is_staff'  => true,
        ]);

        $supportTicket->update(['status' => 'closed', 'last_reply_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'closed']);
        }

        return redirect()->route('admin.chat.index')->with('success', 'Chat closed.');
    }

    public function destroy(SupportTicket $supportTicket)
    {
        abort_unless($supportTicket->is_chat, 404);
        $supportTicket->messages()->delete();
        $supportTicket->delete();
        return redirect()->route('admin.chat.index')->with('success', 'Chat deleted.');
    }
}
