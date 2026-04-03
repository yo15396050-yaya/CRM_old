<?php

namespace App\DataTables;

use App\Models\Entreprise;
use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Carbon\Carbon;

class EntreprisesDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">
                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">
                            <a href="' . route('clients.show_entreprise', [$row->id]) . '" class="dropdown-item openRightModal"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>
                            <a href="javascript:;" class="dropdown-item delete-table-row" data-entreprise-id="' . $row->id . '"><i class="fa fa-trash mr-2"></i>' . __('app.delete') . '</a>
                        </div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('client_name', function ($row) {
                return $row->user ? $row->user->name : 'N/A';
            })
            ->editColumn('statut', function ($row) {
                $status = $row->statut;
                $class = 'badge badge-info';
                $text = __('app.' . $status);

                switch ($status) {
                    case 'validee':
                        $class = 'badge badge-success';
                        break;
                    case 'rejetee':
                        $class = 'badge badge-danger';
                        break;
                    case 'en_cours':
                        $class = 'badge badge-warning';
                        break;
                    case 'en_attente':
                    default:
                        $class = 'badge badge-secondary';
                        break;
                }

                return '<span class="' . $class . '">' . $text . '</span>';
            })
            ->editColumn('date_demande', function ($row) {
                return $row->date_demande ? Carbon::parse($row->date_demande)->translatedFormat($this->company->date_format) : '--';
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat($this->company->date_format);
            })
            ->rawColumns(['action', 'statut']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Entreprise $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Entreprise $model)
    {
        return $model->newQuery()->with('user');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $dataTable = $this->setBuilder('entreprises-table', 0)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["entreprises-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
            ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
        }

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('DT_RowIndex')
                ->title('#')
                ->orderable(false)
                ->searchable(false),
            Column::make('denomination_sociale')
                ->title(__('Dénomination Sociale')),
            Column::make('client_name')
                ->title(__('app.client'))
                ->name('user.name')
                ->data('client_name'),
            Column::make('forme_juridique')
                ->title(__('Forme Juridique')),
            Column::make('capital_social')
                ->title(__('Capital Social')),
            Column::make('objet_social')
                ->title(__('Secteur/Objet')),
            Column::make('ville')
                ->title(__('Ville')),
            Column::make('telephone')
                ->title(__('app.phone')),
            Column::make('email')
                ->title(__('app.email')),
            Column::make('statut')
                ->title(__('app.status')),
            Column::make('date_demande')
                ->title(__('Date Demande')),
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }
}
