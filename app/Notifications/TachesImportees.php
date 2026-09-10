<?php

namespace App\Notifications;

use App\Models\EmailNotificationSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;

/**
 * Recapitulatif des taches confiees lors d'un import.
 *
 * Un seul message par collaborateur, plutot qu'un par tache.
 *
 * La raison est mesuree : un envoi SMTP coute neuf secondes sur ce
 * serveur, et la file d'attente est en « sync ». Notifier tache par
 * tache aurait fait durer un import de vingt lignes trois minutes, et
 * de cent lignes un quart d'heure — au-dela du delai maximal de PHP.
 * Le recapitulatif ramene le cout au nombre de personnes concernees.
 *
 * Il est aussi plus lisible : recevoir vingt courriels d'un coup, c'est
 * n'en lire aucun.
 */
class TachesImportees extends BaseNotification
{

    private Collection $taches;
    private $emailSetting;

    public function __construct(Collection $taches)
    {
        $this->taches = $taches;
        $this->emailSetting = EmailNotificationSetting::userAssignTask();
        $this->company = $taches->first()->company ?? company();
    }

    /**
     * Memes canaux que la notification d'assignation : le collaborateur
     * qui a coupe ses courriels ne doit pas en recevoir davantage parce
     * que les taches arrivent par fichier.
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($this->emailSetting->send_email == 'yes'
            && $notifiable->email_notifications
            && $notifiable->email) {
            $via[] = 'mail';
        }

        return $via;
    }

    public function toMail($notifiable): MailMessage
    {
        $build = parent::build($notifiable);

        $nombre = $this->taches->count();
        $format = $this->company->date_format;

        /* La liste des taches, avec leur echeance quand elle existe. */
        $lignes = $this->taches->map(function ($t) use ($format) {
            $ref = $t->task_short_code ? ' <span style="color:#9ca3af">#' . e($t->task_short_code) . '</span>' : '';

            $echeance = $t->due_date
                ? ' — <b style="color:#3f6212">' . __('app.dueDate') . ' : '
                    . $t->due_date->format($format) . '</b>'
                : '';

            return '<li style="margin-bottom:6px">' . e($t->heading) . $ref . $echeance . '</li>';
        })->implode('');

        $contenu = '<p>' . trans_choice(
            'Une tâche vous a été confiée.|:count tâches vous ont été confiées.',
            $nombre, ['count' => $nombre]
        ) . '</p><ul style="padding-left:18px;margin:10px 0 0">' . $lignes . '</ul>';

        $url = getDomainSpecificUrl(route('tasks.index'), $this->company);

        $build
            ->subject(trans_choice(
                'Une nouvelle tâche vous a été confiée|:count nouvelles tâches vous ont été confiées',
                $nombre, ['count' => $nombre]
            ) . ' — ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->markdown('mail.task.created', [
                'url' => $url,
                'content' => $contenu,
                'themeColor' => $this->company->header_color,
                'notifiableName' => $notifiable->name,
            ]);

        parent::resetLocale();

        return $build;
    }

    /**
     * Trace en base : on retient la premiere tache, pour que le clic
     * depuis la cloche mene quelque part, et le nombre total.
     */
    public function toArray()
    {
        $premiere = $this->taches->first();

        return [
            'id'         => $premiere->id,
            'heading'    => $premiere->heading,
            'nombre'     => $this->taches->count(),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

}
