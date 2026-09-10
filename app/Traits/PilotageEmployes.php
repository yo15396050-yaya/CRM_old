<?php

namespace App\Traits;

use App\Models\TaskboardColumn;
use Illuminate\Support\Facades\DB;

/**
 * Indicateurs de la liste des employes.
 *
 * Ils repondent a une question de repartition, pas de comptage : qui est
 * disponible, qui est sature, ou sont les retards. Un simple total
 * d'effectif ne dirait rien qu'on ne sache deja.
 *
 * Quatre requetes groupees, executees une seule fois au chargement de la
 * page. Rien n'est calcule dans la vue.
 */
trait PilotageEmployes
{

    /**
     * Roles consideres comme faisant partie de l'effectif du cabinet.
     * Les clients et les apporteurs en sont exclus.
     */
    private function rolesEffectif(): array
    {
        return ['employee', 'employee-stagiaires', 'stagiaires-it', 'empyohou', 'dg-dck', 'dga-dck'];
    }

    protected function employesIndicateurs(): object
    {
        $id = company()->id;
        $termine = TaskboardColumn::completeColumn();

        /* Effectif actif */
        $effectif = DB::table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->whereIn('roles.name', $this->rolesEffectif())
            ->where('users.company_id', $id)
            ->where('users.status', 'active')
            ->distinct()
            ->pluck('users.id');

        /* Charge : nombre de taches ouvertes portees par chacun */
        $charges = DB::table('task_users')
            ->join('tasks', 'tasks.id', '=', 'task_users.task_id')
            ->join('users', 'users.id', '=', 'task_users.user_id')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.company_id', $id)
            ->where('tasks.board_column_id', '<>', $termine->id)
            ->whereIn('task_users.user_id', $effectif)
            ->selectRaw('users.id, users.name, COUNT(*) AS n')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('n')
            ->get();

        $plusCharge = $charges->first();

        /* Retards : taches depassees et nombre de personnes concernees */
        $retards = DB::table('task_users')
            ->join('tasks', 'tasks.id', '=', 'task_users.task_id')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.company_id', $id)
            ->where('tasks.board_column_id', '<>', $termine->id)
            ->whereNotNull('tasks.due_date')
            ->whereDate('tasks.due_date', '<', now())
            ->whereIn('task_users.user_id', $effectif)
            ->selectRaw('COUNT(DISTINCT tasks.id) AS taches, COUNT(DISTINCT task_users.user_id) AS gens')
            ->first();

        return (object) [
            'effectif'     => $effectif->count(),
            'sansTache'    => max($effectif->count() - $charges->count(), 0),
            'chargeMax'    => $plusCharge ? (int) $plusCharge->n : 0,
            'chargeQui'    => $plusCharge ? $plusCharge->name : null,
            'retards'      => (int) ($retards->taches ?? 0),
            'retardsGens'  => (int) ($retards->gens ?? 0),
        ];
    }

}
