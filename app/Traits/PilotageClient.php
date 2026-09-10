<?php

namespace App\Traits;

use App\Models\TaskboardColumn;
use Illuminate\Support\Facades\DB;

/**
 * Indicateurs d'une fiche client.
 *
 * La fiche affichait des chiffres de facturation (revenus, factures
 * impayees) alors que le cabinet ne pilote pas son activite ainsi. Les
 * quatre indicateurs ci-dessous repondent aux questions qu'on se pose
 * devant un dossier client : ou en est le travail, est-il en retard, la
 * relation contractuelle est-elle en cours.
 *
 * Le pendant collectif est PilotageClients, a l'echelle de la liste.
 */
trait PilotageClient
{

    /**
     * @param int $clientId identifiant de l'utilisateur portant le role client
     */
    protected function clientIndicateurs(int $clientId): object
    {
        $termine = TaskboardColumn::completeColumn();
        $societe = company()->id;

        /* Diligences : le total et la part encore ouverte. Les statuts du
           cabinet sont libelles en francais, on ecarte l'achevee par son
           libelle sans tenir compte de la casse. */
        $diligences = DB::table('projects')
            ->whereNull('deleted_at')
            ->where('company_id', $societe)
            ->where('client_id', $clientId)
            ->selectRaw(
                "COUNT(*) AS total,
                 SUM(CASE WHEN LOWER(status) NOT IN ('terminée', 'finished')
                          THEN 1 ELSE 0 END) AS en_cours"
            )
            ->first();

        /* Charge et retards, portes par les taches des diligences du client. */
        $taches = DB::table('tasks')
            ->join('projects', 'projects.id', '=', 'tasks.project_id')
            ->whereNull('tasks.deleted_at')
            ->whereNull('projects.deleted_at')
            ->where('tasks.company_id', $societe)
            ->where('projects.client_id', $clientId)
            ->where('tasks.board_column_id', '<>', $termine->id)
            ->selectRaw(
                'COUNT(*) AS en_cours,
                 SUM(CASE WHEN tasks.due_date IS NOT NULL AND DATE(tasks.due_date) < CURDATE()
                          THEN 1 ELSE 0 END) AS retard'
            )
            ->first();

        /* Contrats : ceux dont la periode couvre aujourd'hui. Un contrat
           sans date de fin est considere comme toujours en cours. */
        $contrats = DB::table('contracts')
            ->where('company_id', $societe)
            ->where('client_id', $clientId)
            ->selectRaw(
                'COUNT(*) AS total,
                 SUM(CASE WHEN (start_date IS NULL OR DATE(start_date) <= CURDATE())
                            AND (end_date IS NULL OR DATE(end_date) >= CURDATE())
                          THEN 1 ELSE 0 END) AS actifs'
            )
            ->first();

        return (object) [
            'diligences'       => (int) ($diligences->total ?? 0),
            'diligencesOuvertes' => (int) ($diligences->en_cours ?? 0),
            'taches'           => (int) ($taches->en_cours ?? 0),
            'retard'           => (int) ($taches->retard ?? 0),
            'contrats'         => (int) ($contrats->total ?? 0),
            'contratsActifs'   => (int) ($contrats->actifs ?? 0),
        ];
    }

}
