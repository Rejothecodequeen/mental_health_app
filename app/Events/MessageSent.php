<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    // Broadcast to both users involved in the chat
    public function broadcastOn()
    {
        $sender   = $this->message->sender_id;
        $receiver = $this->message->receiver_id;

        return [
            new PrivateChannel("chat." . $sender . "." . $receiver),
            new PrivateChannel("chat." . $receiver . "." . $sender),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id'         => $this->message->id,
            'sender_id'  => $this->message->sender_id,
            'receiver_id'=> $this->message->receiver_id,
            'message'    => $this->message->message,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}
