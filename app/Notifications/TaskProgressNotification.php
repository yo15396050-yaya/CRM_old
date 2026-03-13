<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskNote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskProgressNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected $task;
    protected $company;
    protected $note;

    public function __construct(Task $task, TaskNote $note = null)
    {
        $this->task = $task;
        $this->company = $task->company;
        $this->note = $note;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('tasks.show', $this->task->id);
        $subject = $this->company->company_name . ' | Mise à jour de votre demande';

        $email = $this->build($notifiable)
            ->subject($subject)
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line('Le traitement de votre tâche "' . $this->task->heading . '" a progressé.');

        if ($this->note && $this->note->note) {
            $email->line('**Détails techniques :**')
                ->line($this->note->note);
        }

        if ($this->note && $this->note->deliverables) {
            $email->line('**Processus de traitement :**')
                ->line($this->note->deliverables);
        }

        return $email->action('S\'informer sur l\'avancement', $url)
            ->line('Nous restons à votre entière disposition.');
    }

    public function toArray($notifiable)
    {
        return [
            'id' => $this->task->id,
            'heading' => $this->task->heading,
            'type' => 'type_2',
            'note' => $this->note ? $this->note->note : '',
            'created_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Contenu spécifique pour WhatsApp/SMS
     */
    public function toSmsContent()
    {
        return "Mise à jour Tâche: {$this->task->heading}\nConsultez votre espace pour les détails techniques.";
    }
}
