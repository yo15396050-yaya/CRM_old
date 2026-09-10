<?php

namespace App\Traits;

use App\Models\Task;
use App\Models\User;

/**
 * Qui prevenir lorsqu'une tache change de colonne.
 *
 * Le principe : on ecrit a quelqu'un uniquement s'il a une decision a
 * prendre ou une information qu'il ne peut pas deviner. L'avancement
 * ordinaire d'une tache se lit sur le tableau, il n'a pas besoin d'un
 * courriel.
 *
 * Quatre passages seulement declenchent un envoi :
 *
 *   1. vers « En attente d'approbation »  -> l'administrateur arbitre
 *   2. sortie de « En attente d'approbation » -> l'assigne connait le verdict
 *   3. vers « Incomplete »                -> l'assigne doit reprendre
 *   4. vers « Terminee » par un tiers     -> l'assigne apprend la cloture
 *
 * Et dans tous les cas, jamais l'auteur du geste : il sait ce qu'il
 * vient de faire.
 */
trait NotificationStatutTache
{

    /**
     * Passages qui meritent un courriel, exprimes en slugs de colonne.
     */
    private const ARRIVEE_ARBITRAGE = 'waiting_approval';
    private const ARRIVEE_REPRISE   = 'incomplete';
    private const ARRIVEE_CLOTURE   = 'completed';

    /**
     * Faut-il notifier ce changement de colonne, et si oui, qui ?
     *
     * @param  Task        $task    tache apres enregistrement
     * @param  string|null $depart  slug de la colonne quittee
     * @return array{0: \Illuminate\Support\Collection|null, 1: string|null}
     *         destinataires et nom de la notification ; [null, null] si
     *         ce passage ne merite pas d'envoi.
     */
    protected function destinatairesChangementStatut(Task $task, ?string $depart): array
    {
        $arrivee = optional($task->boardColumn)->slug;

        if (is_null($arrivee) || $depart === $arrivee) {
            return [null, null];
        }

        // 1. Une approbation est demandee : seul l'arbitre est concerne.
        if ($arrivee === self::ARRIVEE_ARBITRAGE) {
            return [$this->arbitres($task), 'TaskApproval'];
        }

        // 2. L'arbitrage est rendu : l'assigne doit connaitre le verdict.
        if ($depart === self::ARRIVEE_ARBITRAGE) {
            $notification = $arrivee === self::ARRIVEE_CLOTURE ? 'TaskCompleted' : 'TaskStatusUpdated';

            return [$this->assignes($task), $notification];
        }

        // 3. Retour en arriere : la tache est a reprendre.
        if ($arrivee === self::ARRIVEE_REPRISE) {
            return [$this->assignes($task), 'TaskStatusUpdated'];
        }

        // 4. Cloture par quelqu'un d'autre que la personne en charge.
        if ($arrivee === self::ARRIVEE_CLOTURE) {
            return [$this->assignes($task), 'TaskCompleted'];
        }

        // Tout le reste — a faire, en cours, en test — reste silencieux.
        return [null, null];
    }

    /**
     * Administrateurs de la societe, plus l'administrateur de la diligence.
     */
    private function arbitres(Task $task)
    {
        $arbitres = User::allAdmins($task->company_id);

        if ($task->project && $task->project->projectAdmin) {
            $arbitres = $arbitres->merge(collect([$task->project->projectAdmin]));
        }

        return $this->sansAuteurDuGeste($arbitres);
    }

    /**
     * Collaborateurs en charge de la tache. La diligence peut avoir coupe
     * leurs notifications : ce reglage existait deja et reste respecte.
     */
    private function assignes(Task $task)
    {
        if ($task->project_id && $task->project && $task->project->allow_collaborator_notification != 'enable') {
            return collect();
        }

        return $this->sansAuteurDuGeste($task->users);
    }

    /**
     * Retire du lot la personne qui vient d'agir, et les comptes sans
     * adresse : leur ecrire n'aurait aucun effet.
     */
    private function sansAuteurDuGeste($destinataires)
    {
        $auteur = user() ? user()->id : null;

        return collect($destinataires)
            ->filter(fn ($u) => $u && $u->id !== $auteur && !empty($u->email))
            ->unique('id')
            ->values();
    }

}
