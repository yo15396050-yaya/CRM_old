<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

/**
 * Indicateurs de la liste des clients.
 *
 * Ils portent sur l'etat du portefeuille plutot que sur son volume :
 * combien de contrats sont a renouveler, combien de clients n'en ont
 * aucun, et quelle part du portefeuille fait l'objet d'un suivi.
 *
 * Quatre requetes groupees, executees au chargement de la page.
 */
trait PilotageClients
{

    protected function clientsIndicateurs(): object
    {
        $id = company()->id;

        $clients = DB::table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.name', 'client')
            ->where('users.company_id', $id)
            ->distinct()
            ->pluck('users.id');

        $actifs = DB::table('users')
            ->whereIn('id', $clients)
            ->where('status', 'active')
            ->count();

        /* Contrats : ce qui est echu, et ce qui va l'etre */
        $contrats = DB::table('contracts')
            ->whereIn('client_id', $clients)
            ->whereNotNull('end_date')
            ->selectRaw('SUM(DATE(end_date) < CURDATE()) AS expires')
            ->selectRaw('SUM(DATE(end_date) >= CURDATE() AND DATE(end_date) <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)) AS bientot')
            ->first();

        $avecContrat = DB::table('contracts')
            ->whereIn('client_id', $clients)
            ->distinct()
            ->count('client_id');

        /* Part du portefeuille reellement suivie */
        $suivis = DB::table('projects')
            ->whereNull('deleted_at')
            ->whereIn('client_id', $clients)
            ->where('status', '<>', 'terminée')
            ->distinct()
            ->count('client_id');

        return (object) [
            'total'       => $clients->count(),
            'actifs'      => $actifs,
            'expires'     => (int) ($contrats->expires ?? 0),
            'bientot'     => (int) ($contrats->bientot ?? 0),
            'sansContrat' => max($clients->count() - $avecContrat, 0),
            'suivis'      => $suivis,
        ];
    }

}
