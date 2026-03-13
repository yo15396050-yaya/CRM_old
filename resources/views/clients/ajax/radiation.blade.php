<div class="row" id="radiation_table">
    <div class="col-sm-12">
        <div class="content-wrapper">
            <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                @lang('Liste des clients en attente de radiations du CGA')
            </h4>
            <div class="d-flex flex-column w-tables rounded mt-3 bg-white table-responsive">
                <table class='table table-hover border-0 w-100' id="table-radiation">
                    <thead>
                        <tr>
                            <td align="left">#</td>
                            <td>Identifiant</td>
                            <td>Nom</td>
                            <td width="40%">Motif</td>
                            <td width="20%">Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($clientsAppor->isEmpty())
                            <tr><td colspan="5" align="center">Aucune données</td></tr>
                        @else
                            @foreach ($clientsAppor as $clientsRad)
                                <tr>
                                    <td align="left"><input type="checkbox" name="select_alField as $customField) {
                                        $data[] = [$customField->name => l_table" id="select-all-table" onclick="selectAllTable(this)"></td>
                                    <td>{{$clientsRad->user_id}}</td>
                                    <td><strong class="text-red">{{$clientsRad->company_name}}</strong></td>
                                    <td>{{$clientsRad->electronic_address_scheme}}</td>  
                                    <td>
                                        <div class="d-grid d-lg-flex d-md-flex action-bar">
                                            <div id="table-actions" class="flex-grow-1 align-items-center">

                                                <a class="btn btn-primary btn-radiation" href="javascript:;" data-user-id="{{$clientsRad->user_id}}">
                                                    <i class="fa fa-check mr-2"></i>
                                                    @lang('Valider')
                                                </a>

                                                <a class="btn btn-secondary btn-cancel-radiation" href="javascript:;" data-user-id="{{$clientsRad->user_id}}">
                                                    <i class="fa fa-times mr-2"></i>
                                                    @lang('Annuler')
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    $('body').on('click', '.btn-radiation', function() {
            const id = $(this).data('user-id');
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('app.approve')",
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
                    var url = "{{ route('clients.toggleRadiationValidate', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                showTable();
                            }
                        }
                    });
                }
            });
        });

    $('body').on('click', '.btn-cancel-radiation', function() {
        const id = $(this).data('user-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "cette action annule la radiation du client.",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('app.approve')",
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
                var url = "{{ route('clients.toggleRadiationCancel', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            showTable();
                        }
                    }
                });
            }
        });
    });
</script>