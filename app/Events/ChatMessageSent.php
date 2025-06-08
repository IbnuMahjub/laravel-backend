<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomChat;
    public $authId;
    public $incomingId;

    public function __construct($roomChat, $authId, $incomingId)
    {
        $this->roomChat = $roomChat;
        $this->authId = $authId;
        $this->incomingId = $incomingId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('room-chat.' . $this->authId . '.' . $this->incomingId);
    }

    public function broadcastWith()
    {
        return [
            'data' => $this->roomChat
        ];
    }
}
