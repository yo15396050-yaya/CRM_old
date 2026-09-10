<?php

namespace App\Traits;

use App\Models\Task;
use App\Models\TaskboardColumn;

/**
 * Regle d'acces a l'etape « En attente d'approbation ».
 *
 * Seul un administrateur peut deplacer une tache VERS cette colonne
 * ou l'en faire SORTIR. La regle est portee ici, et non dans les vues,
 * pour qu'un appel direct a l'API soit refuse au meme titre qu'un clic :
 * masquer un bouton ne securise rien.
 *
 * Le flux « envoyer pour approbation » reste ouvert aux collaborateurs
 * concernes par la tache : c'est une demande, pas un arbitrage. Il passe
 * par estAutoriseAEnvoyerEnApprobation().
 */
trait ApprobationTache
{

    /**
     * Slug de la colonne d'approbation, tel qu'il est enregistre en base.
     */
    private function slugApprobation(): string
    {
        return 'waiting_approval';
    }

    /**
     * L'utilisateur courant peut-il arbitrer ?
     *
     * Trois cas : l'administrateur, la direction (DG et DGA, voir
     * roles_arbitres()), et l'administrateur du projet porteur, assimile a
     * un arbitre pour ses propres taches — c'est deja le cas dans toutes
     * les autres regles du module.
     */
    private function estArbitre(?Task $task = null): bool
    {
        if (est_arbitre()) {
            return true;
        }

        return $task && $task->project && $task->project->project_admin == user()->id;
    }

    /**
     * Refuse la transition si elle entre dans la colonne d'approbation
     * ou en sort, et que l'utilisateur n'est pas arbitre.
     *
     * @param Task                 $task   tache concernee
     * @param TaskboardColumn|null $cible  colonne visee ; null = pas de changement
     */
    protected function verifierTransitionApprobation(Task $task, ?TaskboardColumn $cible): void
    {
        if (is_null($cible)) {
            return;
        }

        $slug = $this->slugApprobation();

        $depart = optional($task->boardColumn)->slug;
        $arrivee = $cible->slug;

        // Rien a arbitrer : la colonne d'approbation n'est ni quittee ni rejointe.
        if ($depart !== $slug && $arrivee !== $slug) {
            return;
        }

        // Deplacer une tache a l'interieur de la meme colonne n'est pas une transition.
        if ($depart === $arrivee) {
            return;
        }

        abort_403(!$this->estArbitre($task));
    }

    /**
     * Le flux de demande d'approbation : ouvert aux personnes reellement
     * concernees par la tache, refuse a tout autre compte authentifie.
     */
    protected function verifierDemandeApprobation(Task $task): void
    {
        if ($this->estArbitre($task)) {
            return;
        }

        $assignes = $task->users->pluck('id')->toArray();

        abort_403(
            !(in_array(user()->id, $assignes) || $task->added_by == user()->id)
        );
    }

}
