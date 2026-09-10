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
        
        // La locale n'est posee que par build(), appele plus bas : sans la fixer
        // ici, les mois sortent en anglais dans un gabarit ecrit en francais.
        $locale = $this->company->locale ?: 'fr';
        $date = $this->contract->created_at->locale($locale)->translatedFormat('d F Y');
        // contract_number porte deja le prefixe, applique une seule fois par
        // ContractObserver::creating(). Le reformater donnerait « CONT#CONT#642 ».
        $contractNumber = $this->contract->contract_number;
        $contractTypeName = $this->contract->contractType->name ?? __('app.menu.contract');

        // Le lien de signature est une route signee : le gabarit affiche sa
        // date limite plutot que de laisser le destinataire la decouvrir expiree.
        $expiryDays = GlobalSetting::SIGNED_ROUTE_EXPIRY;
        $expiryDate = now($this->company->timezone)
            ->addDays($expiryDays)
            ->locale($locale)
            ->translatedFormat('d F Y');

        $data = [
            'companyName' => $this->company->company_name,
            'logoUrlDark' => $this->emailLogoUrl(),
            'logoPath' => $this->emailLogoPath(),
            'recipientName' => $notifiable->name,
            'subject' => $this->contract->subject ?: 'Contrat de service',
            'contractType' => $contractTypeName,
            'date' => $date,
            'contractNumber' => $contractNumber,
            'expiryDays' => $expiryDays,
            'expiryDate' => $expiryDate,
            'url' => $url,
            'company' => $this->company,
        ];

        return $this->build($notifiable)
            ->subject('Contrat ' . $contractNumber . ' à signer avant le ' . $expiryDate)
            ->view('emails.contract_init', $data)
            ->text('emails.txt.contract_init', $data);
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
        // contract_number porte deja le prefixe, applique une seule fois par
        // ContractObserver::creating(). Le reformater donnerait « CONT#CONT#642 ».
        $contractNumber = $this->contract->contract_number;
        $url = url()->temporarySignedRoute('front.contract.show', now()->addDays(GlobalSetting::SIGNED_ROUTE_EXPIRY), $this->contract->hash);
        $url = getDomainSpecificUrl($url, $this->company);
        
        return "📄 *Nouveau Contrat* : {$this->contract->subject}\n" .
               "Référence : {$contractNumber}\n\n" .
               "Bonjour {$notifiable->name}, votre contrat est prêt. Vous pouvez le signer ici :\n" .
               "🔗 {$url}";
    }

    public function toSmsContent()
    {
        // contract_number porte deja le prefixe, applique une seule fois par
        // ContractObserver::creating(). Le reformater donnerait « CONT#CONT#642 ».
        $contractNumber = $this->contract->contract_number;
        return "Nouv. Contrat: {$this->contract->subject}\nRef: {$contractNumber}\nConsultez vos emails.";
    }
}
