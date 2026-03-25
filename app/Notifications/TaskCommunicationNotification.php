<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskNote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskCommunicationNotification extends BaseNotification
{

    protected $task;
    protected $taskId;
    protected $company;
    protected $note;

    public function __construct(Task $task, TaskNote $note = null)
    {
        $this->task = $task;
        $this->taskId = $task->id;
        $this->company = $task->company;
        $this->note = $note;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Recharger la tâche depuis la BDD pour garantir les données fraîches
        $reloadedTask = Task::withoutGlobalScopes()->with(['project', 'boardColumn', 'company', 'users', 'files'])->find($this->taskId);
        if ($reloadedTask) {
            $this->task = $reloadedTask;
            $this->company = $reloadedTask->company;
        }

        $url = route('tasks.show', $this->task->id);
        $subject = $this->company->company_name . ' – Mise à jour de votre tâche : ' . $this->task->heading;
        $dueDate = $this->task->due_date ? $this->task->due_date->format($this->company->date_format) : 'N/A';

        $threshold = now()->subHours(1);
        $attachments = [];
        if ($this->task->files) {
            foreach ($this->task->files as $file) {
                if ($file->created_at->greaterThanOrEqualTo($threshold)) {
                    $attachments[] = [
                        'name' => $file->filename,
                        'url' => route('front.task_file_download', md5($file->id))
                    ];
                }
            }
        }

        $isClient = $notifiable->hasRole('client');

        if ($isClient) {
            // CLIENT → template épuré et rassurant
            $build = $this->build($notifiable)
                ->subject($subject)
                ->view('emails.task_update', [
                'companyName' => $this->company->company_name,
                'date' => now($this->company->timezone)->format($this->company->date_format),
                'heure' => now($this->company->timezone)->format('H:i'),
                'taskHeading' => $this->task->heading,
                'taskReference' => $this->task->task_short_code,
                'taskStatus' => $this->task->boardColumn->column_name,
                'recipientName' => $notifiable->name,
                'specialistName' => ($this->task->responsible_id ? $this->task->responsible->name : ($this->task->users->count() > 0 ? $this->task->users->first()->name : 'N/A')),
                'dueDate' => $dueDate,
                'explanation' => $this->note ? $this->note->note : null,
                'attachments' => $attachments,
                'url' => $url,
                'company' => $this->company,
            ]);
        }
        else {
            // COLLABORATEUR → template Diff Historique (Solution 3)
            // Forcer le chargement du projet avec son client (la relation eager-loadée par défaut
            // n'inclut pas le client car Task::$with limite les colonnes du projet)
            $project = $this->task->project_id
                ?\App\Models\Project::withoutGlobalScopes()->withTrashed()->with(['client' => function ($q) {
                $q->withoutGlobalScopes();
            }, 'clientdetails'])->find($this->task->project_id)
                : null;

            // Récupérer le statut précédent depuis l'historique de la tâche
            $previousStatusName = null;
            if ($this->task->history && $this->task->history->count() > 0) {
                $lastHistory = $this->task->history->first();
                $previousStatusName = $lastHistory->board_column_name ?? null;
            }

            // Auteur de la dernière modification
            $modifiedBy = user() ? user()->name : 'Système';

            $clientName = 'N/A';
            if ($project && $project->client) {
                $clientName = $project->client->name;
            } elseif ($project && $project->clientdetails) {
                $clientName = $project->clientdetails->company_name;
            }

            // Mappage des priorités en français
            $priorityMap = [
                'high' => 'Haut',
                'medium' => 'Moyen',
                'low' => 'Faible'
            ];
            $translatedPriority = $priorityMap[strtolower($this->task->priority)] ?? ucfirst($this->task->priority);

            $build = $this->build($notifiable)
                ->subject('[Mise à jour] ' . $this->task->task_short_code . ' – ' . $this->task->heading)
                ->view('emails.collab_task_update', [
                'companyName' => $this->company->company_name,
                'date' => now($this->company->timezone)->format($this->company->date_format),
                'heure' => now($this->company->timezone)->format('H:i'),
                'taskHeading' => $this->task->heading,
                'taskReference' => $this->task->task_short_code ?? 'N/A',
                'taskStatus' => $this->task->boardColumn->column_name,
                'previousStatus' => $previousStatusName,
                'modifiedBy' => $modifiedBy,
                'recipientName' => $notifiable->name,
                'priority' => $translatedPriority,
                'dueDate' => $dueDate,
                'noteContent' => $this->note ? $this->note->note : null,
                'clientName' => $clientName,
                'projectName' => $project ? $project->project_name : 'Personnel / Hors projet',
                'attachments' => $attachments,
                'url' => $url,
                'company' => $this->company,
            ]);
        }

        // Physically attach files
        if ($this->task->files) {
            foreach ($this->task->files->unique('hashname') as $file) {
                if ($file->created_at->greaterThanOrEqualTo($threshold)) {
                    $filePath = public_path('user-uploads/' . \App\Models\TaskFile::FILE_PATH . '/' . $this->task->id . '/' . $file->hashname);
                    if (file_exists($filePath)) {
                        $build->attach($filePath, ['as' => $file->filename]);
                    }
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
            'type' => 'communication',
            'note' => $this->note ? $this->note->note : '',
            'created_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Contenu spécifique pour SMS
     */
    public function toSmsContent()
    {
        $url = route('tasks.show', $this->task->id); // Idéalement un lien court ici
        return "{$this->company->company_name} :\nTâche \"{$this->task->heading}\" – Statut : {$this->task->boardColumn->column_name}.\nVoir détails : {$url}";
    }

    /**
     * Contenu spécifique pour WhatsApp
     */
    public function toWhatsAppContent($notifiable)
    {
        $url = route('tasks.show', $this->task->id);
        $deadline = $this->task->due_date ? $this->task->due_date->format($this->company->date_format) : 'N/A';

        return "📌 *{$this->company->company_name}*\n\n" .
            "Bonjour {$notifiable->name},\n\n" .
            "Votre tâche *{$this->task->heading}* est actuellement :\n" .
            "➡ Statut : *{$this->task->boardColumn->column_name}*\n\n" .
            "📅 Échéance : {$deadline}\n\n" .
            "🔗 Consulter : {$url}\n\n" .
            "Merci.";
    }
}
