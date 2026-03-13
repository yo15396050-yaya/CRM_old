<?php

namespace App\Exports;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class InscriptionsExport implements FromCollection, WithHeadings
{
    protected $start_date;
    protected $end_date;

    public function __construct($start_date = null, $end_date = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $query = Registration::query();

        if ($this->start_date && $this->end_date) {
            $start = Carbon::parse($this->start_date)->startOfDay();
            $end = Carbon::parse($this->end_date)->endOfDay();
            $query->whereBetween('date_inscription', [$start, $end]);
        }

        return $query->orderBy('date_inscription', 'desc')->get([
            'ticket_number',
            'nom_complet',
            'email',
            'telephone',
            'montant',
            'statut',
            'date_inscription'
        ]);
    }

    public function headings(): array
    {
        return [
            'N° Ticket',
            'Nom complet',
            'Email',
            'Téléphone',
            'Montant',
            'Statut',
            'Date inscription'
        ];
    }
}
