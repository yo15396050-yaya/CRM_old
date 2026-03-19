<?php

namespace Modules\Sms\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Modules\Sms\Entities\SmsNotificationSetting;
use Modules\Sms\Http\Traits\WhatsappMessageTrait;
use Modules\Sms\Http\Traits\InfobipMessageTrait;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class TaskCompletedSms extends Notification implements ShouldQueue
{
    use Queueable, WhatsappMessageTrait, InfobipMessageTrait;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $task;

    private $message;

    private $smsSetting;

    private $company;

    public function __construct(Task $task)
    {
        $this->task = $task;

        $this->company = $this->task->company;
        $this->smsSetting = SmsNotificationSetting::where('slug', 'task-completed')->where('company_id', $this->company->id)->first();
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

        $this->message = "✅ " . __('email.taskComplete.subject') . "\n" . "Titre : " . $this->task->heading . "\n" . __('app.task') . ' #' . $this->task->task_short_code . "\n" . ($this->task->project ? __('app.project') . ' : ' . $this->task->project->project_name : '');

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

        if (sms_setting()->infobip_status || config('services.infobip.api_key')) {
            array_push($via, 'infobip');
        }

        return $via;
    }

    public function toTwilio($notifiable)
    {
        $settings = sms_setting();
        $priority = $settings->notification_priority ?? 'both';
        $message = __($this->smsSetting->slug->translationString(), ['heading' => $this->task->heading, 'taskId' => $this->task->id, 'project' => $this->task->project ? $this->task->project->project_name : '-']);

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
                ->variable('heading', Str::limit($this->task->heading, 27, '...'))
                ->variable('task_id', $this->task->id)
                ->variable('project', ($this->task->project ? $this->task->project->project_name : '-'));
        }
    }

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            // Optional recipient user id.
            ->to($notifiable->telegram_user_id)
            // Markdown supported.
            ->content($this->message)
            ->button(__('app.view'), route('tasks.show', $this->task->id));
    }

    public function toInfobip($notifiable)
    {
        $settings = sms_setting();
        $priority = $settings->notification_priority ?? 'both';

        $firstName = explode(' ', $notifiable->name)[0];
        $description = $this->task->description ? trim(strip_tags($this->task->description)) : 'Réalisée avec succès';
        $shortDescription = Str::limit($description, 50);
        $url = route('tasks.show', $this->task->id);
        $companyName = $this->company->company_name;

        $params = [
            'name' => $firstName,
            'heading' => $this->task->heading,
            'description' => $description,
            'url' => $url,
            'companyName' => $companyName
        ];

        $messageSms = __('sms::template.task-completed', array_merge($params, ['description' => $shortDescription]));
        $messageWhatsapp = __('sms::template.task-completed-whatsapp', $params);

        $attachments = [];
        foreach ($this->task->files as $file) {
            $attachments[] = [
                'url' => $file->file_url,
                'name' => $file->filename
            ];
        }

        if ($priority == 'whatsapp_first') {
            if ($this->sendViaInfobip($notifiable, $messageWhatsapp, 'whatsapp', $attachments)) {
                return true;
            }
        }

        if ($priority == 'sms_first') {
            if ($this->sendViaInfobip($notifiable, $messageSms, 'sms', $attachments)) {
                return true;
            }

            if ($settings->infobip_status || config('services.infobip.api_key')) {
                return $this->sendViaInfobip($notifiable, $messageWhatsapp, 'whatsapp', $attachments);
            }
        }

        if ($priority == 'both') {
            $this->sendViaInfobip($notifiable, $messageWhatsapp, 'whatsapp', $attachments);
        }

        return $this->sendViaInfobip($notifiable, $messageSms, 'sms', $attachments);
    }
}
