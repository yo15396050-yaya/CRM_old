<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

/**
 * Lecture du fichier d'import des taches.
 *
 * A la difference des autres imports du projet, les colonnes ne sont
 * pas mises en correspondance par l'utilisateur : le modele officiel
 * fixe leur ordre et leur intitule. Cela permet une validation ligne a
 * ligne precise, que l'assistant de correspondance ne permettait pas.
 */
class TaskImport implements ToArray
{

    /* Plafond par fichier : l'import est synchrone, on ne veut
       pas bloquer la requete indefiniment. La constante vit ici
       et non dans le trait : PHP interdit d'acceder a une
       constante de trait depuis l'exterieur. */
    public const MAXIMUM_LIGNES = 500;

    /**
     * Colonnes du modele, dans l'ordre exact du fichier.
     *
     * « cle » sert au code, « nom » figure dans l'en-tete du modele.
     */
    public static function colonnes(): array
    {
        return [
            ['cle' => 'heading',     'nom' => 'Tâche',         'requis' => true],
            ['cle' => 'project',     'nom' => 'Projet',        'requis' => false],
            ['cle' => 'start_date',  'nom' => 'Date de début', 'requis' => true],
            ['cle' => 'due_date',    'nom' => 'Date de fin',   'requis' => false],
            ['cle' => 'status',      'nom' => 'Statut',        'requis' => false],
            ['cle' => 'assigned',    'nom' => 'Assigné à',     'requis' => false],
            ['cle' => 'category',    'nom' => 'Catégorie',     'requis' => false],
            ['cle' => 'description', 'nom' => 'Description',   'requis' => false],
        ];
    }

    /** Intitules attendus dans la premiere ligne du fichier. */
    public static function entetes(): array
    {
        return array_column(self::colonnes(), 'nom');
    }

    public function array(array $rows): array
    {
        return $rows;
    }

}
