<?php

namespace Modules\Sms\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Modules\Sms\Entities\SmsNotificationSetting;
use Modules\Sms\Http\Traits\WhatsappMessageTrait;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class OrderUpdated extends Notification implements ShouldQueue
{
    use Queueable, WhatsappMessageTrait;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $order;

    private $smsSetting;

    private $message;

    private $company;

    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->company = $this->order->company;
        $this->smsSetting = SmsNotificationSetting::where('slug', 'order-updated')->where('company_id', $this->company->id)->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->smsSetting && $this->smsSetting->send_sms != 'yes') {
            return [];
        }

        $this->message = __('email.order.updateSubject') . "\n" . __('modules.orders.orderNumber') . ': ' . $this->order->order_number;

        $via = [];

        if (!is_null($notifiable->mobile) && !is_null($notifiable->country_id)) {
            if (sms_setting()->status) {
                array_push($via, TwilioChannel::class);
            }

            if (sms_setting()->nexmo_status) {
                array_push($via, 'vonage');
            }

            if ($this->smsSetting->msg91_flow_id && sms_setting()->msg91_status) {
                array_push($via, 'msg91');
            }
        }

        if (sms_setting()->telegram_status && $notifiable->telegram_user_id) {
            array_push($via, 'telegram');
        }

        return $via;
    }

    public function toTwilio($notifiable)
    {
        $settings = sms_setting();
        $priority = $settings->notification_priority ?? 'both';
        $message = __($this->smsSetting->slug->translationString(), ['orderNumber' => $this->order->order_number]);

        if ($priority == 'whatsapp_first') {
            if ($this->toWhatsapp($notifiable, $message)) {
                return null;
            }
        }

        if ($priority == 'sms_first') {
            if ($this->toSms($notifiable, $this->message)) {
                return null;
            }

            if ($settings->whatsapp_status) {
                $this->toWhatsapp($notifiable, $message);
                return null;
            }
        }

        if ($priority == 'both') {
            $this->toWhatsapp($notifiable, $message);
        }

        if ($settings->status) {
            return (new TwilioSmsMessage())
                ->content($this->message);
        }

        return null;
    }

    //phpcs:ignore
    public function toVonage($notifiable)
    {

        if (sms_setting()->nexmo_status) {
            return (new VonageMessage)
                ->content($this->message)->unicode();
        }
    }

    //phpcs:ignore
    public function toMsg91($notifiable)
    {
        if ($this->smsSetting->msg91_flow_id && sms_setting()->msg91_status) {
            return (new \Craftsys\Notifications\Messages\Msg91SMS)
                ->flow($this->smsSetting->msg91_flow_id)
                ->variable('order_number', $this->order->order_number);
        }
    }

    public function toTelegram($notifiable)
    {

        return TelegramMessage::create()
            // Optional recipient user id.
            ->to($notifiable->telegram_user_id)
            // Markdown supported.
            ->content($this->message)
            ->button(__('email.order.action'), route('orders.show', $this->order->id));
    }
}
