<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export Excel du rapport hebdomadaire.
 *
 * Ne calcule rien : le rapport est deja construit par le trait
 * RapportHebdomadaire, cette classe ne fait que le mettre en colonnes.
 * Dupliquer les requetes ici ferait diverger l'export de l'ecran.
 */
class RapportHebdomadaireExport implements FromCollection, WithHeadings, WithStyles
{

    private object $rapport;

    public function __construct(object $rapport)
    {
        $this->rapport = $rapport;
    }

    public function headings(): array
    {
        return [
            'Référence', 'Tâche', 'Client ou diligence', 'Statut', 'En retard',
            'Priorité', 'Début', 'Échéance', 'Réalisée le', 'Délai (jours)',
            'Durée prévue', 'Temps pointé (h)', 'Collaborateurs', 'Dernière mise à jour',
        ];
    }

    public function collection()
    {
        $priorites = ['high' => 'Haute', 'medium' => 'Moyenne', 'low' => 'Basse'];

        return $this->rapport->taches->map(fn($t) => [
            $t->reference ?: '',
            $t->titre,
            $t->client ?: ($t->diligence ?: ''),
            $t->statut,
            $t->retard ? 'oui' : 'non',
            $priorites[$t->priorite] ?? $t->priorite,
            $t->debut?->format('d/m/Y') ?: '',
            $t->echeance?->format('d/m/Y') ?: '',
            $t->faitLe?->format('d/m/Y') ?: '',
            $t->delai ?? '',
            $t->prevu ?: '',
            $t->pointe ?? '',
            /* Tous les collaborateurs, jamais le premier seulement. */
            $t->gens->pluck('nom')->implode(', '),
            $t->maj->format('d/m/Y'),
        ]);
    }

    public function styles(Worksheet $feuille)
    {
        return [1 => ['font' => ['bold' => true]]];
    }

}
