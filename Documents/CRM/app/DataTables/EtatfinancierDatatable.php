<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\Project;
use App\Models\CustomField;
use App\Models\EtatFinancier;
use App\Models\CustomFieldGroup;
use App\Models\GlobalSetting;
use App\Models\ProjectStatusSetting;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;

class EtatfinancierDatatable extends BaseDataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);
        
        // Ajoutez une case à cocher pour chaque ligne
        $datatables->addColumn('check', fn($row) => $this->checkBox($row));
        
        // Column for actions
        $datatables->addColumn('action', function ($row) {
            return $this->getActionColumn($row);
        });  
        
        // Éditez la colonne `name` pour qu'elle soit un lien vers le détail
        $datatables->editColumn('name', function ($row) {
            return '<a href="' . route('projects.etatFinanciers.show', [$row->id]) . '" class="openRightModal text-darkest-grey">' . $row->company_name . '</a>';
        });

        $datatables->addColumn('chiffre', function ($row) {
            // Formater le chiffre
            $formattedChiffre = number_format($row->chiffre, 0, '.', ' ') . ' FCFA';
        
            // Commencer la chaîne de retour
            $output = '
                <div class="d-flex">';
                if ($row->chiffre >= 0) {
                    $output .= '
                        <span class="badge badge-light p-2 f-14 mr-3" style="background-color:#000;">
                            <strong style="color:yellow;">' . ($formattedChiffre ?? '') . '</strong>
                        </span>';
                }else{
                    $output .= '
                    <span class="badge badge-light p-2 f-14 mr-3" style="background-color:red;">
                        <strong style="color:white;">Suspendu</strong>
                    </span>';
                }
                 $output .= '
                    <input type="hidden" name="chiffre[' . $row->id . ']" value="' . ($row->chiffre ?? '') . '" class="form-control height-35 f-14" />
                    <div class="task_view">
                        <div class="dropdown">
                            <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" 
                               type="link" id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" 
                               aria-haspopup="true" aria-expanded="false">
                                <i class="icon-options-vertical icons"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" 
                                 aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';
        
                    // Condition pour afficher "Modifier" ou "Ajouter"
                    if ($row->chiffre > 0) {
                        $output .= '<a class="dropdown-item modif-actif" href="javascript:;" data-modif-id="' . $row->id . '">
                                        <i class="fa fa-plus mr-2"></i>
                                        Modifier
                                    </a>';
                    } else {
                        $output .= '<a class="dropdown-item etat-actif" href="javascript:;" data-actif-id="' . $row->id . '">
                                        <i class="fa fa-plus mr-2"></i>
                                        Ajouter
                                    </a>';
                    }
                
                    // Ajout de l'option "Suspendre"
                    if ($row->chiffre > 0) {
                        $output .= '<a class="dropdown-item etat-inactif" href="javascript:;" data-inactif-id="' . $row->id . '">
                                        <i class="fa fa-times mr-2"></i>
                                        Suspendre
                                    </a>';
                    } else {
                        $output .= '';
                    } 
                    // Fermer les balises
                    $output .= '
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
        
            // Retourner le contenu final
            return $output;
        });
        // Liste des colonnes
        $columns = ['etat','info', 'etat_301', 'etat_302', 'tee_rme', 'balance', 'bilan', 'pv', 'rapport', 'facture', 'valid_client', 'visa', 'depot_ligne', 'depot_physique', 'said'];
    
        foreach ($columns as $column) {
            $datatables->editColumn($column, function ($row) use ($column) {
                // Appel à une méthode pour obtenir le statut spécifique
                return $this->getStatusLabel($row, $column);
            });
        }
    
        $customFieldColumns = CustomField::customFieldData($datatables, EtatFinancier::CUSTOM_FIELD_MODEL);
        $datatables->rawColumns(array_merge(['action', 'name', 'chiffre', 'check'], $columns, $customFieldColumns));
    
        $datatables->addIndexColumn();
        $datatables->smart(false);
        $datatables->setRowId(fn($row) => 'row-' . $row->id);
    
        return $datatables;
    }
    
    // Méthode pour générer le dropdown
    protected function getStatusLabel($row, $column)
    {
        // Définition des couleurs pour chaque statut
        $colors = [
            '#0078ff',
            '#d21010', // Besoin d'infos et Non traité
            '#679c0d', // En traitement et Infos non transmises
            '#f5c308', // Validé et Infos disponibles
        ];

        $statusClasses = [
            0 => 'border-info', // Besoin d'infos et Non traité
            1 => 'border-danger', // En traitement et Infos non transmises
            2 => 'border-success', // Validé et Infos disponibles
            3 => 'border-warning' // Validé et Infos disponibles
        ];
    
        // Labels des statuts
        $statusLabels = [
            'etat' => [
                0 => 'Applicable',
                1 => 'Non-applicable',
            ],
            'info' => [
                0 => 'N/A',
                1 => 'Besoin d\'infos',
                2 => 'Infos disponibles',
                3 => 'Infos non transmises'
            ],
            'etat_301' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'etat_302' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'tee_rme' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'balance' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'bilan' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'pv' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'rapport' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'facture' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'valid_client' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'visa' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'depot_ligne' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'depot_physique' => [
                1 => 'Non traité',
                0 => 'En traitement',
                2 => 'Validé',
                3 => 'N/A'
            ],
            'said' => [
                0 => 'II Plateaux III',
                1 => 'II Plateaux Djibi ',
                2 => 'II Pateaux I ',
                3 => 'II Pateaux II ',
                4 => 'Anyama ',
                5 => 'Alepé',
                6 => 'Abobo II',
                7 => 'Abobo III',
                8 => 'Adjamé I',
                9 => 'Adjamé II',
                10 => 'Attecoubé',
                11 => 'Adjamé III',
                12 => 'Cocody',
                13 => 'Williamsvile',
                14 => 'Plateau I',
                15 => 'Plateau II',
                16 => 'Yopougon I',
                17 => 'Yopougon III',
                18 => 'Yopougon III',
                19 => 'Yopougon V',
                20 => 'Yopougon IV',
                21 => 'Bingerville',
                22 => 'Riviera I',
                23 => 'Riviera II',
                24 => 'Port-Bouet',
                25 => 'Treichville I',
                26 => 'Treichville II',
                27 => 'Bietry',
                28 => 'Koumassi I',
                29 => 'Koumassi II',
                30 => 'Marcory I',
                31 => 'Marcory II',
                32 => 'Zone IV',
                33 => 'Abengourou',
                34 => 'Agnibilekro',
                35 => 'Betié',
                36 => 'Niablé',
                37 => 'Aboisso',
                38 => 'Adiaké',
                39 => 'Bonoua',
                40 => 'Grand Bassam',
                41 => 'Tiapoum',
                42 => 'Adzopé',
                43 => 'Agboville',
                44 => 'Akoupé',
                45 => 'Taabo',
                46 => 'Tiassalé',
                47 => 'Yakassé',
                48 => 'Bondoukou',
                49 => 'Doropo',
                50 => 'Koun Fao',
                51 => 'Kouassi-Datékro',
                52 => 'Nassian',
                53 => 'Tanda',
                54 => 'Bouaké I',
                55 => 'Dabakala',
                56 => 'katiola',
                57 => 'M’Bahiakro',
                58 => 'Niakara',
                59 => 'Bouaké I',
                60 => 'Bouaké II',
                61 => 'béoumi',
                62 => 'Sakassou',
                63 => 'Dabou',
                64 => 'Grand Lahou',
                65 => 'Jacqueville',
                66 => 'Sikensi',
                67 => 'Songon',
                68 => 'Daloa I',
                69 => 'Daloa II',
                70 => 'Issia',
                71 => 'Mankono',
                72 => 'Seguela',
                73 => 'Vavoua',
                74 => 'Arrah',
                75 => 'Bocanda',
                76 => 'Bongouanou',
                77 => 'Dimbokro',
                78 => 'Daoukro',
                79 => 'M’Batto',
                80 => 'Divo',
                81 => 'Gagnoa',
                82 => 'Oumé',
                83 => 'Guiglo',
                84 => 'Boundiali',
                85 => 'Dikodougou',
                86 => 'Ferkessedougou',
                87 => 'kong',
                88 => 'Korhogo',
                89 => 'M’Bengue',
                90 => 'Ouangolodougou',
                91 => 'Tengrela',
                92 => 'Odienne',
                93 => 'Touba',
                94 => 'Man',
                95 => 'Danane',
                96 => 'Bangolo',
                97 => 'Fresco',
                98 => 'San pedro I',
                99 => 'San pedro II',
                100 => 'Tabou',
                101 => 'Soubre',
                102 => 'Bouafle',
                103 => 'Tiebissou',
                104 => 'Toumodi',
                105 => 'Youmoussoukro',
                106 => 'Zeunoula',
            ],
        ];

        // Déterminer la classe par défaut basée sur la valeur actuelle
        $currentValue = $row->$column;
        $currentClass = isset($statusClasses[$currentValue]) ? $statusClasses[$currentValue] : '';
    
        // Création du select avec la classe par défaut
        $status = '<select class="dropdown form-control height-35 change-status ' . $currentClass . '" data-etatfinancier-id="' . $row->id . '" data-column="' . $column . '" >';

        // Parcours des statuts pour générer les options
        foreach ($statusLabels[$column] as $key => $label) {
            $colorStyle = 'color: ' . ($key >= 0 && $key <= 3 ? $colors[$key] : $colors[3]);
            
            $status .= '<option value="' . $key . '" style="' . $colorStyle . '" ';
            //$status .= '<option value="' . $key . '" ';
            if ($key == $currentValue) {
                $status .= 'selected';
            }
            $status .= '><span><i class="fa fa-circle" aria-hidden="true"></i><span> ' . $label . '</option>';
        }

        $status .= '</select>';

        return $status;
    }
    
    // Méthode pour les actions
    protected function getActionColumn($row)
    {
        return '<div class="task_view">
            <a href="' . route('projects.etatFinanciers.show', [$row->id]) . '" class="taskView openRightModal text-darkest-grey f-w-500">' . __('app.view') . '</a>
            </div>';
    }

    /**
     * @param EtatFinancier $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(EtatFinancier $model)
    {
        $request = $this->request();
    
        // Sélectionner les colonnes pertinentes de la table etatfinancier
        $model = $model->select('etatfinanciers.id', 'etatfinanciers.chiffre', 'etatfinanciers.user_id', 'etatfinanciers.etat', 'etatfinanciers.info', 'etatfinanciers.etat_301', 'etatfinanciers.etat_302', 'etatfinanciers.tee_rme', 'etatfinanciers.balance', 'etatfinanciers.bilan', 'etatfinanciers.pv', 'etatfinanciers.rapport', 'etatfinanciers.facture', 'etatfinanciers.valid_client', 
        'etatfinanciers.visa', 'etatfinanciers.depot_ligne', 'etatfinanciers.depot_physique', 'etatfinanciers.said','client_details.company_name')
            ->join('users', 'etatfinanciers.user_id', '=', 'users.id') // Jointure avec la table users
            ->join('client_details', 'users.id', '=', 'client_details.user_id'); // Jointure avec client_details
    
        // Appliquer les filtres basés sur la requête
        if ($request->searchText != '') {
            $model->where(function ($query) { 
                $query->where('client_details.company_name', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.chiffre', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.etat', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.info', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.etat_301', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.etat_302', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.tee_rme', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.balance', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.bilan', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.pv', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.rapport', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.facture', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.valid_client', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.visa', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.depot_ligne', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.depot_physique', 'like', '%' .request('searchText') . '%')
                    ->orWhere('etatfinanciers.said', 'like', '%' .request('searchText') . '%');
            });
        }
        /* Filtrer par l'utilisateur si nécessaire
        if (user()->permission('view_etatfinancier') == 'added') {
            $model->where('etatfinancier.user_id', user()->id);
        }*/
    
        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $dataTable = $this->setBuilder('etatfinancier-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["etatfinancier-table"].buttons().container()
                    .appendTo( "#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
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
       
            $data = [
                'check' => [
                    'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                    'exportable' => false,  
                    'orderable' => false,
                    'searchable' => false,
                    'width' => '50px',
                    'className' => 'sticky-col sticky-col-1', // Première colonne figée
                ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#', 'width' => '20px', 'className' => 'sticky-col sticky-col-2'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id'), 'visible' => false, 'width' => '30px'],
            __('app.company_name') => ['data' => 'company_name', 'name' => 'company_name', 'title' => __('Nom'), 'visible' => true, 'width' => '200px','className' => 'sticky-col sticky-col-2'],
            __('app.chiiffre') => ['data' => 'chiffre', 'name' => 'chiffre', 'title' => __('Chiffire d\'affaire'),'width' => '150px'],
            __('app.etat') => ['data' => 'etat', 'name' => 'etat', 'title' => __('Etat financiers'),'width' => '150px'],
            __('app.info') => ['data' => 'info', 'name' => 'info', 'title' => __('Informations'),'width' => '150px'],
            __('app.etat_301') => ['data' => 'etat_301', 'name' => 'etat_301', 'title' => __('Etat 301'),'width' => '100px'],
            __('app.etat_302') => ['data' => 'etat_302', 'name' => 'etat_302', 'title' => __('Etat 302'),'width' => '100px'],
            __('app.tee_rme') => ['data' => 'tee_rme', 'name' => 'tee_rme', 'title' => __('TEE/RME'),'width' => '100px'],
            __('app.balance') => ['data' => 'balance', 'name' => 'balance', 'title' => __('Balance'),'width' => '100px'],
            __('app.bilan') => ['data' => 'bilan', 'name' => 'bilan', 'title' => __('Bilan'),'width' => '100px'],
            __('app.pv') => ['data' => 'pv', 'name' => 'pv', 'title' => __('Pv'),'width' => '100px'],
            __('app.rapport') => ['data' => 'rapport', 'name' => 'rapport', 'title' => __('Rapport'),'width' => '100px'],
            __('app.facture') => ['data' => 'facture', 'name' => 'facture', 'title' => __('Facture'),'width' => '100px'],
            __('app.valid_client') => ['data' => 'valid_client', 'name' => 'valid_client', 'title' => __('Validation Client'),'width' => '100px'],
            __('app.visa') => ['data' => 'visa', 'name' => 'visa', 'title' => __('Visa'),'width' => '100px'],
            __('app.depot_ligne') => ['data' => 'depot_ligne', 'name' => 'depot_ligne', 'title' => __('Dépot Ligne'),'width' => '100px'],
            __('app.depot_physique') => ['data' => 'depot_physique', 'name' => 'depot_physique', 'title' => __('Dépot Physique'),'width' => '100px'],
            __('app.said') => ['data' => 'said', 'name' => 'said', 'title' => __('Said'),'width' => '100px'],
        ];
   
        $action = [
            Column::computed('action', __('app.action'))
                ->exportable(true)
                ->printable(true)
                ->orderable(true)
                ->searchable(false)
                ->addClass('text-right pr-20')
                ->width('50px')
        ];

        return array_merge($data, CustomFieldGroup::customFieldsDataMerge(new EtatFinancier()), $action);
    }

}
