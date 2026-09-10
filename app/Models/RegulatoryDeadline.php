<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Echeance reglementaire recurrente (fiscale ou sociale).
 *
 * Les lignes decrivent une regle — « le 15 de chaque mois » — et non une
 * date. Les occurrences sont calculees a la volee par prochaines(), ce qui
 * evite toute reprise annuelle du referentiel.
 *
 * Une ligne dont company_id est null vaut pour toutes les entreprises :
 * c'est le calendrier commun installe par la migration. Une entreprise
 * peut ajouter ses propres echeances en renseignant company_id.
 */
class RegulatoryDeadline extends Model
{
    protected $table = 'regulatory_deadlines';

    protected $fillable = [
        'company_id', 'label', 'detail',
        'day_of_month', 'frequency', 'months',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'day_of_month' => 'integer',
        'sort_order'   => 'integer',
    ];

    /**
     * Occurrences tombant dans les $jours prochains jours, les plus
     * proches d'abord. Une seule requete, puis un calcul en memoire :
     * le referentiel compte quelques lignes, jamais des milliers.
     *
     * @return array<int, object{date: Carbon, label: string, detail: ?string, urgent: bool}>
     */
    public static function prochaines(int $jours = 30, ?int $companyId = null): array
    {
        $regles = static::query()
            ->where('is_active', true)
            ->where(function ($q) use ($companyId) {
                $q->whereNull('company_id');

                if ($companyId) {
                    $q->orWhere('company_id', $companyId);
                }
            })
            ->orderBy('sort_order')
            ->get();

        $debut = Carbon::today();
        $fin   = $debut->copy()->addDays($jours);

        $occurrences = [];

        foreach ($regles as $regle) {
            foreach ($regle->occurrencesEntre($debut, $fin) as $date) {
                $occurrences[] = (object) [
                    'date'   => $date,
                    'label'  => $regle->label,
                    'detail' => $regle->detail,
                    // Une echeance a 7 jours ou moins est signalee comme
                    // urgente, comme les deux premieres lignes de la maquette.
                    'urgent' => $debut->diffInDays($date, false) <= 7,
                ];
            }
        }

        usort($occurrences, fn ($a, $b) => $a->date <=> $b->date);

        return $occurrences;
    }

    /**
     * Dates auxquelles cette regle tombe entre deux bornes incluses.
     *
     * @return Carbon[]
     */
    public function occurrencesEntre(Carbon $debut, Carbon $fin): array
    {
        $moisRetenus = $this->moisRetenus();
        $dates = [];

        $curseur = $debut->copy()->startOfMonth();

        while ($curseur->lessThanOrEqualTo($fin)) {
            if (is_null($moisRetenus) || in_array((int) $curseur->month, $moisRetenus, true)) {
                // Le 31 d'un mois de 30 jours retombe sur le dernier jour reel.
                $jour = min($this->day_of_month, $curseur->daysInMonth);
                $date = $curseur->copy()->day($jour);

                if ($date->betweenIncluded($debut, $fin)) {
                    $dates[] = $date;
                }
            }

            $curseur->addMonth();
        }

        return $dates;
    }

    /**
     * Mois concernes par la regle, ou null lorsqu'elle vaut tous les mois.
     *
     * @return int[]|null
     */
    private function moisRetenus(): ?array
    {
        if ($this->frequency === 'monthly' || blank($this->months)) {
            return null;
        }

        return array_map('intval', array_filter(explode(',', $this->months)));
    }
}
