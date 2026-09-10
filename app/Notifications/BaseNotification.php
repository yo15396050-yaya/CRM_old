<?php

namespace App\Notifications;

use App\Models\GlobalSetting;
use App\Models\SmtpSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class BaseNotification extends Notification implements ShouldQueue
{

    use Queueable, Dispatchable;

    protected $company = null;
    protected $slack = null;

    /**
     * Create a new notification instance.
     *
     * @return MailMessage
     */

    /**
     * Build a mail message with proper configuration settings.
     *
     * This method sets up the mail message according to the configured settings
     * for the company or global settings.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function build($notifiable = null)
    {
        // Set the company and global settings
        $company = $this->company;
        $globalSetting = GlobalSetting::first();

        $locale = $notifiable->locale ?? 'en';

        // Set the application locale based on the company's locale or global settings
        if (isset($locale)) {
            App::setLocale($locale ?? (!is_null($company) ? $company->locale : 'en'));
        }
        else {
            App::setLocale(session('locale') ?: $globalSetting->locale);
        }

        // Retrieve SMTP settings
        $smtpSetting = SmtpSetting::first();

        // Initialize a mail message instance
        $build = (new MailMessage);

        // Set default reply name and email to SMTP settings
        $replyName = $companyName = $smtpSetting->mail_from_name;
        $replyEmail = $companyEmail = $smtpSetting->mail_from_email;

        // Set the application logo URL from the global settings
        Config::set('app.logo', $globalSetting->masked_logo_url);
        Config::set('app.name', $companyName);

        // If the application is Worksuite, return the mail message with SMTP settings
        if (isWorksuite()) {
            return $build->from($companyEmail, $companyName);
        }

        // If a company is specified, customize the reply name, email, logo URL, and application name
        if (!is_null($company)) {
            $replyName = $company->company_name;
            $replyEmail = $company->company_email;
            Config::set('app.logo', $company->masked_logo_url);
            Config::set('app.name', $replyName);
        }

        // Ensure that the company email and name are used if mail verification is successful
        $companyEmail = config('mail.verified') === true ? $companyEmail : $replyEmail;
        $companyName = config('mail.verified') === true ? $companyName : $replyName;

        // Return the mail message with configured from and replyTo settings
        return $build->from($companyEmail, $companyName)->replyTo($replyEmail, $replyName);
    }

    /**
     * Delai laisse aux fichiers Dropzone pour finir de monter avant le rendu.
     *
     * Porte par la file quand il y en a une : la requete HTTP rend la main
     * immediatement. Sous le driver 'sync', la notification s'execute dans la
     * requete et ce delai est ignore par Laravel — d'ou l'attente conservee
     * par waitForUploads(), sans laquelle le mail partirait sans ses pieces.
     */
    const UPLOAD_GRACE_SECONDS = 10;

    public function withDelay($notifiable, $channel = null)
    {
        if (config('queue.default') === 'sync') {
            return null;
        }

        return now()->addSeconds(self::UPLOAD_GRACE_SECONDS);
    }

    /**
     * Attente de repli, uniquement quand aucune file ne peut porter le delai.
     */
    protected function waitForUploads()
    {
        if (config('queue.default') === 'sync') {
            sleep(self::UPLOAD_GRACE_SECONDS);
        }
    }

    /**
     * URL publique du logo au format PNG pour les e-mails.
     *
     * Gmail, Outlook et Yahoo suppriment les images SVG : le logo servi a
     * l'application ne peut donc pas etre reutilise tel quel dans un message.
     * Deux rasterisations sont versionnees dans public/img/ : ce sont des
     * ressources du code, pas des fichiers televerses.
     *
     * @param bool $onDark true pour la version blanche, posee sur un fond sombre
     */
    protected function emailLogoUrl($onDark = false)
    {
        $file = $onDark ? 'task-flow-email-blanc.png' : 'task-flow-email-sombre.png';

        return getDomainSpecificUrl(asset('img/' . $file), $this->company);
    }

    /**
     * Chemin local du logo PNG, pour embarquer l'image dans le message.
     *
     * L'URL publique depend de APP_URL : tant qu'elle ne pointe pas sur un
     * domaine resolvable depuis Internet, l'image reste cassee chez le
     * destinataire. Embarquer le fichier rend le logo independant de ce
     * reglage — les gabarits retombent sur emailLogoUrl() si le fichier
     * venait a manquer.
     *
     * @param bool $onDark true pour la version blanche, posee sur un fond sombre
     */
    protected function emailLogoPath($onDark = false)
    {
        $file = $onDark ? 'task-flow-email-blanc.png' : 'task-flow-email-sombre.png';

        return public_path('img/' . $file);
    }

    /**
     * Mention d'echeance affichee en pastille : « À remettre dans 4 jours »,
     * « À remettre aujourd'hui », ou « En retard de 2 jours ».
     *
     * Retourne un libelle nul quand l'echeance est absente ou trop lointaine
     * pour meriter une alerte : la pastille disparait alors du gabarit.
     *
     * @return array{label: string|null, overdue: bool}
     */
    protected function dueBadge($dueDate)
    {
        if (is_null($dueDate)) {
            return ['label' => null, 'overdue' => false];
        }

        $due = $dueDate instanceof \Carbon\Carbon ? $dueDate->copy() : \Carbon\Carbon::parse($dueDate);
        $days = (int) now()->startOfDay()->diffInDays($due->startOfDay(), false);

        if ($days < 0) {
            return ['label' => 'En retard de ' . abs($days) . ' ' . (abs($days) > 1 ? 'jours' : 'jour'), 'overdue' => true];
        }

        if ($days === 0) {
            return ['label' => "À remettre aujourd'hui", 'overdue' => false];
        }

        if ($days <= 7) {
            return ['label' => 'À remettre dans ' . $days . ' ' . ($days > 1 ? 'jours' : 'jour'), 'overdue' => false];
        }

        return ['label' => null, 'overdue' => false];
    }

    protected function modifyUrl($url)
    {
        return getDomainSpecificUrl($url, $this->company);
    }

    /**
     * Build a Slack message for notification.
     */
    protected function slackBuild($notifiable): SlackMessage
    {
        // Retrieve Slack settings for the company
        $slack = $notifiable->company->slackSetting;

        // Compose and return a Slack message
        return (new SlackMessage())
            ->from($notifiable->company->company_name) // Set the sender name
            ->to('@' . $notifiable->employeeDetail->slack_username) // Set the recipient's Slack username
            ->image(asset_url_local_s3('slack-logo/' . $slack->slack_logo)); // Set the image for Slack message
    }

    /**
     * Create a Slack message for redirected notifications.
     */
    protected function slackRedirectMessage($subjectKey, $notifiable)
    {
        try {
            // Build a Slack message using the slackBuild function
            return $this->slackBuild($notifiable)
                ->content('*' . __($subjectKey) . '*' . "\n" . 'This is a redirected notification. Add slack username for *' . $notifiable->name . '*');
        } catch (\Exception $e) {
            // Catch and display any exceptions occurred
            echo $e->getMessage();
        }
    }

    /**
     * Check if the notifiable has a Slack username.
     *
     * @param mixed $notifiable
     * @return bool
     */
    protected function slackUserNameCheck($notifiable): bool
    {
        if (!isset($notifiable->employeeDetail)) {
            return false;
        }

        if (is_array($notifiable->employee)) {
            if (count($notifiable->employee) == 0) {
                return false;
            }
        }

        // Check if the notifiable a non-empty Slack username
        return (!is_null($notifiable->employeeDetail->slack_username) && ($notifiable->employeeDetail->slack_username != ''));
    }

    public function resetLocale()
    {
        // Set the company and global settings
        $company = $this->company;
        $globalSetting = GlobalSetting::first();

        // Set the application locale based on the company's locale or global settings
        if (!is_null($company)) {
            App::setLocale($company->locale ?? 'en');
        }
        else {
            App::setLocale(session('locale') ?: $globalSetting->locale);
        }
    }

}
