<?php

namespace App\Traits;

use App\Models\TaskboardColumn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Rapport hebdomadaire des taches.
 *
 * Regle de semaine, unique et appliquee partout : du lundi 00:00 au
 * dimanche 23:59:59, dans le fuseau de la societe.
 *
 * Perimetre d'une tache : elle appartient a la semaine si l'un de ces
 * faits y tombe — elle a ete achevee, son echeance arrivait, ou elle a
 * change de statut. Se limiter aux taches achevees masquerait le travail
 * en cours ; se limiter aux echeances masquerait ce qui a ete livre en
 * avance. Les trois ensemble donnent l'activite reelle de la semaine.
 *
 * Le suivi du temps existe dans l'application (project_time_logs) mais
 * n'est pas utilise par le cabinet : sept saisies en tout, toutes de
 * 2024. La mesure de reference est donc le delai de traitement, en
 * jours entre le debut et l'achevement. Le temps pointe reste lu et
 * affiche quand il existe.
 */
trait RapportHebdomadaire
{

    /**
     * Bornes de la semaine demandee.
     *
     * @param string|null $semaine au format ISO « 2026-W36 », ou null
     *                             pour la semaine courante
     */
    protected function bornesSemaine(?string $semaine = null): array
    {
        $fuseau = company()->timezone;

        if ($semaine && preg_match('/^(\d{4})-W(\d{1,2})$/', $semaine, $m)) {
            $debut = Carbon::now($fuseau)
                ->setISODate((int) $m[1], (int) $m[2])
                ->startOfWeek(Carbon::MONDAY);
        }
        else {
            $debut = Carbon::now($fuseau)->startOfWeek(Carbon::MONDAY);
        }

        return [$debut->copy()->startOfDay(), $debut->copy()->endOfWeek(Carbon::SUNDAY)];
    }

    /**
     * Identifiants des taches appartenant a la semaine.
     *
     * Une seule requete, qui ne ramene que des identifiants : le detail
     * est charge ensuite pour ces seules taches, jamais pour la base
     * entiere.
     */
    private function tachesDeLaSemaine(Carbon $debut, Carbon $fin, ?int $limiteA = null)
    {
        /* L'historique est lu a part, puis injecte comme simple liste
           d'identifiants.

           Une sous-requete correlee « EXISTS » sur task_history serait
           plus concise, mais cette table ne porte aucun index hormis sa
           clef primaire : EXPLAIN montre alors un parcours complet de
           task_history repete pour chaque tache examinee. En deux
           lectures independantes, chaque table n'est parcourue qu'une
           fois. */
        $bouges = DB::table('task_history')
            ->whereBetween('created_at', [$debut, $fin])
            ->distinct()
            ->pluck('task_id')
            ->filter()
            ->all();

        $requete = DB::table('tasks')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.company_id', company()->id)
            ->where(function ($q) use ($debut, $fin, $bouges) {
                $q->whereBetween('tasks.completed_on', [$debut, $fin])
                  ->orWhereBetween('tasks.due_date', [$debut, $fin]);

                if ($bouges) {
                    $q->orWhereIn('tasks.id', $bouges);
                }
            });

        /* Restriction a un collaborateur : le filtre porte sur la table
           d'affectation, donc une tache partagee reste visible pour
           chacune des personnes qui y travaillent. */
        if ($limiteA !== null) {
            $requete->whereExists(function ($q) use ($limiteA) {
                $q->select(DB::raw(1))
                  ->from('task_users')
                  ->whereColumn('task_users.task_id', 'tasks.id')
                  ->where('task_users.user_id', $limiteA);
            });
        }

        return $requete->pluck('tasks.id');
    }

    /**
     * Le rapport complet : liste, synthese et activite par collaborateur.
     *
     * @param string|null $semaine semaine ISO, ou null pour la courante
     * @param int|null    $limiteA identifiant du collaborateur auquel
     *                             restreindre le rapport ; null pour
     *                             tout le cabinet
     */
    protected function rapportHebdomadaire(?string $semaine = null, ?int $limiteA = null): object
    {
        [$debut, $fin] = $this->bornesSemaine($semaine);

        $ids = $this->tachesDeLaSemaine($debut, $fin, $limiteA);

        $vide = (object) [
            'debut'         => $debut,
            'fin'           => $fin,
            'limiteA'       => $limiteA,
            'semaineIso'    => $debut->format('o-\WW'),
            'taches'        => collect(),
            'collaborateurs' => collect(),
            'synthese'      => (object) [
                'total' => 0, 'termines' => 0, 'enCours' => 0, 'aFaire' => 0,
                'incompletes' => 0, 'attente' => 0, 'retards' => 0,
                'delaiMoyen' => null, 'delaisJuges' => 0,
                'pointeHeures' => 0, 'pointeTaches' => 0, 'gens' => 0,
            ],
        ];

        if ($ids->isEmpty()) {
            return $vide;
        }

        /* Liste : jointures a gauche, car une tache peut n'avoir ni
           diligence ni client sans devoir disparaitre du rapport. */
        $taches = DB::table('tasks')
            ->leftJoin('taskboard_columns', 'taskboard_columns.id', '=', 'tasks.board_column_id')
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->leftJoin('users AS cli', 'cli.id', '=', 'projects.client_id')
            ->whereIn('tasks.id', $ids)
            ->select(
                'tasks.id', 'tasks.task_short_code', 'tasks.heading', 'tasks.priority',
                'tasks.start_date', 'tasks.due_date', 'tasks.completed_on', 'tasks.updated_at',
                'tasks.estimate_hours', 'tasks.estimate_minutes',
                'taskboard_columns.column_name', 'taskboard_columns.slug', 'taskboard_columns.label_color',
                'projects.id AS projet_id', 'projects.project_name', 'cli.name AS client'
            )
            ->orderByDesc('tasks.updated_at')
            ->get();

        /* Collaborateurs de toutes les taches en une requete : sans cela,
           une lecture par ligne produirait autant de requetes que de
           taches affichees. */
        $affectations = DB::table('task_users')
            ->join('users', 'users.id', '=', 'task_users.user_id')
            ->whereIn('task_users.task_id', $ids)
            ->select('task_users.task_id', 'users.id', 'users.name')
            ->get()
            ->groupBy('task_id');

        /* Temps pointe, agrege en base plutot qu'en PHP. */
        $pointes = DB::table('project_time_logs')
            ->whereIn('task_id', $ids)
            ->selectRaw('task_id, SUM(total_minutes) AS minutes')
            ->groupBy('task_id')
            ->pluck('minutes', 'task_id');

        $aujourdhui = Carbon::now(company()->timezone);

        $lignes = $taches->map(function ($t) use ($affectations, $pointes, $aujourdhui) {
            $gens = ($affectations[$t->id] ?? collect())
                ->map(fn($u) => (object) ['id' => $u->id, 'nom' => $u->name])
                ->values();

            $delai = null;

            if ($t->completed_on && $t->start_date) {
                $delai = Carbon::parse($t->start_date)
                    ->startOfDay()
                    ->diffInDays(Carbon::parse($t->completed_on)->startOfDay());
            }

            return (object) [
                'id'        => $t->id,
                'reference' => $t->task_short_code,
                'titre'     => $t->heading,
                'client'    => $t->client,
                'diligence' => $t->project_name,
                'projetId'  => $t->projet_id,
                'debut'     => $t->start_date ? Carbon::parse($t->start_date) : null,
                'echeance'  => $t->due_date ? Carbon::parse($t->due_date) : null,
                'faitLe'    => $t->completed_on ? Carbon::parse($t->completed_on) : null,
                'statut'    => $t->column_name,
                'slug'      => $t->slug,
                'couleur'   => $t->label_color,
                'priorite'  => $t->priority,
                'prevu'     => $t->estimate_hours || $t->estimate_minutes
                    ? trim(($t->estimate_hours ? $t->estimate_hours . ' h ' : '')
                         . ($t->estimate_minutes ? $t->estimate_minutes . ' min' : ''))
                    : null,
                'pointe'    => isset($pointes[$t->id]) ? round($pointes[$t->id] / 60, 1) : null,
                'delai'     => $delai,
                'gens'      => $gens,
                'maj'       => Carbon::parse($t->updated_at),
                /* « En retard » n'est pas un statut du tableau : c'est une
                   tache non achevee dont l'echeance est depassee. */
                'retard'    => $t->slug !== 'completed'
                               && $t->due_date
                               && Carbon::parse($t->due_date)->endOfDay()->lt($aujourdhui),
            ];
        });

        return (object) [
            'debut'          => $debut,
            'fin'            => $fin,
            'limiteA'        => $limiteA,
            'semaineIso'     => $debut->format('o-\WW'),
            'taches'         => $lignes,
            'synthese'       => $this->syntheseHebdomadaire($lignes),
            'collaborateurs' => $this->activiteParCollaborateur($lignes),
        ];
    }

    private function syntheseHebdomadaire($lignes): object
    {
        $delais  = $lignes->pluck('delai')->filter(fn($x) => $x !== null);
        $pointes = $lignes->pluck('pointe')->filter(fn($x) => $x !== null);

        $gens = $lignes->flatMap(fn($t) => $t->gens->pluck('id'))->unique();

        return (object) [
            'total'        => $lignes->count(),
            'termines'     => $lignes->where('slug', 'completed')->count(),
            'enCours'      => $lignes->where('slug', 'doing')->count(),
            'aFaire'       => $lignes->where('slug', 'to_do')->count(),
            'incompletes'  => $lignes->where('slug', 'incomplete')->count(),
            'attente'      => $lignes->where('slug', 'waiting_approval')->count(),
            'retards'      => $lignes->where('retard', true)->count(),
            'delaiMoyen'   => $delais->isNotEmpty() ? round($delais->avg(), 1) : null,
            'delaisJuges'  => $delais->count(),
            'pointeHeures' => $pointes->isNotEmpty() ? round($pointes->sum(), 1) : 0,
            'pointeTaches' => $pointes->count(),
            'gens'         => $gens->count(),
        ];
    }

    private function activiteParCollaborateur($lignes)
    {
        $par = [];

        foreach ($lignes as $t) {
            foreach ($t->gens as $g) {
                $par[$g->id] ??= (object) [
                    'id' => $g->id, 'nom' => $g->nom,
                    'total' => 0, 'termines' => 0, 'enCours' => 0,
                    'aFaire' => 0, 'retards' => 0, 'delais' => [], 'pointe' => 0,
                ];

                $l = $par[$g->id];
                $l->total++;

                if ($t->slug === 'completed') { $l->termines++; }
                if ($t->slug === 'doing')     { $l->enCours++; }
                if ($t->slug === 'to_do')     { $l->aFaire++; }
                if ($t->retard)               { $l->retards++; }
                if ($t->delai !== null)       { $l->delais[] = $t->delai; }
                if ($t->pointe !== null)      { $l->pointe += $t->pointe; }
            }
        }

        return collect($par)
            ->map(function ($l) {
                $l->delaiMoyen = $l->delais
                    ? round(array_sum($l->delais) / count($l->delais), 1)
                    : null;
                unset($l->delais);

                return $l;
            })
            ->sortByDesc('total')
            ->values();
    }

}
