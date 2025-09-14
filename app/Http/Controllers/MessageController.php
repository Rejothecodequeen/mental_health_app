<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent; // Correct event import

class MessageController extends Controller
{
    // Show chat page with users and optional receiver
    public function chat($receiverId = null)
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $receiver = $receiverId ? User::findOrFail($receiverId) : null;

        return view('chat', compact('users', 'receiver'));
    }

    // Send a message and broadcast it
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string',
        ]);

        // Save the message
        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
        ]);

        // Broadcast to receiver on private channel
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    // Fetch all messages between logged-in user and receiver
    public function fetch($receiverId)
    {
        return Message::where(function ($q) use ($receiverId) {
            $q->where('sender_id', Auth::id())
              ->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($receiverId) {
            $q->where('sender_id', $receiverId)
              ->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();
    }
}
