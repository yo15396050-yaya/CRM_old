<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectInitNotification extends BaseNotification
{
    protected $project;
    protected $projectId;
    protected $company;

    /**
     * Ensure the notification is only dispatched after the DB transaction commits.
     */
    public $afterCommit = true;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->projectId = $project->id;
        $this->company = $project->company;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Recharger le projet
        $reloadedProject = Project::withoutGlobalScopes()->with(['client', 'company', 'members', 'files'])->find($this->projectId);
        if ($reloadedProject) {
            $this->project = $reloadedProject;
            $this->company = $reloadedProject->company;
        }

        $url = route('projects.show', $this->project->id);
        
        $isClient = $notifiable->hasRole('client');
        $subject = $isClient 
            ? $this->company->company_name . ' – Ouverture de votre dossier : ' . $this->project->project_name 
            : 'Assignation Diligence : ' . $this->project->project_name;

        $attachments = [];
        if ($this->project->files) {
            foreach ($this->project->files as $file) {
                $attachments[] = [
                    'name' => $file->filename,
                    'url' => route('front.project_file_download', md5($file->id))
                ];
            }
        }

        if ($isClient) {
            $build = $this->build($notifiable)
                ->subject($subject)
                ->view('emails.project_init', [
                    'companyName' => $this->company->company_name,
                    'date' => now($this->company->timezone)->format($this->company->date_format),
                    'heure' => now($this->company->timezone)->format('H:i'),
                    'projectName' => $this->project->project_name,
                    'recipientName' => $notifiable->name,
                    'headerColor' => $this->company->header_color,
                    'url' => $url,
                    'company' => $this->company,
                    'attachments' => $attachments,
                ]);
        } else {
            $build = $this->build($notifiable)
                ->subject($subject)
                ->view('emails.collab_project_init', [
                    'companyName' => $this->company->company_name,
                    'date' => now($this->company->timezone)->format($this->company->date_format),
                    'heure' => now($this->company->timezone)->format('H:i'),
                    'projectName' => $this->project->project_name,
                    'recipientName' => $notifiable->name,
                    'url' => $url,
                    'company' => $this->company,
                    'attachments' => $attachments,
                ]);
        }

        // Physiquement attacher les fichiers
        if ($this->project->files) {
            foreach ($this->project->files->unique('hashname') as $file) {
                $filePath = public_path('user-uploads/' . \App\Models\ProjectFile::FILE_PATH . '/' . $this->project->id . '/' . $file->hashname);
                if (file_exists($filePath)) {
                    $build->attach($filePath, ['as' => $file->filename]);
                }
            }
        }

        return $build;
    }

    public function toArray($notifiable)
    {
        return [
            'id' => $this->project->id,
            'project_name' => $this->project->project_name,
            'type' => 'project_init',
            'created_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Contenu spécifique pour WhatsApp/SMS (Client)
     */
    public function toWhatsAppClientContent($notifiable)
    {
        return "Bonjour {$notifiable->name}, le dossier '{$this->project->project_name}' a été ouvert. Nous vous tiendrons informé de chaque étape. Merci de votre confiance.";
    }

    /**
     * Contenu spécifique pour WhatsApp/SMS (Collaborateur)
     */
    public function toWhatsAppCollabContent($notifiable)
    {
        return "Vous avez été assigné à la diligence '{$this->project->project_name}'. Connectez-vous au CRM pour commencer.";
    }
}
