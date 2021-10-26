<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Transfers implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transferData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($transferData)
    {
        $this->transferData = $transferData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
//        return ['transfer-channel'];
        return new Channel('transfer');
    }

    public function broadcastAs()
    {
        return 'Transfers';
    }
}
