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
        $this->waitForUploads();

        // Recharger le projet
        $reloadedProject = Project::withoutGlobalScopes()
            ->with(['client', 'company', 'members', 'files', 'category', 'projectAdmin', 'projectMembersWithoutScope'])
            ->find($this->projectId);
        if ($reloadedProject) {
            $this->project = $reloadedProject;
            $this->company = $reloadedProject->company;
        }

        $url = route('projects.show', $this->project->id);
        
        $isClient = $notifiable->hasRole('client');
        $subject = $isClient 
            ? $this->company->company_name . ' – Ouverture de votre dossier : ' . $this->project->project_name 
            : 'Assignation Projet : ' . $this->project->project_name;

        $attachments = [];
        if ($this->project->files) {
            \Illuminate\Support\Facades\Log::info("DEBUG ProjectInitNotification - Fichiers trouvés pour le projet " . $this->project->id . ": " . $this->project->files->count());
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
            $format = $this->company->date_format;

            // Periode couverte : les deux bornes du projet quand elles existent.
            $periodLabel = null;
            if ($this->project->start_date && $this->project->deadline) {
                $periodLabel = $this->project->start_date->format($format) . ' – ' . $this->project->deadline->format($format);
            } elseif ($this->project->start_date) {
                $periodLabel = 'À partir du ' . $this->project->start_date->format($format);
            }

            $teamMembers = $this->project->projectMembersWithoutScope
                ? $this->project->projectMembersWithoutScope->pluck('name')->filter()->take(8)->values()->all()
                : [];

            $clientName = null;
            if ($this->project->client) {
                $clientName = $this->project->client->name;
            } elseif ($this->project->clientdetails) {
                $clientName = $this->project->clientdetails->company_name;
            }

            $data = [
                'companyName' => $this->company->company_name,
                'logoUrlDark' => $this->emailLogoUrl(),
                'logoPath' => $this->emailLogoPath(),
                'date' => now($this->company->timezone)->format($format),
                'heure' => now($this->company->timezone)->format('H:i'),
                'projectName' => $this->project->project_name,
                'projectCode' => $this->project->project_short_code,
                'natureName' => $this->project->category ? $this->project->category->category_name : null,
                'periodLabel' => $periodLabel,
                'deadline' => $this->project->deadline ? $this->project->deadline->format($format) : null,
                'clientName' => $clientName,
                'adminName' => $this->project->projectAdmin ? $this->project->projectAdmin->name : null,
                'teamMembers' => $teamMembers,
                'summary' => $this->project->project_summary,
                'recipientName' => $notifiable->name,
                'url' => $url,
                'company' => $this->company,
                'attachments' => $attachments,
            ];

            $build = $this->build($notifiable)
                ->subject($subject)
                ->view('emails.collab_project_init', $data)
                ->text('emails.txt.collab_project_init', $data);
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
        return "Vous avez été assigné à le projet '{$this->project->project_name}'. Connectez-vous au CRM pour commencer.";
    }
}
