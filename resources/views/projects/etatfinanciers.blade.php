@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush
<style>
    .table {
        table-layout: fixed; /* Assure que les colonnes ont une largeur fixe */
        width: 100%; /* Optionnel : permet au tableau de prendre toute la largeur disponible */
    }

    .table th, .table td {
        width: auto; /* Définit la largeur de chaque colonne à 200 pixels */
        overflow: hidden; /* Cache le contenu qui dépasse */
        text-overflow: ellipsis; /* Ajoute des points de suspension pour le texte qui dépasse */
        white-space: nowrap; /* Empêche le retour à la ligne */
    }
    /* Appliquer à toutes les colonnes figées */
    .sticky-col {
        position: -webkit-sticky;
        position: sticky;
        background-color: white; /* Garde un fond pour éviter l'effet de transparence */
        z-index: 100; /* Priorité sur les autres colonnes */
    }
    
    /* Positionner chaque colonne figée */
    .sticky-col-1 {
        left: 0;
    }
    .sticky-col-2 {
        left: 50px; /* Ajuster en fonction de la largeur de la première colonne */
    }
    .sticky-col-3 {
        left: 70px; /* Ajuster en fonction de la largeur des deux premières colonnes */
    }
    
    /* Assurer que le conteneur permet le défilement */
    .table-container {
        overflow-x: auto;
        position: relative;
    }

</style>


@php
$addProjectPermission = user()->permission('add_projects');
$manageProjectTemplatePermission = user()->permission('manage_project_template');
$viewProjectTemplatePermission = user()->permission('view_project_template');
$deleteProjectPermission = user()->permission('delete_projects');
@endphp
@section('filter-section')

<x-filters.filter-box>
    <!-- CLIENT START -->
    <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.client')</p>
        <div class="select-status">
            <select class="form-control select-picker" name="client" id="client" data-live-search="true" data-size="8">
                <option value="all">@lang('app.all')</option>
                @foreach ($clients as $client)
                    <x-user-option :user="$client" />
                @endforeach
            </select>
        </div>
    </div>

    <!-- CLIENT END -->

    <!-- SEARCH BY TASK START -->
    <div class="task-search d-flex  py-1 px-lg-3 px-0 border-right-grey align-items-center">
        <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
            <div class="input-group bg-grey rounded">
                <div class="input-group-prepend">
                    <span class="input-group-text border-0 bg-additional-grey">
                        <i class="fa fa-search f-13 text-dark-grey"></i>
                    </span>
                </div>
                <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                    placeholder="@lang('app.startTyping')">
            </div>
        </form>
    </div>
    <!-- SEARCH BY TASK END -->

    <!-- RESET START -->
    <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
        <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
            @lang('app.clearFilters')
        </x-forms.button-secondary>
    </div>
    <!-- RESET END -->
</x-filters.filter-box>

@endsection

@section('content')
<div class="content-wrapper">
    <div class="d-flex flex-column w-tables rounded mt-3 bg-white table-responsive">
        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
    </div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endsection


@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    @include('sections.datatable_js')

    <script>
        const showTable = () => {
            window.LaravelDataTables["etatfinancier-table"].draw(false);
        }

        $('#search-text-field').on('keyup', function() {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            }
        });

        $('#reset-filters,#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('.show-unverified').removeClass("btn-active");
            $('.show-clients').addClass("btn-active");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $(document).ready(function() {
            // Appliquer la classe et la couleur en fonction de la sélection existante
            $('.change-status').each(function() {
                var selectedValue = $(this).val();
                updateSelectAppearance($(this), selectedValue);
            });

            // Écouter les changements de sélection    
            $(document).on("change", ".change-status", function() {
                var selectedValue = $(this).val();
                updateSelectAppearance($(this), selectedValue);
            });

            // Fonction pour mettre à jour l'apparence du select
            function updateSelectAppearance(selectElement, value) {
                // Réinitialiser les classes et la couleur
                selectElement.removeClass("border-info border-danger border-warning border-success");
                selectElement.css("color", ""); // Réinitialiser la couleur

                // Appliquer les styles en fonction de la valeur
                if (value == 0) {
                    selectElement.addClass("border-danger");
                    selectElement.css("color", "#d21010"); // Couleur pour "Besoin d'infos"
                } else if (value == 1) {
                    selectElement.addClass("border-danger");
                    selectElement.css("color", "#007bff"); // Couleur pour "Besoin d'infos"
                } else if (value == 3) {
                    selectElement.addClass("border-warning");
                    selectElement.css("color", "#f5c308"); // Couleur pour "En traitement"
                } else if (value == 2) {
                    selectElement.addClass("border-success");
                    selectElement.css("color", "#679c0d"); // Couleur pour "Validé"
                }
            }
        });

        $(document).on('show.bs.dropdown', '.table-responsive', function() {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('#etatfinancier-table').on('change', '.change-status', function() {
            var url = "{{ route('projects.change_status_etat') }}";
            var token = "{{ csrf_token() }}";
            var id = $(this).data('etatfinancier-id');
            var column = $(this).data('column');
            var status = $(this).val();

            // Créez un objet pour les statuts
            var statusData = {};
            statusData[column] = status; // Ajoutez le statut pour la colonne correspondante

            if (id != "" && status != "") {
                $.easyAjax({
                    url: url,
                    type: "POST",
                    container: '.content-wrapper',
                    blockUI: true,
                    data: {
                        '_token': token,
                        row_ids: id, // Envoyez l'ID pour mettre à jour l'enregistrement
                        status: statusData // Envoyez l'objet de statut
                    },
                    success: function(data) {
                        window.LaravelDataTables["etatfinancier-table"].draw(false);
                    }
                });
            }
        });
       
        $('body').on('click', '.etat-actif', function() {
            var userId = $(this).data('actif-id'); 
            var url = "{{ route('projects.etatFinanciers.apply', ':id') }}";
            url = url.replace(':id', userId);
            var token = "{{ csrf_token() }}";
            
            $.easyAjax({
                type: 'GET', 
                url: url,
                data: {
                    '_token': token,
                },
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire({
                            title: "Veillez renseigné le bon chiffre d'affaires.",
                            text: "",
                            icon: 'info',
                            html: `<div>
                                        <input type="number" name="chiffre" id="chiffre" placeholder="Entrez le chiffre d'affaires" class="form-control height-35 f-14" />
                                    </div>
                                    `,
                            showCancelButton: true,
                            focusConfirm: false,
                            confirmButtonText: "Valider !",
                            cancelButtonText: "@lang('app.cancel')",
                            customClass: { 
                                confirmButton: 'btn btn-primary mr-3',
                                cancelButton: 'btn btn-secondary'
                            },
                            showClass: {
                                popup: 'swal2-noanimation',
                                backdrop: 'swal2-noanimation'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var url = "{{ route('projects.change_status_etat') }}";
                                var chiffreValue = $('#chiffre').val();

                                $.easyAjax({
                                    type: 'POST',  
                                    url: url,
                                    data: {
                                        '_token': token,
                                        'row_ids': userId, // Passer l'ID pour la mise à jour
                                        'status': { 'chiffre': chiffreValue }
                                    },
                                    success: function(response) {
                                        if (response.status == "success") {
                                            showTable();
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            });
        });

        $('body').on('click', '.modif-actif', function() {
            var userId = $(this).data('modif-id'); 
            var userCA = $('input[name="chiffre[' + userId + ']"]').val(); // Récupérer la valeur de l'input
            var url = "{{ route('projects.etatFinanciers.apply', ':id') }}";
            url = url.replace(':id', userId);
            var token = "{{ csrf_token() }}";
            //alert(userCA);
            $.easyAjax({
                type: 'GET', 
                url: url,
                data: {
                    '_token': token,
                },
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire({
                            title: "Modifier le chiffre d'affaires.",
                            text: "",
                            icon: 'info',
                            html: `<div>
                                        <input type="number" name="chiffre" id="chiffre" value="`+ userCA +`" class="form-control height-35 f-14" />
                                    </div>
                                    `,
                            showCancelButton: true,
                            focusConfirm: false,
                            confirmButtonText: "Valider !",
                            cancelButtonText: "@lang('app.cancel')",
                            customClass: { 
                                confirmButton: 'btn btn-primary mr-3',
                                cancelButton: 'btn btn-secondary'
                            },
                            showClass: {
                                popup: 'swal2-noanimation',
                                backdrop: 'swal2-noanimation'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var url = "{{ route('projects.change_status_etat') }}";
                                var chiffreValue = $('#chiffre').val();

                                $.easyAjax({
                                    type: 'POST',  
                                    url: url,
                                    data: {
                                        '_token': token,
                                        'row_ids': userId, // Passer l'ID pour la mise à jour
                                        'status': { 'chiffre': chiffreValue }
                                    },
                                    success: function(response) {
                                        if (response.status == "success") {
                                            showTable();
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            });
        });
    
        $('body').on('click', '.etat-inactif', function() {
            var userId = $(this).data('inactif-id'); 
            //var userCA = $('input[name="chiffre[' + userId + ']"]').val(); // Récupérer la valeur de l'input
            var url = "{{ route('projects.etatFinanciers.apply', ':id') }}";
            url = url.replace(':id', userId);
            var token = "{{ csrf_token() }}";
            
            $.easyAjax({
                type: 'GET', 
                url: url,
                data: {
                    '_token': token,
                },
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire({
                            title: "Est vous sur ?",
                            text: "Suspendre le chiffire d'affaire.",
                            icon: 'warning',
                            showCancelButton: true,
                            focusConfirm: false,
                            confirmButtonText: "Valider !",
                            cancelButtonText: "@lang('app.cancel')",
                            customClass: { 
                                confirmButton: 'btn btn-primary mr-3',
                                cancelButton: 'btn btn-secondary'
                            },
                            showClass: {
                                popup: 'swal2-noanimation',
                                backdrop: 'swal2-noanimation'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var url = "{{ route('projects.change_status_etat') }}";
                                var chiffreValue = -1;

                                $.easyAjax({
                                    type: 'POST',  
                                    url: url,
                                    data: {
                                        '_token': token,
                                        'row_ids': userId, // Passer l'ID pour la mise à jour
                                        'status': { 'chiffre': chiffreValue }
                                    },
                                    success: function(response) {
                                        if (response.status == "success") {
                                            showTable();
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            });
        });
    </script>
@endpush