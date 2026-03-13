<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskInitNotification extends BaseNotification
{

    protected $task;
    protected $taskId;
    protected $company;

    public function __construct(Task $task)
    {
        $this->task = $task;
        $this->taskId = $task->id;
        $this->company = $task->company;
    }

    public function via($notifiable)
    {
        // On retourne database par défaut, l'envoi vers les autres canaux 
        // sera piloté par le ProNotificationService pour gérer le fallback.
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Recharger la tâche depuis la BDD pour garantir les données fraîches
        $reloadedTask = Task::withoutGlobalScopes()->with(['project', 'boardColumn', 'company', 'users'])->find($this->taskId);
        if ($reloadedTask) {
            $this->task = $reloadedTask;
            $this->company = $reloadedTask->company;
        }

        $url = route('tasks.show', $this->task->id);
        $dueDate = $this->task->due_date ? $this->task->due_date->format($this->company->date_format) : 'N/A';

        $subject = $this->company->company_name . ' – Mise à jour de votre tâche : ' . $this->task->heading;

        // Detect if the notifiable is a client (external) or a collaborator (internal)
        $isClient = $notifiable->hasRole('client');

        $build = null;

        $attachments = [];
        if ($this->task->files) {
            foreach ($this->task->files as $file) {
                // Pour une nouvelle tâche, on joint TOUS les fichiers déjà présents
                $attachments[] = [
                    'name' => $file->filename,
                    'url' => route('front.task_file_download', md5($file->id))
                ];
            }
        }

        if ($isClient) {
            // CLIENT → template épuré et rassurant
            $build = $this->build($notifiable)
                ->subject($subject)
                ->view('emails.task_init', [
                'companyName' => $this->company->company_name,
                'date' => now($this->company->timezone)->format($this->company->date_format),
                'heure' => now($this->company->timezone)->format('H:i'),
                'taskHeading' => $this->task->heading,
                'taskReference' => $this->task->task_short_code,
                'taskStatus' => $this->task->boardColumn->column_name,
                'recipientName' => $notifiable->name,
                'priority' => $this->task->priority,
                'specialistName' => $this->task->users->count() > 0
                ? $this->task->users->pluck('name')->implode(', ')
                : ($this->task->responsible_id ?\App\Models\User::find($this->task->responsible_id)->name : 'Équipe technique'),
                'dueDate' => $dueDate,
                'explanation' => $this->task->description ?: 'Aucune note particulière.',
                'attachments' => $attachments,
                'headerColor' => $this->company->header_color,
                'url' => $url,
                'company' => $this->company,
            ]);
        }
        else {
            // COLLABORATEUR → template Briefing Opérationnel (Solution 1)
            $responsible = $this->task->responsible_id ?\App\Models\User::find($this->task->responsible_id) : null;
            // Forcer le chargement du projet avec son client
            $project = $this->task->project_id
                ?\App\Models\Project::withoutGlobalScopes()->withTrashed()->with(['client' => function ($q) {
                $q->withoutGlobalScopes();
            }, 'clientdetails'])->find($this->task->project_id)
                : null;

            // DEBUG: tracer la récupération du client
            \Illuminate\Support\Facades\Log::info('DEBUG TaskInitNotification [collab]', [
                'task_id' => $this->task->id,
                'task_project_id' => $this->task->project_id,
                'project_found' => $project ? true : false,
                'project_client_id' => $project ? $project->client_id : 'no project',
                'project_client_name' => ($project && $project->client) ? $project->client->name : 'NULL',
                'project_clientdetails' => ($project && $project->clientdetails) ? $project->clientdetails->company_name : 'NULL',
            ]);

            $clientName = 'N/A';
            if ($project && $project->client) {
                $clientName = $project->client->name;
            } elseif ($project && $project->clientdetails) {
                $clientName = $project->clientdetails->company_name;
            }

            $build = $this->build($notifiable)
                ->subject('[Action requise] Tâche ' . $this->task->task_short_code . ' – ' . $this->task->heading)
                ->view('emails.collab_task_init', [
                'companyName' => $this->company->company_name,
                'date' => now($this->company->timezone)->format($this->company->date_format),
                'heure' => now($this->company->timezone)->format('H:i'),
                'taskHeading' => $this->task->heading,
                'taskReference' => $this->task->task_short_code ?? 'N/A',
                'taskStatus' => $this->task->boardColumn->column_name,
                'recipientName' => $notifiable->name,
                'priority' => $this->task->priority,
                'dueDate' => $dueDate,
                'description' => $this->task->description,
                'clientName' => $clientName,
                'projectName' => $project ? $project->project_name : 'N/A',
                'responsibleName' => $responsible ? $responsible->name : null,
                'responsibleEmail' => $responsible ? $responsible->email : null,
                'attachments' => $attachments,
                'url' => $url,
                'company' => $this->company,
            ]);
        }

        // Physically attach files
        if ($this->task->files) {
            foreach ($this->task->files->unique('hashname') as $file) {
                // Chemin physique pour joindre le fichier
                $filePath = public_path('user-uploads/' . \App\Models\TaskFile::FILE_PATH . '/' . $this->task->id . '/' . $file->hashname);
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
            'id' => $this->task->id,
            'heading' => $this->task->heading,
            'type' => 'type_1',
            'created_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Contenu spécifique pour WhatsApp/SMS
     */
    public function toSmsContent()
    {
        $dueDate = $this->task->due_date ? $this->task->due_date->format($this->company->date_format) : 'N/A';
        return "Nouv. Tâche: {$this->task->heading}\nPriorité: {$this->task->priority}\nÉchéance: {$dueDate}";
    }
}
