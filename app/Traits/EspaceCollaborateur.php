<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\TaskboardColumn;
use Illuminate\Support\Facades\DB;

/**
 * Donnees propres au tableau de bord du collaborateur, tel que la
 * maquette dck-flow-task le decrit sous l'ecran « Mon tableau de bord ».
 *
 * EmployeeDashboard prepare deja l'essentiel : pendingTasks,
 * inProcessTasks, dueTasks, notices, weekWiseTimelogs, currentClockIn,
 * totalTimeFormatted et counts. Ce trait n'ajoute que les trois
 * elements qui manquaient, sans recalculer ce qui existe.
 */
trait EspaceCollaborateur
{
    /**
     * Diligences dont le collaborateur est membre, avec leur
     * avancement et le detail des taches terminees. Une seule requete :
     * les deux comptages passent par des sous-selections.
     */
    protected function espaceDiligences(int $userId, int $limite = 4)
    {
        $termine = TaskboardColumn::completeColumn();

        return Project::query()
            ->select('projects.id', 'projects.project_name', 'projects.completion_percent')
            ->selectSub(
                DB::table('tasks')
                    ->selectRaw('count(*)')
                    ->whereColumn('tasks.project_id', 'projects.id')
                    ->whereNull('tasks.deleted_at'),
                'taches_total'
            )
            ->selectSub(
                DB::table('tasks')
                    ->selectRaw('count(*)')
                    ->whereColumn('tasks.project_id', 'projects.id')
                    ->whereNull('tasks.deleted_at')
                    ->where('tasks.board_column_id', $termine->id),
                'taches_faites'
            )
            ->where('projects.company_id', company()->id)
            ->whereNull('projects.deleted_at')
            ->whereIn('projects.id', function ($q) use ($userId) {
                $q->select('project_id')->from('project_members')->where('user_id', $userId);
            })
            ->orderByDesc('projects.updated_at')
            ->limit($limite)
            ->get();
    }

    /**
     * Taches terminees par le collaborateur depuis le debut du mois.
     */
    protected function espaceTermineesCeMois(int $userId): int
    {
        $termine = TaskboardColumn::completeColumn();

        return DB::table('tasks')
            ->join('task_users', 'task_users.task_id', '=', 'tasks.id')
            ->where('task_users.user_id', $userId)
            ->where('tasks.board_column_id', $termine->id)
            ->whereNull('tasks.deleted_at')
            ->where('tasks.updated_at', '>=', now()->startOfMonth())
            ->distinct()
            ->count('tasks.id');
    }


    /**
     * Minutes pointees jour par jour sur la semaine en cours.
     *
     * EmployeeDashboard expose weekWiseTimelogs, mais c'est un TOTAL en
     * minutes, pas un detail. La maquette demande une barre par jour :
     * une requete groupee la fournit, sans boucler sur les jours.
     *
     * @return array<int, array{jour: \Carbon\Carbon, minutes: int}>
     */
    protected function espaceSemaine(int $userId): array
    {
        $debut = now()->startOfWeek();
        $fin = now()->endOfWeek();

        $parJour = DB::table('project_time_logs')
            ->selectRaw('DATE(start_time) as j, SUM(total_minutes) as m')
            ->where('user_id', $userId)
            ->where('company_id', company()->id)
            ->whereBetween(DB::raw('DATE(start_time)'), [$debut->toDateString(), $fin->toDateString()])
            ->groupBy('j')
            ->pluck('m', 'j');

        $jours = [];
        $curseur = $debut->copy();

        while ($curseur->lessThanOrEqualTo($fin)) {
            $jours[] = [
                'jour'    => $curseur->copy(),
                'minutes' => (int) ($parJour[$curseur->toDateString()] ?? 0),
            ];
            $curseur->addDay();
        }

        return $jours;
    }

    /**
     * Renseigne les variables attendues par la vue mon-espace.
     */
    protected function chargerEspaceCollaborateur(int $userId): void
    {
        $this->espaceDiligences     = $this->espaceDiligences($userId);
        $this->espaceTermineesMois  = $this->espaceTermineesCeMois($userId);
        $this->espaceSemaine        = $this->espaceSemaine($userId);
    }
}
