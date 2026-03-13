<?php

namespace App\Events;

use App\Models\UserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatEvent implements ShouldBroadcast
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userChat;

    public function __construct(UserChat $userChat)
    {
        $this->userChat = $userChat;
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('user.' . $this->userChat->to);
    }

    public function broadcastAs()
    {
        return 'messages.received';
    }

}
