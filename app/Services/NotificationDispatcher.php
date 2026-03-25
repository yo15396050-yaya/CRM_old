<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskNote;
use App\Models\User;
use App\Notifications\TaskCommunicationNotification;
use App\Services\ProNotificationService;
use Illuminate\Support\Facades\Notification;

class NotificationDispatcher
{
    protected $proNotificationService;

    public function __construct()
    {
        $this->proNotificationService = new ProNotificationService();
    }

    /**
     * Dispatch notifications for task notes
     */
    public function dispatchTaskNoteNotification(Task $task, TaskNote $note = null, array $channels = ['email', 'whatsapp', 'sms'])
    {
        if (in_array('none', $channels)) {
            return;
        }

        $channelsUsed = [];
        $recipientName = null;

        // 1. Notify Client if note is client visible and approved
        if ($note && $note->is_client_visible && $note->status == 'approved' && $task->project && $task->project->client) {
            $client = $task->project->client;

            // Don't notify if the client is the one posting
            if (!user() || user()->id != $client->id) {
                // Utiliser le nouveau service Pro
                $this->proNotificationService->sendTaskCommunication($task, $client, $note, $channels);

                $channelsUsed = $channels;
                $recipientName = $client->name;
            }
        }

        // 2. Notify Responsible if an исполнитель posts a note
        if ($task->responsible_id && (!user() || user()->id != $task->responsible_id)) {
            $responsible = User::find($task->responsible_id);
            if ($responsible) {
                // Pour le responsable, on peut rester sur la notification interne ou pro
                $this->proNotificationService->sendTaskCommunication($task, $responsible, $note, $channels);
            }
        }

        // Update note with actual channels used for history/UI
        if ($note && !empty($channelsUsed)) {
            $note->channel = implode(', ', $channelsUsed);
            $note->recipient_name = $recipientName;
            $note->save();
        }
    }

    /**
     * Dispatch notification when a task is delegated
     * 
     * @param Task $task
     * @param array $channels
     * @return void
     */
    public function dispatchTaskDelegation(Task $task, array $channels = ['email', 'whatsapp', 'sms'])
    {
        if (in_array('none', $channels)) {
            return;
        }

        // 1. Notify ALL assigned members (Type 1 - Init)
        if ($task->users->count() > 0) {
            foreach ($task->users as $user) {
                /** @var User $user */
                $this->proNotificationService->sendTaskInit($task, $user, $channels);
            }
        }
        elseif ($task->responsible_id) {
            $responsible = User::find($task->responsible_id);
            if ($responsible) {
                $this->proNotificationService->sendTaskInit($task, $responsible, $channels);
            }
        }

        // 2. Notify Client about the task (Type 1 - Init) 
        if ($task->project && $task->project->client) {
            $client = $task->project->client;
            $this->proNotificationService->sendTaskInit($task, $client, $channels);
        }
    }

    /**
     * Dispatch update notifications to all assigned members and the project client
     */
    public function dispatchTaskUpdate(Task $task, array $channels = ['email', 'whatsapp', 'sms'])
    {
        if (in_array('none', $channels)) {
            return;
        }

        // 1. Notify ALL assigned members (Type 2 - Update)
        if ($task->users->count() > 0) {
            foreach ($task->users as $user) {
                /** @var User $user */
                $this->proNotificationService->sendTaskCommunication($task, $user, null, $channels);
            }
        }

        // 2. Notify Client about the update (Type 2 - Update)
        if ($task->project && $task->project->client) {
            $client = $task->project->client;
            $this->proNotificationService->sendTaskCommunication($task, $client, null, $channels);
        }
    }

    /**
     * Dispatch notifications to manually selected collaborators and clients
     * 
     * @param Task $task
     * @param array $employeeIds
     * @param array $clientIds
     * @param array $channels
     * @return void
     */
    public function dispatchManualNotifications(Task $task, array $employeeIds = [], array $clientIds = [], array $channels = ['email', 'whatsapp', 'sms'])
    {
        if (in_array('none', $channels)) {
            return;
        }

        $alreadyNotifiedIds = $task->users->pluck('id')->toArray();
        if ($task->responsible_id) {
            $alreadyNotifiedIds[] = $task->responsible_id;
        }

        if (!empty($employeeIds)) {
            $employees = User::whereIn('id', $employeeIds)->get();
            foreach ($employees as $employee) {
                /** @var User $employee */
                if (!in_array($employee->id, $alreadyNotifiedIds)) {
                    $this->proNotificationService->sendTaskInit($task, $employee, $channels);
                }
            }
        }

        if (!empty($clientIds)) {
            $clients = User::whereIn('id', $clientIds)->get();
            $projectClient = ($task->project && $task->project->client) ? $task->project->client : null;
            foreach ($clients as $client) {
                /** @var User $client */
                if (!$projectClient || $client->id != $projectClient->id) {
                    $this->proNotificationService->sendTaskInit($task, $client, $channels);
                }
            }
        }
    }

    /**
     * Dispatch notifications when a project (diligence) is created or updated
     * 
     * @param Project $project
     * @param array $channels
     * @return void
     */
    public function dispatchProjectInit(Project $project, array $channels = ['email', 'whatsapp', 'sms'])
    {
        if (in_array('none', $channels)) {
            return;
        }

        try {
            // 1. Notify ALL assigned members
            if ($project->projectMembers->count() > 0) {
                foreach ($project->projectMembers as $user) {
                    /** @var User $user */
                    $this->proNotificationService->sendProjectInit($project, $user, $channels);
                }
            }

            // 2. Notify Client
            if ($project->client) {
                $this->proNotificationService->sendProjectInit($project, $project->client, $channels);
            }
        } catch (\Throwable $e) {
            \Log::error('Error in dispatchProjectInit: ' . $e->getMessage());
        }
    }

    /**
     * Dispatch notifications when a contract is created or updated
     */
    public function dispatchContractInit(\App\Models\Contract $contract, array $channels = ['email', 'whatsapp', 'sms'])
    {
        if (in_array('none', $channels)) {
            return;
        }

        try {
            // Notify Client
            if ($contract->client) {
                $this->proNotificationService->sendContractInit($contract, $contract->client, $channels);
            }
        } catch (\Throwable $e) {
            \Log::error('Error in dispatchContractInit: ' . $e->getMessage());
        }
    }
}
