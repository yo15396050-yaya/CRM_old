<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class WhatsAppNotification extends Notification
{
    use Queueable;
  
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable)
    {
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN')); 

        $twilio->messages->create(
            'whatsapp:+2250778941955', // Numéro du destinataire $notifiable->phone_number
            [
                'from' => env('TWILIO_WHATSAPP_NUMBER'),
                'body' => $this->message,
            ]
        );
    }
}
