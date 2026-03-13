<?php

namespace App\Listeners;

use App\Events\NewChatEvent;
use App\Models\User;
use App\Notifications\NewChat;
use App\Scopes\ActiveScope;
use Illuminate\Support\Facades\Notification;

class NewChatListener
{

    /**
     * Handle the event.
     *
     * @param NewChatEvent $event
     * @return void
     */

    public function handle(NewChatEvent $event)
    {
        $userId = $event->userChat->user_id ?? $event->userChat->to;

        if ($userId) {
            $notifyUser = User::withoutGlobalScope(ActiveScope::class)->find($userId);

            if ($notifyUser) {
                $notification = new NewChat($event->userChat);
                $notification->skipEmail = $event->userChat->skip_new_chat_email;
                Notification::send($notifyUser, $notification);
            }
        }
    }

}
