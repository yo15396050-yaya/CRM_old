<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\Registration;
use App\Models\Formation;
use Yajra\DataTables\Html\Column;


class RegistrationDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('check', fn($row) => $this->checkBox($row))
            ->editColumn('montant', function ($row) {
                return number_format($row->montant, 0, ',', ' ') . ' FCFA';
            })
            ->editColumn('statut', function ($row) {
                return $row->statut == 'pending'
                    ? '<span class="badge bg-warning">En attente</span>'
                    : '<span class="badge bg-success">Payé</span>';
            })
            ->editColumn('date_inscription', function ($row) {
                return $row->date_inscription
                    ? $row->date_inscription->format('d/m/Y H:i')
                    : '';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('form.download', $row->ticket_number) . '" class="btn btn-sm btn-primary">PDF</a>';
            })
            ->rawColumns(['check', 'statut', 'action']); // Garder seulement celles qui contiennent du HTML
    }

    public function query()
    {
        return Registration::query();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('inscriptions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(6, 'desc');
    }

    protected function getColumns()
    {
        return [
            ['data' => 'check', 'name' => 'check', 'title' => '<input type="checkbox" id="select-all-table" onclick="selectAllTable(this)">', 'orderable' => false, 'searchable' => false],
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'ticket_number', 'name' => 'ticket_number', 'title' => 'N° Ticket'],
            ['data' => 'nom_complet', 'name' => 'nom_complet', 'title' => 'Nom complet'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'telephone', 'name' => 'telephone', 'title' => 'Téléphone'],
            ['data' => 'montant', 'name' => 'montant', 'title' => 'Montant'],
            ['data' => 'statut', 'name' => 'statut', 'title' => 'Statut'],
            ['data' => 'date_inscription', 'name' => 'date_inscription', 'title' => 'Date inscription'],
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)
        ];
    }
}
