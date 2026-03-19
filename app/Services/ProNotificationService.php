<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskNote;
use App\Models\User;
use App\Models\ProNotificationLog;
use App\Notifications\TaskInitNotification;
use App\Notifications\TaskProgressNotification;
use App\Notifications\TaskCommunicationNotification;
use App\Notifications\ProjectInitNotification;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Modules\Sms\Http\Traits\InfobipMessageTrait;
use Illuminate\Support\Str;

class ProNotificationService
{
    use InfobipMessageTrait;

    protected $twilio;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        if ($sid && $token && $token !== '[AuthToken]') {
            // Option pour ignorer SSL en local (nécessaire sur Windows/Laragon parfois)
            $options = [];
            if (config('app.env') === 'local') {
                $curlClient = new \Twilio\Http\CurlClient([
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]);
                $this->twilio = new Client($sid, $token, null, null, $curlClient);
            }
            else {
                $this->twilio = new Client($sid, $token);
            }
        }
    }
    /**
     * Envoi de la notification d'initialisation (Type 1)
     */
    public function sendTaskInit(Task $task, User $notifiable, array $channels = ['email', 'whatsapp'])
    {
        Log::info("Début sendTaskInit pour {$notifiable->email} sur canaux: " . implode(',', $channels));
        try {
            // 1. Envoi Email & Database (Laravel par défaut)
            if (in_array('email', $channels)) {
                Log::info("Tentative envoi email (Init) à {$notifiable->email}");
                $notifiable->notifyNow(new TaskInitNotification($task));
                $this->logNotification($task, $notifiable, 'type_1', 'email', 'sent');
                Log::info("Email (Init) envoyé et loggé.");
            }

            // 2. Tentative WhatsApp
            if (in_array('whatsapp', $channels)) {
                $to = $this->formatE164($notifiable->whatsapp ?? $notifiable->mobile);
                Log::info("Tentative WhatsApp (Init) à " . ($to ?? 'vide'));
                $this->sendWhatsApp($task, $notifiable, 'type_1');
            }

            // 3. Tentative SMS (si explicitly demandé ou en fallback)
            if (in_array('sms', $channels)) {
                $this->sendSms($task, $notifiable, 'type_1');
            }

        }
        catch (\Throwable $e) {
            Log::error("Erreur sendTaskInit: " . $e->getMessage());
        }
    }

    /**
     * Envoi de la notification de processus / communication (Type 2 / Communication)
     */
    public function sendTaskCommunication(Task $task, User $notifiable, TaskNote $note = null, array $channels = ['email', 'whatsapp'])
    {
        Log::info("Début sendTaskCommunication pour {$notifiable->email}");
        try {
            $notification = new TaskCommunicationNotification($task, $note);

            // 1. Envoi Email
            if (in_array('email', $channels)) {
                Log::info("Tentative envoi email (Update) à {$notifiable->email}");
                $notifiable->notifyNow($notification);
                $this->logNotification($task, $notifiable, 'communication', 'email', 'sent', $note ? $note->note : null);
                Log::info("Email (Update) envoyé et loggé.");
            }

            // 2. WhatsApp
            if (in_array('whatsapp', $channels)) {
                $to = $this->formatE164($notifiable->whatsapp ?? $notifiable->mobile);
                Log::info("Tentative WhatsApp (Update) à " . ($to ?? 'vide'));
                $content = $notification->toWhatsAppContent($notifiable);
                $this->sendWhatsApp($task, $notifiable, 'communication', $content);
            }

            // 3. SMS
            if (in_array('sms', $channels)) {
                $content = $notification->toSmsContent();
                $this->sendSms($task, $notifiable, 'communication', $content);
            }

        }
        catch (\Throwable $e) {
            Log::error("Erreur sendTaskCommunication: " . $e->getMessage());
        }
    }

    /**
     * Envoi de la notification d'initialisation de Projet (Type Init)
     */
    public function sendProjectInit(Project $project, User $notifiable, array $channels = ['email', 'whatsapp'])
    {
        Log::info("Début sendProjectInit pour {$notifiable->email} sur canaux: " . implode(',', $channels));
        try {
            $notification = new ProjectInitNotification($project);

            // 1. Envoi Email
            if (in_array('email', $channels)) {
                Log::info("Tentative envoi email (Project Init) à {$notifiable->email}");
                $notifiable->notifyNow($notification);
                $this->logProjectNotification($project, $notifiable, 'project_init', 'email', 'sent');
                Log::info("Email (Project Init) envoyé.");
            }

            // 2. WhatsApp
            if (in_array('whatsapp', $channels)) {
                $content = $notifiable->hasRole('client') ? $notification->toWhatsAppClientContent($notifiable) : $notification->toWhatsAppCollabContent($notifiable);
                $this->sendProjectWhatsApp($project, $notifiable, 'project_init', $content);
            }

            // 3. SMS
            if (in_array('sms', $channels)) {
                $content = $notifiable->hasRole('client') ? $notification->toWhatsAppClientContent($notifiable) : $notification->toWhatsAppCollabContent($notifiable);
                $this->sendProjectSms($project, $notifiable, 'project_init', $content);
            }

        }
        catch (\Throwable $e) {
            Log::error("Erreur sendProjectInit: " . $e->getMessage());
        }
    }

    protected function sendWhatsApp(Task $task, User $notifiable, $type, $content = null)
    {
        $to = $this->formatE164($notifiable->whatsapp ?? $notifiable->mobile);

        // Try Infobip first
        $settings = sms_setting();
        if ($settings->infobip_status || config('services.infobip.api_key')) {
            Log::info("Tentative WhatsApp via Infobip pour {$to}");
            
            // Build rich content if not provided (similar to NewClientTaskSms)
            if (!$content) {
                $firstName = explode(' ', $notifiable->name)[0];
                $description = $task->description ? trim(strip_tags($task->description)) : 'N/A';
                $dueDate = $task->due_date ? $task->due_date->translatedFormat('d/m/Y à H:i') : 'Non définie';
                $taskPriority = ['high' => 'Haute', 'medium' => 'Normale', 'low' => 'Basse'][$task->priority] ?? $task->priority;
                $url = route('tasks.show', $task->id);
                $companyName = $task->company->company_name ?? 'Notre équipe';

                if ($type == 'type_1') {
                    $content = trans('sms::template.new-client-task-whatsapp', [
                        'name' => $firstName,
                        'heading' => $task->heading,
                        'description' => $description,
                        'dueDate' => $dueDate,
                        'priority' => $taskPriority,
                        'url' => $url,
                        'companyName' => $companyName
                    ]);
                    Log::info("Content traduit WhatsApp Type 1: " . Str::limit($content, 100));
                } else {
                    $content = "✅ Bonjour {$firstName},\n\nLa tâche \"{$task->heading}\" a été mise à jour.\n\nVous pouvez consulter les détails ici :\n🔗 {$url}";
                }
            }

            $attachments = [];
            foreach ($task->files as $file) {
                $attachments[] = [
                    'url' => $file->file_url,
                    'name' => $file->filename
                ];
            }

            if ($this->sendViaInfobip($notifiable, $content, 'whatsapp', $attachments)) {
                $this->logNotification($task, $notifiable, $type, 'whatsapp', 'sent');
                return;
            }
            Log::warning("Échec envoi Infobip WhatsApp, tentative Twilio...");
        }

        if (!$to || !$this->twilio) {
            $this->logNotification($task, $notifiable, $type, 'whatsapp', 'failed', !$this->twilio ? 'Client Twilio non configuré' : 'Numéro manquant');
            return;
        }

        // WhatsApp nécessite le préfixe 'whatsapp:'
        $toWhatsApp = (strpos($to, 'whatsapp:') === false) ? 'whatsapp:' . $to : $to;
        $fromWhatsApp = env('TWILIO_WHATSAPP_NUMBER', 'whatsapp:+14155238886');
        $contentSid = env('TWILIO_WHATSAPP_CONTENT_SID');

        try {
            $params = [
                'from' => $fromWhatsApp,
            ];

            if ($contentSid) {
                $params['contentSid'] = $contentSid;
                // On passe les variables pour le template (1=Titre, 2=Date/Heure par défaut)
                $dueDate = $task->due_date ? $task->due_date->format('d/m H:i') : now()->format('d/m H:i');
                $params['contentVariables'] = json_encode([
                    '1' => (string)$task->heading,
                    '2' => (string)$dueDate
                ]);
            }
            else {
                $params['body'] = $content ?: "Mise à jour de tâche : " . $task->heading;
            }

            $this->twilio->messages->create($toWhatsApp, $params);
            $this->logNotification($task, $notifiable, $type, 'whatsapp', 'sent');
        }
        catch (\Throwable $e) {
            Log::error("Erreur Twilio WhatsApp: " . $e->getMessage());
            // FALLBACK vers SMS
            $this->logNotification($task, $notifiable, $type, 'whatsapp', 'fallback_triggered', $e->getMessage());
            $this->sendSms($task, $notifiable, $type, $content);
        }
    }

    protected function sendSms(Task $task, User $notifiable, $type, $content = null)
    {
        $to = $this->formatE164($notifiable->mobile);

        // Try Infobip first
        $settings = sms_setting();
        if ($settings->infobip_status || config('services.infobip.api_key')) {
            Log::info("Tentative SMS via Infobip pour {$to}");

            if (!$content) {
                $description = $task->description ? trim(strip_tags($task->description)) : 'N/A';
                $shortDescription = Str::limit($description, 50);
                $dueDate = $task->due_date ? $task->due_date->translatedFormat('d/m/Y') : 'Non définie';
                $url = route('tasks.show', $task->id);

            if ($type == 'type_1') {
                $content = trans('sms::template.new-client-task', [
                    'heading' => $task->heading,
                    'description' => $shortDescription,
                    'dueDate' => $dueDate,
                    'url' => $url
                ]);
                Log::info("Content traduit SMS Type 1: " . Str::limit($content, 100));
            } else {
                    $content = "Mise à jour tâche : {$task->heading}\nLien : {$url}";
                }
            }

            $attachments = [];
            foreach ($task->files as $file) {
                $attachments[] = [
                    'url' => $file->file_url,
                    'name' => $file->filename
                ];
            }

            if ($this->sendViaInfobip($notifiable, $content, 'sms', $attachments)) {
                $this->logNotification($task, $notifiable, $type, 'sms', 'sent');
                return;
            }
            Log::warning("Échec envoi Infobip SMS, tentative Twilio...");
        }

        if (!$to || !$this->twilio) {
            $this->logNotification($task, $notifiable, $type, 'sms', 'failed', !$this->twilio ? 'Client Twilio non configuré' : 'Numéro manquant');
            return;
        }

        try {
            $this->twilio->messages->create($to, [
                'from' => env('TWILIO_SMS_NUMBER'),
                'body' => $content ?: "Mise à jour de tâche : " . $task->heading
            ]);
            $this->logNotification($task, $notifiable, $type, 'sms', 'sent');
        }
        catch (\Throwable $e) {
            Log::error("Erreur Twilio SMS: " . $e->getMessage());
            $this->logNotification($task, $notifiable, $type, 'sms', 'failed', $e->getMessage());
        }
    }

    /**
     * Journalisation dans la base de données (Tâches)
     */
    protected function logNotification($task, $notifiable, $type, $channel, $status, $error = null)
    {
        try {
            $to = ($channel == 'email') ? $notifiable->email : $this->formatE164($notifiable->whatsapp ?? $notifiable->mobile ?? 'N/A');

            ProNotificationLog::create([
                'company_id' => $task->company_id,
                'task_id' => $task->id,
                'user_id' => $notifiable->id,
                'type' => $type,
                'channel' => $channel,
                'to' => $to ?: 'N/A',
                'status' => $status,
                'content_summary' => ($type == 'type_1') ? "Initialisation: " . $task->heading : "Communication: " . $task->heading,
                'error_details' => $error,
                'sent_at' => now()
            ]);
        }
        catch (\Throwable $e) {
            Log::error("Échec logNotification: " . $e->getMessage());
        }
    }

    protected function sendProjectWhatsApp(Project $project, User $notifiable, $type, $content = null)
    {
        $to = $this->formatE164($notifiable->whatsapp ?? $notifiable->mobile);

        // Try Infobip first
        $settings = sms_setting();
        if ($settings->infobip_status || config('services.infobip.api_key')) {
            Log::info("Tentative Project WhatsApp via Infobip pour {$to}");
            
            if (!$content) {
                $content = "📢 Mise à jour de projet : " . $project->project_name . "\nConsultez les détails sur votre dashboard.";
            }

            if ($this->sendViaInfobip($notifiable, $content, 'whatsapp')) {
                $this->logProjectNotification($project, $notifiable, $type, 'whatsapp', 'sent');
                return;
            }
        }

        if (!$to || !$this->twilio) {
            $this->logProjectNotification($project, $notifiable, $type, 'whatsapp', 'failed', !$this->twilio ? 'Client Twilio non configuré' : 'Numéro manquant');
            return;
        }

        $toWhatsApp = (strpos($to, 'whatsapp:') === false) ? 'whatsapp:' . $to : $to;
        $fromWhatsApp = env('TWILIO_WHATSAPP_NUMBER', 'whatsapp:+14155238886');

        try {
            $this->twilio->messages->create($toWhatsApp, [
                'from' => $fromWhatsApp,
                'body' => $content ?: "Mise à jour de projet : " . $project->project_name
            ]);
            $this->logProjectNotification($project, $notifiable, $type, 'whatsapp', 'sent');
        }
        catch (\Throwable $e) {
            Log::error("Erreur Twilio WhatsApp Projet: " . $e->getMessage());
            $this->logProjectNotification($project, $notifiable, $type, 'whatsapp', 'fallback_triggered', $e->getMessage());
            $this->sendProjectSms($project, $notifiable, $type, $content);
        }
    }

    protected function sendProjectSms(Project $project, User $notifiable, $type, $content = null)
    {
        $to = $this->formatE164($notifiable->mobile);

        // Try Infobip first
        $settings = sms_setting();
        if ($settings->infobip_status || config('services.infobip.api_key')) {
            Log::info("Tentative Project SMS via Infobip pour {$to}");

            if (!$content) {
                $content = "📢 Projet : " . $project->project_name;
            }

            if ($this->sendViaInfobip($notifiable, $content, 'sms')) {
                $this->logProjectNotification($project, $notifiable, $type, 'sms', 'sent');
                return;
            }
        }

        if (!$to || !$this->twilio) {
            $this->logProjectNotification($project, $notifiable, $type, 'sms', 'failed', !$this->twilio ? 'Client Twilio non configuré' : 'Numéro manquant');
            return;
        }

        try {
            $this->twilio->messages->create($to, [
                'from' => env('TWILIO_SMS_NUMBER'),
                'body' => $content ?: "Mise à jour de projet : " . $project->project_name
            ]);
            $this->logProjectNotification($project, $notifiable, $type, 'sms', 'sent');
        }
        catch (\Throwable $e) {
            Log::error("Erreur Twilio SMS Projet: " . $e->getMessage());
            $this->logProjectNotification($project, $notifiable, $type, 'sms', 'failed', $e->getMessage());
        }
    }

    /**
     * Journalisation pour les projets
     */
    protected function logProjectNotification($project, $notifiable, $type, $channel, $status, $error = null)
    {
        try {
            $to = ($channel == 'email') ? $notifiable->email : $this->formatE164($notifiable->whatsapp ?? $notifiable->mobile ?? 'N/A');

            ProNotificationLog::create([
                'company_id' => $project->company_id,
                'project_id' => $project->id,
                'user_id' => $notifiable->id,
                'type' => $type,
                'channel' => $channel,
                'to' => $to ?: 'N/A',
                'status' => $status,
                'content_summary' => "Projet: " . $project->project_name,
                'error_details' => $error,
                'sent_at' => now()
            ]);
        }
        catch (\Throwable $e) {
            Log::error("Échec logProjectNotification: " . $e->getMessage());
        }
    }

    /**
     * Formate un numéro de téléphone au format E.164 (ex: +225...)
     */
    private function formatE164($number)
    {
        if (!$number) return null;

        // Nettoyage des caractères non numériques (sauf +)
        $number = preg_replace('/[^0-9+]/', '', $number);

        // Si commence déjà par +, c'est bon
        if (strpos($number, '+') === 0) {
            return $number;
        }

        // Si commence par 00, on remplace par +
        if (strpos($number, '00') === 0) {
            return '+' . substr($number, 2);
        }

        // Par défaut pour la Côte d'Ivoire (+225)
        // On ajoute le préfixe si absent. 
        // Si le numéro commence par un chiffre autre que 0, on ajoute +225 directly
        // Si il commence par 0, on garde le 0 car en CI les nouveaux numéros à 10 chiffres commencent par 01, 05, 07 etc.
        return '+225' . $number;
    }
}
