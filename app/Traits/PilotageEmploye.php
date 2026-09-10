<?php

namespace App\Traits;

use App\Models\TaskboardColumn;
use Illuminate\Support\Facades\DB;

/**
 * Indicateurs d'une fiche collaborateur.
 *
 * La fiche repondait jusqu'ici avec des chiffres de presence, de conges
 * et de feuille de temps : des modules que le cabinet n'utilise pas. Les
 * quatre indicateurs ci-dessous repondent aux seules questions que l'on
 * se pose devant un collaborateur : de quoi a-t-il la charge, est-il en
 * retard, tient-il ses echeances, sur quels dossiers intervient-il.
 *
 * Le pendant collectif de ce trait est PilotageEmployes, qui repond aux
 * memes questions a l'echelle de la liste.
 */
trait PilotageEmploye
{

    /**
     * @param int $userId identifiant du collaborateur observe
     */
    protected function employeIndicateurs(int $userId): object
    {
        $termine = TaskboardColumn::completeColumn();
        $societe = company()->id;

        /* Charge courante et retards en une seule lecture : les deux
           portent sur le meme peripetre, les taches non terminees. */
        $charge = DB::table('task_users')
            ->join('tasks', 'tasks.id', '=', 'task_users.task_id')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.company_id', $societe)
            ->where('tasks.board_column_id', '<>', $termine->id)
            ->where('task_users.user_id', $userId)
            ->selectRaw(
                'COUNT(*) AS en_cours,
                 SUM(CASE WHEN tasks.due_date IS NOT NULL AND DATE(tasks.due_date) < CURDATE()
                          THEN 1 ELSE 0 END) AS retard,
                 SUM(CASE WHEN tasks.due_date IS NOT NULL AND DATE(tasks.due_date) = CURDATE()
                          THEN 1 ELSE 0 END) AS aujourdhui'
            )
            ->first();

        /* Respect des echeances : sur les taches achevees qui avaient une
           echeance, la part livree a temps. Les taches sans echeance sont
           exclues du calcul, sinon elles le gonflent artificiellement. */
        $tenue = DB::table('task_users')
            ->join('tasks', 'tasks.id', '=', 'task_users.task_id')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.company_id', $societe)
            ->where('tasks.board_column_id', $termine->id)
            ->where('task_users.user_id', $userId)
            ->whereNotNull('tasks.due_date')
            ->whereNotNull('tasks.completed_on')
            ->selectRaw(
                'COUNT(*) AS jugees,
                 SUM(CASE WHEN DATE(tasks.completed_on) <= DATE(tasks.due_date)
                          THEN 1 ELSE 0 END) AS a_temps'
            )
            ->first();

        $jugees = (int) ($tenue->jugees ?? 0);
        $aTemps = (int) ($tenue->a_temps ?? 0);

        /* Diligences en cours sur lesquelles il intervient. Les statuts
           du cabinet sont libelles en francais (« En cours », « terminée »)
           et non avec les valeurs d'origine de l'application : on ecarte
           donc l'achevee par son libelle, sans tenir compte de la casse. */
        $diligences = DB::table('project_members')
            ->join('projects', 'projects.id', '=', 'project_members.project_id')
            ->whereNull('projects.deleted_at')
            ->where('projects.company_id', $societe)
            ->where('project_members.user_id', $userId)
            ->whereRaw('LOWER(projects.status) NOT IN (?, ?)', ['terminée', 'finished'])
            ->distinct()
            ->count('projects.id');

        return (object) [
            'enCours'    => (int) ($charge->en_cours ?? 0),
            'retard'     => (int) ($charge->retard ?? 0),
            'aujourdhui' => (int) ($charge->aujourdhui ?? 0),
            'jugees'     => $jugees,
            'aTemps'     => $aTemps,
            /* Sans tache achevee jugeable, aucun taux n'a de sens : on
               renvoie null pour que la vue affiche un tiret plutot
               qu'un 0 % trompeur. */
            'respect'    => $jugees > 0 ? (int) round($aTemps / $jugees * 100) : null,
            'diligences' => $diligences,
        ];
    }

}
