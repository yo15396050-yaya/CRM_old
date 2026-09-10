<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskboardColumn;
use Illuminate\Support\Facades\DB;

/**
 * Donnees de l'ecran « Tableau de bord » du cabinet, tel que
 * PAGES.dashboard le decrit dans la maquette dck-flow-task :
 *
 *   quatre indicateurs — taches en cours, diligences actives,
 *   contrats en vigueur, collaborateurs ;
 *   la charge de travail sur six mois, creees contre terminees ;
 *   la repartition des taches par colonne du tableau ;
 *   les diligences dont l'echeance approche ;
 *   le taux de cloture dans les delais.
 *
 * OverviewDashboard fournit deja $counts et $projectActivities : ils ne
 * sont pas recalcules ici.
 */
trait PilotageCabinet
{
    /** Les quatre indicateurs de tete. */
    protected function cabinetIndicateurs(): object
    {
        $id = company()->id;
        $termine = TaskboardColumn::completeColumn();
        $moisDernier = now()->copy()->subDays(30);

        $taches = DB::table('tasks')
            ->selectRaw('COUNT(*) as ouvertes')
            ->selectRaw('SUM(created_at >= ?) as recentes', [$moisDernier])
            ->where('company_id', $id)
            ->whereNull('deleted_at')
            ->where('board_column_id', '<>', $termine->id)
            ->where('is_private', 0)
            ->first();

        $diligences = DB::table('projects')
            ->selectRaw("SUM(status <> 'terminée') as actives")
            ->selectRaw("SUM(status = 'terminée' AND updated_at >= ?) as clotureesMois", [now()->startOfMonth()])
            ->where('company_id', $id)
            ->whereNull('deleted_at')
            ->first();

        $contrats = DB::table('contracts')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(end_date IS NOT NULL AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 45 DAY)) as bientot')
            // La table contracts ne porte pas de suppression douce.
            ->where('company_id', $id)
            ->first();

        $collaborateurs = DB::table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.name', 'employee')
            ->where('users.status', 'active')
            ->where('users.company_id', $id)
            ->distinct()
            ->count('users.id');

        /* Volume du mois et taux de cloture : calcules sur les seules
           taches, les diligences etant exclues du calcul de productivite. */
        $mois = DB::table('tasks')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(board_column_id = ?) as terminees', [$termine->id])
            ->where('company_id', $id)
            ->whereNull('deleted_at')
            ->where('is_private', 0)
            ->where('created_at', '>=', now()->startOfMonth())
            ->first();

        $totalMois = (int) $mois->total;
        $finiesMois = (int) $mois->terminees;

        /* Taux de cloture sur l'ensemble des taches, et non sur le seul
           mois courant : un taux calcule sur le mois est vide les premiers
           jours et n'a aucune valeur de pilotage. Les diligences restent
           exclues, comme le demande la specification. */
        $global = DB::table('tasks')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(board_column_id = ?) as terminees', [$termine->id])
            ->where('company_id', $id)
            ->whereNull('deleted_at')
            ->where('is_private', 0)
            ->first();

        $totalGlobal = (int) $global->total;

        /* Backlog : ce qui attend d'etre pris en charge, c'est-a-dire les
           colonnes d'entree du tableau, hors travail deja engage. */
        $backlog = DB::table('tasks')
            ->join('taskboard_columns', 'taskboard_columns.id', '=', 'tasks.board_column_id')
            ->whereIn('taskboard_columns.slug', ['to_do', 'incomplete'])
            ->where('tasks.company_id', $id)
            ->whereNull('tasks.deleted_at')
            ->where('tasks.is_private', 0)
            ->count();

        $clients = DB::table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.name', 'client')
            ->where('users.status', 'active')
            ->where('users.company_id', $id)
            ->distinct()
            ->count('users.id');

        /* Respect des echeances et delai de traitement.
           Les deux se lisent sur les taches cloturees qui portaient une
           echeance : « completed_on » donne la date de cloture reelle. */
        $delais = DB::table('tasks')
            ->where('company_id', $id)
            ->whereNull('deleted_at')
            ->where('board_column_id', $termine->id)
            ->whereNotNull('completed_on')
            ->selectRaw('AVG(DATEDIFF(completed_on, DATE(created_at))) AS delai')
            ->selectRaw('SUM(due_date IS NOT NULL AND completed_on <= DATE(due_date)) AS aTemps')
            ->selectRaw('SUM(due_date IS NOT NULL) AS avecEcheance')
            ->first();

        $avecEcheance = (int) ($delais->avecEcheance ?? 0);

        /* Valeur du portefeuille : contrats encore en cours, c'est-a-dire
           sans date de fin ou dont la date n'est pas passee. */
        $portefeuille = DB::table('contracts')
            ->where('company_id', $id)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhereDate('end_date', '>=', now()))
            ->selectRaw('SUM(original_amount) AS montant, COUNT(*) AS n')
            ->first();

        return (object) [
            'tachesOuvertes'  => (int) $taches->ouvertes,
            'tachesRecentes'  => (int) $taches->recentes,
            'diligences'      => (int) $diligences->actives,
            'diligencesMois'  => (int) $diligences->clotureesMois,
            'contrats'        => (int) $contrats->total,
            'contratsBientot' => (int) $contrats->bientot,
            'collaborateurs'  => $collaborateurs,
            'tachesMois'      => $totalMois,
            'tachesFiniesMois' => $finiesMois,
            'tauxTaches'      => $totalGlobal > 0 ? (int) round(100 * $global->terminees / $totalGlobal) : null,
            'tachesTotal'     => $totalGlobal,
            'tachesFinies'    => (int) $global->terminees,
            'backlog'         => $backlog,
            'clients'         => $clients,

            'delaiMoyen'      => is_null($delais->delai) ? null : round((float) $delais->delai, 1),
            'respect'         => $avecEcheance > 0
                ? (int) round(100 * $delais->aTemps / $avecEcheance)
                : null,
            'respectATemps'   => (int) ($delais->aTemps ?? 0),
            'respectTotal'    => $avecEcheance,
            'portefeuille'    => (float) ($portefeuille->montant ?? 0),
            'portefeuilleN'   => (int) ($portefeuille->n ?? 0),
        ];
    }

    /**
     * Charge de travail sur six mois : taches creees et taches
     * terminees, mois par mois. Deux requetes groupees, pas de boucle
     * de requetes.
     *
     * @return array{labels: string[], creees: int[], terminees: int[]}
     */
    protected function cabinetChargeTravail(): array
    {
        $id = company()->id;
        $termine = TaskboardColumn::completeColumn();
        $debut = now()->startOfMonth()->subMonths(5);

        $creees = DB::table('tasks')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as m, COUNT(*) as n")
            ->where('company_id', $id)->whereNull('deleted_at')
            ->where('created_at', '>=', $debut)
            ->groupBy('m')->pluck('n', 'm');

        $finies = DB::table('tasks')
            ->selectRaw("DATE_FORMAT(updated_at, '%Y-%m') as m, COUNT(*) as n")
            ->where('company_id', $id)->whereNull('deleted_at')
            ->where('board_column_id', $termine->id)
            ->where('updated_at', '>=', $debut)
            ->groupBy('m')->pluck('n', 'm');

        $labels = [];
        $a = [];
        $b = [];
        $curseur = $debut->copy();

        for ($i = 0; $i < 6; $i++) {
            $cle = $curseur->format('Y-m');
            $labels[] = ucfirst($curseur->translatedFormat('M'));
            $a[] = (int) ($creees[$cle] ?? 0);
            $b[] = (int) ($finies[$cle] ?? 0);
            $curseur->addMonth();
        }

        return ['labels' => $labels, 'creees' => $a, 'terminees' => $b];
    }

    /**
     * Repartition des taches par colonne du tableau, avec la couleur
     * definie sur la colonne elle-meme.
     */
    protected function cabinetRepartitionTaches()
    {
        return TaskboardColumn::query()
            ->select('id', 'column_name', 'label_color', 'slug')
            ->where('company_id', company()->id)
            ->withCount(['tasks as n' => function ($q) {
                $q->whereNull('tasks.deleted_at')->where('tasks.is_private', 0);
            }])
            ->orderBy('priority')
            ->get();
    }

    /** Diligences ouvertes dont l'echeance tombe dans moins de 45 jours. */
    /**
     * Collaborateurs ayant cloture le plus de taches sur le trimestre.
     *
     * La fenetre est volontairement large : sur un mois, le classement
     * tient a deux ou trois taches et change tous les jours.
     */
    protected function cabinetTopCollaborateurs(int $limite = 5)
    {
        $termine = TaskboardColumn::completeColumn();

        return DB::table('task_users')
            ->join('tasks', 'tasks.id', '=', 'task_users.task_id')
            ->join('users', 'users.id', '=', 'task_users.user_id')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.company_id', company()->id)
            ->where('tasks.board_column_id', $termine->id)
            ->whereDate('tasks.updated_at', '>=', now()->subDays(90))
            ->selectRaw('users.id, users.name, COUNT(*) AS n')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('n')
            ->limit($limite)
            ->get();
    }

    /**
     * Taches critiques : celles dont l'echeance approche ou est passee,
     * et qui ne sont ni terminees ni classees incompletes.
     *
     * Le bloc portait auparavant sur les diligences. Il suit desormais
     * les taches, seule maille sur laquelle une action est possible :
     * une diligence ne se traite pas, ses taches si.
     */
    protected function cabinetTachesASurveiller(int $limite = 6)
    {
        $closes = TaskboardColumn::where('company_id', company()->id)
            ->whereIn('slug', ['completed', 'incomplete'])
            ->pluck('id');

        return Task::with(['boardColumn:id,column_name,label_color', 'users:id,name'])
            ->select('id', 'heading', 'due_date', 'board_column_id', 'priority')
            ->where('company_id', company()->id)
            ->whereNull('deleted_at')
            ->whereNotIn('board_column_id', $closes)
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays(15))
            ->orderBy('due_date')
            ->limit($limite)
            ->get();
    }

    /**
     * Part des diligences terminees livrees avant leur echeance.
     *
     * Meme reserve que sur les autres indicateurs de delai : la table
     * ne conserve pas de date de cloture, updated_at en tient lieu.
     */
    protected function cabinetTauxCloture(): ?int
    {
        $r = DB::table('projects')
            ->selectRaw("SUM(status = 'terminée' AND deadline IS NOT NULL) as base")
            ->selectRaw("SUM(status = 'terminée' AND deadline IS NOT NULL AND DATE(updated_at) <= deadline) as dansLesDelais")
            ->where('company_id', company()->id)
            ->whereNull('deleted_at')
            ->first();

        return $r->base ? (int) round(100 * $r->dansLesDelais / $r->base) : null;
    }

    /** Renseigne les variables attendues par la vue de pilotage cabinet. */
    protected function chargerPilotageCabinet(): void
    {
        $this->cabIndicateurs  = $this->cabinetIndicateurs();
        $this->cabCharge       = $this->cabinetChargeTravail();
        $this->cabRepartition  = $this->cabinetRepartitionTaches();
        $this->cabASurveiller  = $this->cabinetTachesASurveiller();
        $this->cabTauxCloture  = $this->cabinetTauxCloture();
        $this->cabTopCollab    = $this->cabinetTopCollaborateurs();
    }
}
