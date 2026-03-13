<?php

namespace App\Listeners;

use App\Events\NewProjectMemberEvent;
use App\Notifications\NewProjectMember;
use Illuminate\Support\Facades\Notification;

class NewProjectMemberListener
{

    /**
     * Handle the event.
     *
     * @param NewProjectMemberEvent $event
     * @return void
     */

    public function handle(NewProjectMemberEvent $event)
    {
        try {
            Notification::send($event->projectMember->user, new NewProjectMember($event->projectMember->project));
        } catch (\Throwable $th) {
            \Log::error('Error in NewProjectMemberListener: ' . $th->getMessage());
        }
    }

}
