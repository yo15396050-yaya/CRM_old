<?php

namespace Modules\Sms\Http\Traits;

trait WhatsappMessageTrait
{
    public function toWhatsapp($notifiable, $message)
    {
        $settings = sms_setting();

        if (!$settings->whatsapp_status) {
            return false;
        }

        try {
            $toNumber = '+' . $notifiable->country->phonecode . $notifiable->mobile;
            $fromNumber = $settings->whatapp_from_number;

            if (config('app.env') === 'local') {
                $curlClient = new \Twilio\Http\CurlClient([
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]);
                $twilio = new \Twilio\Rest\Client($settings->account_sid, $settings->auth_token, null, null, $curlClient);
            }
            else {
                $twilio = new \Twilio\Rest\Client($settings->account_sid, $settings->auth_token);
            }


            $twilio->messages->create(
                'whatsapp:' . $toNumber,
            [
                'from' => 'whatsapp:' . $fromNumber,
                'body' => $message,
            ]
            );

            return true;
        }
        catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function toSms($notifiable, $message)
    {
        $settings = sms_setting();

        if (!$settings->status) {
            return false;
        }

        try {
            $toNumber = '+' . $notifiable->country->phonecode . $notifiable->mobile;
            $fromNumber = $settings->from_number;

            if (config('app.env') === 'local') {
                $curlClient = new \Twilio\Http\CurlClient([
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]);
                $twilio = new \Twilio\Rest\Client($settings->account_sid, $settings->auth_token, null, null, $curlClient);
            }
            else {
                $twilio = new \Twilio\Rest\Client($settings->account_sid, $settings->auth_token);
            }


            $twilio->messages->create(
                $toNumber,
            [
                'from' => $fromNumber,
                'body' => $message,
            ]
            );

            return true;
        }
        catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
