<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    // public $message;
    public $orders;
    public $total;
    /**
     * Create a new event instance.
     */
    // public function __construct($message)
    // {
    //     $this->message = $message;
    // }

    public function __construct($orders, $total)
    {
        $this->orders = $orders;
        $this->total = $total;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('messages')
        ];
    }

    public function broadcastAs(): string
    {
        return 'newMessage';
    }

    // public function broadcastWith(): array
    // {
    //     return [
    //         'message' => $this->message
    //     ];
    // }

    public function broadcastWith(): array
    {
        return [
            'total' => $this->total,
            'orders' => $this->orders
        ];
    }
}
