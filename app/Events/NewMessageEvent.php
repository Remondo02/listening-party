<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $listeningPartyId;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($listeningPartyId, $message)
    {
        $this->listeningPartyId = $listeningPartyId;
        $this->message          = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('listening-party.' . $this->listeningPartyId),
        ];
    }

    public function broadcastAs()
    {
        return 'new-message';
    }
}
