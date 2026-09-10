<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\RegulatoryDeadline;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Donnees du bloc de pilotage, conformes a la maquette
 * pilotage-dc-knowing : indicateurs, livraisons mensuelles, repartition
 * du portefeuille, projets a traiter et echeancier reglementaire.
 *
 * Le trait sert deux tableaux de bord :
 *
 *   - administrateur : tout le portefeuille de l'entreprise ;
 *   - collaborateur  : uniquement les projets dont il est membre.
 *
 * La restriction passe par $pilotageUserId. Laisse a null, la portee
 * reste celle de l'entreprise. Renseigne, chaque requete est filtree
 * par une sous-selection sur project_members — jamais une jointure,
 * qui dupliquerait les lignes et fausserait les comptages.
 */
trait PilotageProjets
{
    /** Restreint le pilotage aux projets d'un collaborateur. */
    protected ?int $pilotageUserId = null;

    /**
     * Socle commun a toutes les requetes : projets vivantes de
     * l'entreprise, restreintes au collaborateur le cas echeant.
     */
    private function requetePilotage(): Builder
    {
        $q = DB::table('projects')
            ->where('company_id', company()->id)
            ->whereNull('deleted_at');

        if ($this->pilotageUserId) {
            $q->whereIn('id', function ($sous) {
                $sous->select('project_id')
                    ->from('project_members')
                    ->where('user_id', $this->pilotageUserId);
            });
        }

        return $q;
    }

    /**
     * Les quatre indicateurs de tete.
     *
     * Reserve sur le dernier : la table projects ne conserve aucune date
     * de livraison. On approche la cloture par updated_at, qui bouge a
     * chaque modification — le pourcentage est donc indicatif.
     */
    protected function calculerProjetKpis()
    {
        $k = $this->requetePilotage()
            ->selectRaw('COUNT(*) as suivies')
            ->selectRaw("SUM(status = 'En cours') as enCours")
            ->selectRaw("SUM(deadline IS NOT NULL AND deadline < CURDATE() AND status NOT IN ('terminée','annulée')) as enRetard")
            ->selectRaw("SUM(status = 'terminée' AND deadline IS NOT NULL) as termineesAvecEcheance")
            ->selectRaw("SUM(status = 'terminée' AND deadline IS NOT NULL AND DATE(updated_at) <= deadline) as dansLesDelais")
            ->selectRaw('SUM(created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as creees30j')
            ->first();

        $suivies = (int) $k->suivies;
        $avecEcheance = (int) $k->termineesAvecEcheance;

        return (object) [
            'suivies'    => $suivies,
            'enCours'    => (int) $k->enCours,
            'enRetard'   => (int) $k->enRetard,
            'creees30j'  => (int) $k->creees30j,
            'pctEnCours' => $suivies ? round(100 * $k->enCours / $suivies) : 0,
            'pctRetard'  => $suivies ? round(100 * $k->enRetard / $suivies) : 0,
            'pctDelais'  => $avecEcheance ? round(100 * $k->dansLesDelais / $avecEcheance) : null,
            'termineesAvecEcheance' => $avecEcheance,
        ];
    }

    /**
     * Projets terminees par mois sur douze mois glissants.
     * Meme reserve que ci-dessus sur updated_at.
     *
     * @return array{labels: string[], valeurs: int[], total: int}
     */
    protected function livraisonsParMois()
    {
        $debut = now()->startOfMonth()->subMonths(11);

        $lignes = $this->requetePilotage()
            ->selectRaw("DATE_FORMAT(updated_at, '%Y-%m') as mois, COUNT(*) as n")
            ->where('status', 'terminée')
            ->where('updated_at', '>=', $debut)
            ->groupBy('mois')
            ->pluck('n', 'mois');

        $labels = [];
        $valeurs = [];
        $curseur = $debut->copy();

        for ($i = 0; $i < 12; $i++) {
            $labels[] = ucfirst($curseur->translatedFormat('M'));
            $valeurs[] = (int) ($lignes[$curseur->format('Y-m')] ?? 0);
            $curseur->addMonth();
        }

        return ['labels' => $labels, 'valeurs' => $valeurs, 'total' => array_sum($valeurs)];
    }

    /**
     * Repartition du portefeuille en quatre etats.
     *
     * « En retard » n'est pas un statut stocke : il se deduit d'une
     * echeance depassee sur un projet ni terminee ni annulee, et
     * prime sur le statut d'origine pour ne pas compter deux fois la
     * meme ligne.
     */
    protected function repartitionPortefeuille()
    {
        $r = $this->requetePilotage()
            ->selectRaw("SUM(status = 'terminée') as terminees")
            ->selectRaw("SUM(deadline IS NOT NULL AND deadline < CURDATE() AND status NOT IN ('terminée','annulée')) as enRetard")
            ->selectRaw("SUM(status = 'En cours' AND NOT (deadline IS NOT NULL AND deadline < CURDATE())) as enCours")
            ->selectRaw("SUM(status NOT IN ('terminée','En cours','annulée') AND NOT (deadline IS NOT NULL AND deadline < CURDATE())) as aFaire")
            ->first();

        $etats = [
            ['terminees', __('Terminées'), (int) $r->terminees, '#0E7C5A'],
            ['enCours',   __('En cours'),  (int) $r->enCours,   '#175CD3'],
            ['aFaire',    __('À faire'),   (int) $r->aFaire,    '#667085'],
            ['enRetard',  __('En retard'), (int) $r->enRetard,  '#B42318'],
        ];

        $total = array_sum(array_column($etats, 2));

        return array_map(fn ($e) => [
            'cle'     => $e[0],
            'libelle' => $e[1],
            'n'       => $e[2],
            'pct'     => $total ? round(100 * $e[2] / $total, 1) : 0,
            'couleur' => $e[3],
        ], $etats);
    }

    /**
     * Projets ouvertes les plus proches de leur echeance.
     * Relations prechargees : aucune requete par ligne.
     */
    protected function projetsATraiter(int $limite = 6)
    {
        $q = Project::with(['client:id,name,image'])
            ->select('id', 'project_name', 'project_short_code', 'client_id', 'deadline', 'status', 'completion_percent')
            ->where('company_id', company()->id)
            ->whereNull('deleted_at')
            ->where('status', '<>', 'terminée');

        if ($this->pilotageUserId) {
            $q->whereHas('members', fn ($m) => $m->where('user_id', $this->pilotageUserId));
        }

        return $q->orderByRaw('deadline IS NULL, deadline ASC')->limit($limite)->get();
    }

    /**
     * Renseigne les cinq variables attendues par la vue de pilotage.
     * Le tableau de bord appelant n'a plus qu'a inclure la vue.
     */
    protected function chargerPilotage(?int $userId = null): void
    {
        $this->pilotageUserId = $userId;

        $this->projetKpis   = $this->calculerProjetKpis();
        $this->dashLivraisons  = $this->livraisonsParMois();
        $this->dashRepartition = $this->repartitionPortefeuille();
        $this->dashATraiter    = $this->projetsATraiter();
        // L'echeancier est un referentiel commun : aucune donnee propre
        // a un client, il est donc identique pour tous les roles.
        $this->dashEcheances   = RegulatoryDeadline::prochaines(30, company()->id);
    }
}
