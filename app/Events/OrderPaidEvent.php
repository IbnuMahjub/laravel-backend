<?php

namespace App\Events;

// use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Broadcasting\PresenceChannel;
// use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderPaidEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;


    public function __construct($order)
    {
        $this->order = $order;
        Log::info('🔔 Event OrderPaidEvent dikirim ke Redis:', [
            'order' => $order
        ]);
    }

    public function broadcastOn()
    {
        return ['orders']; // nama channel
    }

    /**
     * Get the event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'order.paid'; // nama event
    }
}
