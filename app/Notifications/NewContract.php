<?php

namespace App\Notifications;

use App\Models\Contract;
use App\Models\GlobalSetting;
use Illuminate\Notifications\Messages\MailMessage;

class NewContract extends BaseNotification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $contract;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
        $this->company = $this->contract->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($notifiable->email_notifications && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $build = parent::build($notifiable);
        $url = url()->temporarySignedRoute('front.contract.show', now()->addDays(GlobalSetting::SIGNED_ROUTE_EXPIRY), $this->contract->hash);
        $url = getDomainSpecificUrl($url, $this->company);

        $build
            ->subject(__('email.newContract.subject'))
            ->view('mail.contract.new-contract', [
                'url' => $url,
                'notifiableName' => $notifiable->name,
                'contractType' => $this->contract->contractType->name ?? __('app.menu.contract'),
                'createdAt' => $this->contract->created_at->translatedFormat('d F Y'),
                'contractNumber' => $this->contract->formatContractNumber(),
                'supportEmail' => $this->company->company_email,
                'supportPhone' => $this->company->company_phone,
                'logo' => $this->company->logo_url,
            ]);

        parent::resetLocale();

        return $build;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
//phpcs:ignore
    public function toArray($notifiable)
    {
        return $this->contract->toArray();
    }

}
