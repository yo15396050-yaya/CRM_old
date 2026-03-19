<?php

namespace Modules\Sms\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Modules\Sms\Http\Traits\InfobipMessageTrait;

class InfobipChannel
{
    use InfobipMessageTrait;

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toInfobip($notifiable);

        if (!$data) {
            return;
        }

        $message = is_array($data) ? $data['body'] : $data;
        $type = is_array($data) ? ($data['type'] ?? 'sms') : 'sms';
        $attachments = is_array($data) ? ($data['attachments'] ?? []) : [];

        $this->sendViaInfobip($notifiable, $message, $type, $attachments);
    }
}
