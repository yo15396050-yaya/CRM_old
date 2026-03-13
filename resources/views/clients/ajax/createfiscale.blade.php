<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="row">
    <div class="col-sm-12">
        <form id="save-client-data-form">
            @csrf
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">@lang('Détails du paiement')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" id="client_list_ids">
                                    <x-client-selection-dropdown :clients="$clients" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-forms.label fieldId="regime" class="f-14 text-dark-grey mb-12"
                                        :fieldLabel="__('Régime fiscal')" fieldRequired="true">
                                    </x-forms.label>
                                    <select id="regime" name="regime" class="dropdown form-control height-35 f-14 select-picker" data-live-search="true">
                                        <option value="">--</option>
                                        <option value="TEE">TEE</option>
                                        <option value="RME">RME</option>
                                        <option value="RSI">RSI</option>
                                        <option value="RNI">RNI</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-forms.label class="f-14 text-dark-grey mb-12" fieldId="periode"
                                        :fieldLabel="__('Période')" fieldRequired="true">
                                    </x-forms.label>
                                    <x-forms.input-group>
                                        <select id="periode" name="periode" class="dropdown form-control height-35 f-14" data-live-search="true">
                                            <option value="">--</option>
                                            <option value="Mensuel">Mensuel</option>
                                            <option value="Trimestriel">Trimestriel</option>
                                            <option value="Semestriel">Semestriel</option>
                                            <option value="Annuel">Annuel</option>
                                        </select>
                                    </x-forms.input-group>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-forms.label class="f-14 text-dark-grey mb-12" fieldId="status"
                                        :fieldLabel="__('Statut')" fieldRequired="true">
                                    </x-forms.label>
                                    <x-forms.input-group>
                                        <select id="status" name="status" class="dropdown form-control height-35 f-14" data-live-search="true">
                                            <option value="">--</option>
                                            <option value="p-paid">Paiement partiel</option>
                                            <option value="paid">Payé</option>
                                            <option value="unpaid">Non payé</option>
                                        </select>
                                    </x-forms.input-group>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <x-forms.text fieldId="type_impot" class="mb-2 mt3 mt-lg-0 mt-md-0" :fieldLabel="__('Libéllé impôt')" fieldName="type_impot"
                                    fieldRequired="true" :fieldPlaceholder="__('Libéllé impôt')"></x-forms.text>
                            </div>
                            <div class="col-md-4">
                                <x-forms.number fieldId="montant" class="mb-2 mt3 mt-lg-0 mt-md-0" :fieldLabel="__('Montant')" fieldName="montant"
                                    fieldRequired="true" :fieldPlaceholder="__('Montant')"></x-forms.number>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-forms.label class="f-14 text-dark-grey mb-12" fieldId="date_paiement"
                                        :fieldLabel="__('Date paiement')">
                                    </x-forms.label>
                                    <input type="date" id="date_paiement" name="date_paiement" class="form-control height-35 f-14"
                                        placeholder="{{ __('placeholders.date') }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp pdf docx" class="mr-0 mr-lg-2 mr-md-2 cropper"
                                :fieldLabel="__('Ajouter un fichier')" fieldName="file" fieldId="file"
                                fieldHeight="119" :popover="__('Joindre un fichier')" />
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-md-12">
                    <div class="form-group my-3">
                        <button type="submit" id="save-client-form" class="btn-primary mr-3 mb-3 mt-3 mt-lg-0 mt-md-0">Soumettre</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if (function_exists('sms_setting') && sms_setting()->telegram_status)
    <script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        function saveClient(data, url, buttonSelector, token) { // Ajoutez le paramètre token
            $.easyAjax({
                url: url,
                container: '#save-client-data-form',
                type: "POST", // Assurez-vous que le type est bien POST
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                file: true,
                data: {
                    '_token': token, // Inclure le token CSRF
                    ...data // Utilisez l'opérateur de décomposition
                },
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    } else {
                        alert(response.message || 'Une erreur est survenue.');
                    }
                },
                error: function(xhr) {
                    alert('Une erreur est survenue : ' + xhr.responseText);
                }
            });
        }

        $('#save-client-form').click(function(event) {
            event.preventDefault();
            const url = "{{ route('clients.storeFiscale') }}";
            var data = $('#save-client-data-form').serializeArray(); // Récupérez les données sous forme de tableau d'objets
            var token = "{{ csrf_token() }}"; // Récupérez le token CSRF

            // Convertir le tableau d'objets en un objet
            var dataObject = {};
            data.forEach(function(item) {
                dataObject[item.name] = item.value;
            });

            saveClient(dataObject, url, "#save-client-form", token);
        });
    });

    @if (function_exists('sms_setting') && sms_setting()->telegram_status)
        var clipboard = new ClipboardJS('.btn-copy');

        clipboard.on('success', function(e) {
            Swal.fire({
                icon: 'success',
                text: '@lang("app.urlCopied")',
                toast: true,
                position: 'top-end',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    confirmButton: 'btn btn-primary',
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
            })
        });
    @endif
</script>