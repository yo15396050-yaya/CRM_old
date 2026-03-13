<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance|void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toWhatsApp($notifiable);

        if (!$message) {
            return;
        }

        // Get mobile number from notifiable
        $to = $notifiable->mobile;

        if (!$to) {
            return;
        }

        // Format number to international format (assuming Ivory Coast +225 if it starts with 0)
        // Clean non-numeric characters first
        $cleanNumber = preg_replace('/[^0-9]/', '', $to);

        if (str_starts_with($cleanNumber, '0') && strlen($cleanNumber) == 10) {
            // Convert 07XXXXXXXX to +22507XXXXXXXX (Ivory Coast)
            $to = '+225' . $cleanNumber;
        }
        elseif (!str_starts_with($cleanNumber, '+')) {
            $to = '+' . $cleanNumber;
        }

        // Add whatsapp: prefix if not present
        if (!str_starts_with($to, 'whatsapp:')) {
            $to = 'whatsapp:' . $to;
        }

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN'); // Using the correct key from .env
        $from = env('TWILIO_WHATSAPP_NUMBER');

        if ($from && !str_starts_with($from, 'whatsapp:')) {
            $from = 'whatsapp:' . $from;
        }

        // Solutions for local environments (Laragon/Windows) with invalid cacert.pem path
        $options = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];
        $httpClient = new \Twilio\Http\CurlClient($options);

        $twilio = new Client($sid, $token, null, null, $httpClient);

        return $twilio->messages->create($to, [
            'from' => $from,
            'body' => $message
        ]);
    }
}
