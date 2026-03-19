<?php

namespace Modules\Sms\Http\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait InfobipMessageTrait
{
    public function sendViaInfobip($notifiable, $message, $type = 'sms', $attachments = [])
    {
        $settings = sms_setting();

        $apiKey = config('services.infobip.api_key') ?: $settings->infobip_api_key;
        $baseUrl = config('services.infobip.base_url') ?: $settings->infobip_base_url;
        $smsFrom = config('services.infobip.sms_from') ?: $settings->infobip_from_number;
        $whatsappFrom = config('services.infobip.whatsapp_from') ?: $settings->infobip_whatsapp_number;

        if (!$apiKey || !$baseUrl) {
            return false;
        }

        // Ensure baseUrl is just the domain or has https
        if (!str_starts_with($baseUrl, 'http')) {
            $baseUrl = 'https://' . $baseUrl;
        }

        try {
            $toNumber = $notifiable->country->phonecode . $notifiable->mobile;
            Log::info('Infobip request start', ['to' => $toNumber, 'type' => $type]);

            if ($type == 'whatsapp') {
                if (!empty($attachments)) {
                    foreach ($attachments as $file) {
                        $this->sendInfobipWhatsappDocument($baseUrl, $apiKey, $whatsappFrom, $toNumber, $file['url'], $file['name']);
                    }
                }
                return $this->sendInfobipWhatsapp($baseUrl, $apiKey, $whatsappFrom, $toNumber, $message);
            }

            // For SMS, append file links to the message
            if (!empty($attachments)) {
                $message .= "\n\nFichiers :";
                foreach ($attachments as $file) {
                    $message .= "\n" . $file['url'];
                }
            }

            return $this->sendInfobipSms($baseUrl, $apiKey, $smsFrom, $toNumber, $message);

        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    private function httpClient()
    {
        $options = [
            'verify' => false,
        ];

        // Fix for cURL error 77 on some Windows/Laragon environments
        // We force a valid path if available, or empty to avoid the "error setting certificate file"
        $cacertPath = 'C:\laragon\etc\ssl\cacert.pem';
        if (file_exists($cacertPath)) {
            $options['curl'] = [
                CURLOPT_CAINFO => $cacertPath,
                CURLOPT_CAPATH => dirname($cacertPath),
            ];
        } else {
            $options['curl'] = [
                CURLOPT_CAINFO => '',
                CURLOPT_CAPATH => '',
            ];
        }

        return Http::withOptions($options);
    }

    private function sendInfobipWhatsappDocument($baseUrl, $apiKey, $from, $to, $mediaUrl, $caption = '')
    {
        $response = $this->httpClient()->withHeaders([
            'Authorization' => 'App ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($baseUrl . '/whatsapp/1/message/document', [
            'from' => $from,
            'to' => $to,
            'content' => [
                'mediaUrl' => $mediaUrl,
                'caption' => $caption ?: 'Document',
            ],
        ]);

        Log::info('Infobip Whatsapp Document Response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        return $response->successful();
    }

    private function sendInfobipSms($baseUrl, $apiKey, $from, $to, $message)
    {
        $response = $this->httpClient()->withHeaders([
            'Authorization' => 'App ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($baseUrl . '/sms/2/text/advanced', [
            'messages' => [
                [
                    'from' => $from,
                    'destinations' => [['to' => $to]],
                    'text' => $message,
                ],
            ],
        ]);

        Log::info('Infobip SMS Response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        return $response->successful();
    }

    private function sendInfobipWhatsapp($baseUrl, $apiKey, $from, $to, $message)
    {
        $response = $this->httpClient()->withHeaders([
            'Authorization' => 'App ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($baseUrl . '/whatsapp/1/message/text', [
            'from' => $from,
            'to' => $to,
            'content' => [
                'text' => $message,
            ],
        ]);

        Log::info('Infobip Whatsapp Response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        return $response->successful();
    }
}
