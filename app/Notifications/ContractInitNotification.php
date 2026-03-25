<?php

namespace App\Notifications;

use App\Models\Contract;
use App\Models\GlobalSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class ContractInitNotification extends BaseNotification
{
    protected $contract;
    protected $contractId;
    protected $company;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
        $this->contractId = $contract->id;
        $this->company = $contract->company;
        
        Log::info('DEBUG ContractInitNotification :: Construct', [
            'contract_id' => $this->contractId,
            'subject' => $contract->subject,
            'is_new' => !$contract->exists
        ]);
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Rechargement frais pour éviter les données ID 0 ou obsolètes
        if ($this->contractId > 0) {
            $reloaded = Contract::withoutGlobalScopes()->with(['company', 'contractType', 'client'])->find($this->contractId);
            if ($reloaded) {
                $this->contract = $reloaded;
                $this->company = $reloaded->company;
            }
        }

        $url = url()->temporarySignedRoute('front.contract.show', now()->addDays(GlobalSetting::SIGNED_ROUTE_EXPIRY), $this->contract->hash);
        $url = getDomainSpecificUrl($url, $this->company);
        
        $date = $this->contract->created_at->translatedFormat('d F Y');
        $contractNumber = $this->contract->formatContractNumber();
        $contractTypeName = $this->contract->contractType->name ?? __('app.menu.contract');

        return $this->build($notifiable)
            ->subject('Nouveau Contrat : ' . ($this->contract->subject ?: $contractNumber))
            ->view('emails.contract_init', [
                'companyName' => $this->company->company_name,
                'logo' => $this->company->logo_url,
                'headerColor' => $this->company->header_color,
                'recipientName' => $notifiable->name,
                'subject' => $this->contract->subject ?: 'Contrat de service',
                'contractType' => $contractTypeName,
                'date' => $date,
                'contractNumber' => $contractNumber,
                'url' => $url,
                'company' => $this->company,
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'id' => $this->contract->id,
            'subject' => $this->contract->subject,
            'type' => 'contract_init',
            'created_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Contenu spécifique pour WhatsApp/SMS
     */
    public function toWhatsAppContent($notifiable)
    {
        $contractNumber = $this->contract->formatContractNumber();
        $url = url()->temporarySignedRoute('front.contract.show', now()->addDays(GlobalSetting::SIGNED_ROUTE_EXPIRY), $this->contract->hash);
        $url = getDomainSpecificUrl($url, $this->company);
        
        return "📄 *Nouveau Contrat* : {$this->contract->subject}\n" .
               "Référence : {$contractNumber}\n\n" .
               "Bonjour {$notifiable->name}, votre contrat est prêt. Vous pouvez le signer ici :\n" .
               "🔗 {$url}";
    }

    public function toSmsContent()
    {
        $contractNumber = $this->contract->formatContractNumber();
        return "Nouv. Contrat: {$this->contract->subject}\nRef: {$contractNumber}\nConsultez vos emails.";
    }
}
